<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionMethod;

trait HasSmartScopes
{
    /**
     * Cache duration para metadatos de la tabla (en segundos)
     */
    protected int $schemaCacheDuration = 3600;

    /**
     * Rastrear joins aplicados para evitar duplicados
     */
    protected array $appliedJoins = [];

    /**
     * Operadores permitidos para filtros avanzados
     */
    protected array $allowedFilterOperators = [
        'eq' => '=',           // igual
        'ne' => '!=',          // no igual
        'gt' => '>',           // mayor que
        'gte' => '>=',         // mayor o igual
        'lt' => '<',           // menor que
        'lte' => '<=',         // menor o igual
        'like' => 'LIKE',      // contiene
        'not_like' => 'NOT LIKE',
        'in' => 'IN',          // en lista
        'not_in' => 'NOT IN',  // no en lista
        'between' => 'BETWEEN', // entre valores
        'null' => 'NULL',      // es nulo
        'not_null' => 'NOT NULL', // no es nulo
        'starts' => 'STARTS',  // comienza con
        'ends' => 'ENDS',      // termina con
    ];

    /**
     * Scope para cargar relaciones dinámicamente con validación y conteos
     *
     * Ejemplos:
     * ?included=author,comments.user
     * ?included=posts:id,title|comments:limit(5)
     */
    public function scopeIncluded(Builder $query): void
    {
        $included = request('included');
        if (! $included) {
            return;
        }

        $relations = explode(',', $included);
        $valid = [];
        $withCount = [];

        foreach ($relations as $relationPath) {
            $relationPath = trim($relationPath);

            // Detectar si es un count (author_count)
            if (str_ends_with($relationPath, '_count')) {
                $relationName = substr($relationPath, 0, -6);
                if ($this->isValidRelation($this, $relationName)) {
                    $withCount[] = $relationName;
                }

                continue;
            }

            // Procesar relación con posibles constraints
            [$relation, $constraints] = $this->parseRelationConstraints($relationPath);

            if ($this->isValidNestedRelation($this, explode('.', $relation))) {
                if ($constraints) {
                    $valid[$relation] = $constraints;
                } else {
                    $valid[] = $relation;
                }
            }
        }

        if (! empty($valid)) {
            $query->with($valid);
        }

        if (! empty($withCount)) {
            $query->withCount($withCount);
        }
    }

    /**
     * Scope para filtros avanzados con múltiples operadores
     *
     * Ejemplos:
     * ?filter[name]=Juan                          (LIKE %Juan%)
     * ?filter[age][gte]=18                        (age >= 18)
     * ?filter[status][in]=active,pending          (status IN ('active','pending'))
     * ?filter[created_at][between]=2024-01-01,2024-12-31
     * ?filter[email][null]=true                   (email IS NULL)
     * ?filter[name][starts]=Dr.                   (name LIKE 'Dr.%')
     */
    public function scopeFilter(Builder $query): void
    {
        $filters = request('filter');
        if (! $filters || ! is_array($filters)) {
            return;
        }

        $columns = $this->getCachedTableColumns();
        $table = $this->getTable();

        foreach ($filters as $column => $value) {
            // Filtro simple: ?filter[name]=value
            if (! is_array($value)) {
                if (in_array($column, $columns)) {
                    $query->where($table.'.'.$column, 'LIKE', "%{$value}%");
                }

                continue;
            }

            // Filtros avanzados: ?filter[column][operator]=value
            foreach ($value as $operator => $operatorValue) {
                if (! in_array($column, $columns)) {
                    continue;
                }

                $this->applyFilterOperator($query, $column, $operator, $operatorValue);
            }
        }
    }

    /**
     * Scope para ordenamiento múltiple con validación
     *
     * Ejemplos:
     * ?sort=name                    (ASC por defecto)
     * ?sort=-created_at             (DESC)
     * ?sort=status,-created_at      (múltiple ordenamiento)
     * ?sort=author.name             (ordenar por relación)
     * ?sort=country.region.name     (relaciones anidadas)
     */
    public function scopeSort(Builder $query): void
    {
        $sort = request('sort');
        if (! $sort) {
            // Ordenamiento por defecto si no se especifica
            if (property_exists($this, 'defaultSort')) {
                $sort = $this->defaultSort;
            } else {
                return;
            }
        }

        $columns = $this->getCachedTableColumns();
        $sorts = explode(',', $sort);

        // Resetear joins aplicados para este query
        $this->appliedJoins = [];

        foreach ($sorts as $field) {
            $field = trim($field);
            $direction = str_starts_with($field, '-') ? 'desc' : 'asc';
            $column = ltrim($field, '-');

            // Ordenamiento por relación (tabla.columna o relación anidada)
            if (str_contains($column, '.')) {
                $this->applySortByRelation($query, $column, $direction);

                continue;
            }

            // Ordenamiento por columna simple
            if (in_array($column, $columns)) {
                $query->orderBy($this->getTable().'.'.$column, $direction);
            }
        }
    }

    /**
     * Scope para paginación inteligente con límites
     *
     * Ejemplos:
     * ?perPage=15
     * ?page=2&perPage=20
     */
    public function scopeGetOrPaginate(Builder $query)
    {
        $perPage = intval(request('perPage', 0));
        $maxPerPage = property_exists($this, 'maxPerPage') ? $this->maxPerPage : 100;

        // Limitar perPage al máximo permitido
        if ($perPage > $maxPerPage) {
            $perPage = $maxPerPage;
        }

        return $perPage > 0
            ? $query->paginate($perPage)->appends(request()->query())
            : $query->get();
    }

    /**
     * Scope para búsqueda global en múltiples campos
     *
     * Ejemplo:
     * ?search=juan
     */
    public function scopeSearch(Builder $query, ?string $term = null): void
    {
        $search = $term ?? request('search');

        if (! $search) {
            return;
        }

        $searchableColumns = property_exists($this, 'searchable')
            ? $this->searchable
            : $this->getCachedTableColumns();

        $table = $this->getTable();

        $query->where(function ($q) use ($search, $searchableColumns, $table) {
            foreach ($searchableColumns as $column) {
                $q->orWhere($table.'.'.$column, 'LIKE', "%{$search}%");
            }
        });
    }

    /**
     * Scope para seleccionar campos específicos (sparse fieldsets)
     *
     * Ejemplo:
     * ?fields=id,name,email
     */
    public function scopeFields(Builder $query): void
    {
        $fields = request('fields');

        if (! $fields) {
            return;
        }

        $columns = $this->getCachedTableColumns();
        $requestedFields = explode(',', $fields);
        $validFields = array_intersect($requestedFields, $columns);

        // Siempre incluir la clave primaria
        $primaryKey = $this->getKeyName();
        if (! in_array($primaryKey, $validFields)) {
            $validFields[] = $primaryKey;
        }

        if (! empty($validFields)) {
            // ✅ Prefijar todas las columnas con el nombre de la tabla
            $table = $this->getTable();
            $qualifiedFields = array_map(fn ($field) => "$table.$field", $validFields);

            $query->select($qualifiedFields);
        }
    }

    /**
     * Scope para filtros de fecha inteligentes
     *
     * Ejemplo:
     * ?date[created_at][from]=2024-01-01
     * ?date[created_at][to]=2024-12-31
     * ?date[created_at][today]=true
     * ?date[created_at][last_days]=7
     */
    public function scopeDateFilter(Builder $query): void
    {
        $dateFilters = request('date');

        if (! $dateFilters || ! is_array($dateFilters)) {
            return;
        }

        $columns = $this->getCachedTableColumns();
        $table = $this->getTable();

        foreach ($dateFilters as $column => $filters) {
            if (! in_array($column, $columns) || ! is_array($filters)) {
                continue;
            }

            $qualifiedColumn = $table.'.'.$column;

            foreach ($filters as $type => $value) {
                match ($type) {
                    'from' => $query->whereDate($qualifiedColumn, '>=', $value),
                    'to' => $query->whereDate($qualifiedColumn, '<=', $value),
                    'today' => $value ? $query->whereDate($qualifiedColumn, today()) : null,
                    'yesterday' => $value ? $query->whereDate($qualifiedColumn, today()->subDay()) : null,
                    'last_days' => $query->whereDate($qualifiedColumn, '>=', today()->subDays((int) $value)),
                    'this_month' => $value ? $query->whereMonth($qualifiedColumn, now()->month)
                        ->whereYear($qualifiedColumn, now()->year) : null,
                    'last_month' => $value ? $query->whereMonth($qualifiedColumn, now()->subMonth()->month)
                        ->whereYear($qualifiedColumn, now()->subMonth()->year) : null,
                    default => null
                };
            }
        }
    }

    /**
     * Aplica operadores de filtrado avanzado
     */
    protected function applyFilterOperator(Builder $query, string $column, string $operator, mixed $value): void
    {
        $operator = strtolower($operator);

        if (! isset($this->allowedFilterOperators[$operator])) {
            return;
        }

        $table = $this->getTable();
        $qualifiedColumn = $table.'.'.$column;

        match ($operator) {
            'null' => $value ? $query->whereNull($qualifiedColumn) : $query->whereNotNull($qualifiedColumn),
            'not_null' => $value ? $query->whereNotNull($qualifiedColumn) : $query->whereNull($qualifiedColumn),
            'in' => $query->whereIn($qualifiedColumn, is_array($value) ? $value : explode(',', $value)),
            'not_in' => $query->whereNotIn($qualifiedColumn, is_array($value) ? $value : explode(',', $value)),
            'between' => $query->whereBetween($qualifiedColumn, is_array($value) ? $value : explode(',', $value)),
            'starts' => $query->where($qualifiedColumn, 'LIKE', "{$value}%"),
            'ends' => $query->where($qualifiedColumn, 'LIKE', "%{$value}"),
            'like' => $query->where($qualifiedColumn, 'LIKE', "%{$value}%"),
            'not_like' => $query->where($qualifiedColumn, 'NOT LIKE', "%{$value}%"),
            default => $query->where($qualifiedColumn, $this->allowedFilterOperators[$operator], $value)
        };
    }

    /**
     * Aplica ordenamiento por columna de relación con soporte para relaciones anidadas
     *
     * Soporta:
     * - BelongsTo: user.name
     * - HasOne: profile.bio
     * - HasMany: comments.created_at (ordena por la primera coincidencia)
     * - BelongsToMany: tags.name
     * - Relaciones anidadas: country.region.name
     */
    protected function applySortByRelation(Builder $query, string $path, string $direction): void
    {
        $segments = explode('.', $path);
        $finalColumn = array_pop($segments);

        $currentModel = $this;
        $currentTable = $this->getTable();
        $joinChain = [];

        // Construir cadena de joins para relaciones anidadas
        foreach ($segments as $relationName) {
            $method = Str::camel($relationName);

            if (! method_exists($currentModel, $method)) {
                return;
            }

            try {
                $relationInstance = $currentModel->$method();

                if (! $relationInstance instanceof Relation) {
                    return;
                }

                $relatedModel = $relationInstance->getRelated();
                $relatedTable = $relatedModel->getTable();

                // Validar que la columna final existe en la última tabla
                if ($relationName === end($segments)) {
                    $relatedColumns = Schema::getColumnListing($relatedTable);
                    if (! in_array($finalColumn, $relatedColumns)) {
                        return;
                    }
                }

                // Construir el join según el tipo de relación
                $joinData = $this->buildJoinForRelation(
                    $relationInstance,
                    $currentTable,
                    $relatedTable,
                    $currentModel
                );

                if ($joinData) {
                    $joinChain[] = $joinData;
                    $currentModel = $relatedModel;
                    $currentTable = $relatedTable;
                }

            } catch (\Throwable $e) {
                return;
            }
        }

        // Asegurar que estamos seleccionando las columnas de la tabla principal
        $baseTable = $this->getTable();
        $hasSelect = ! empty($query->getQuery()->columns);

        if (! $hasSelect) {
            $query->select($baseTable.'.*');
        }

        // Aplicar todos los joins de la cadena
        foreach ($joinChain as $join) {
            $joinKey = $join['table'].'.'.$join['first'];

            // Evitar joins duplicados
            if (in_array($joinKey, $this->appliedJoins)) {
                continue;
            }

            if (isset($join['pivot'])) {
                // Join para BelongsToMany (requiere tabla pivote)
                $query->leftJoin(
                    $join['pivot']['table'],
                    $join['pivot']['first'],
                    '=',
                    $join['pivot']['second']
                );

                $query->leftJoin(
                    $join['table'],
                    $join['first'],
                    '=',
                    $join['second']
                );

                $this->appliedJoins[] = $join['pivot']['table'].'.'.$join['pivot']['first'];
            } else {
                // Join normal para otras relaciones
                $query->leftJoin(
                    $join['table'],
                    $join['first'],
                    '=',
                    $join['second']
                );
            }

            $this->appliedJoins[] = $joinKey;
        }

        // Aplicar ordenamiento con el nombre de tabla calificado
        $query->orderBy($currentTable.'.'.$finalColumn, $direction);

        // Si no se han seleccionado columnas, forzamos select del modelo base
        if (empty($query->getQuery()->columns)) {
            $query->select($baseTable.'.*');
        }

        // ✅ Evita conflicto con ONLY_FULL_GROUP_BY agrupando todas las columnas del modelo base
        $columns = Schema::getColumnListing($baseTable);
        $groupColumns = array_map(fn ($col) => $baseTable.'.'.$col, $columns);
        $query->groupBy($groupColumns);

    }

    /**
     * Construye los parámetros de join según el tipo de relación
     *
     * @return array|null Array con 'table', 'first', 'second' y opcionalmente 'pivot'
     */
    protected function buildJoinForRelation(
        Relation $relation,
        string $parentTable,
        string $relatedTable,
        Model $parentModel
    ): ?array {
        return match (true) {
            $relation instanceof BelongsTo => [
                'table' => $relatedTable,
                'first' => $parentTable.'.'.$relation->getForeignKeyName(),
                'second' => $relatedTable.'.'.$relation->getOwnerKeyName(),
            ],

            $relation instanceof HasOne, $relation instanceof HasMany => [
                'table' => $relatedTable,
                'first' => $relatedTable.'.'.$relation->getForeignKeyName(),
                'second' => $parentTable.'.'.$relation->getLocalKeyName(),
            ],

            $relation instanceof BelongsToMany => [
                'pivot' => [
                    'table' => $relation->getTable(),
                    'first' => $parentTable.'.'.$parentModel->getKeyName(),
                    'second' => $relation->getTable().'.'.$relation->getForeignPivotKeyName(),
                ],
                'table' => $relatedTable,
                'first' => $relation->getTable().'.'.$relation->getRelatedPivotKeyName(),
                'second' => $relatedTable.'.'.$relation->getRelated()->getKeyName(),
            ],

            $relation instanceof HasOneThrough, $relation instanceof HasManyThrough => [
                'table' => $relatedTable,
                'first' => $parentTable.'.'.$parentModel->getKeyName(),
                'second' => $relatedTable.'.'.$relation->getFirstKeyName(),
            ],

            $relation instanceof MorphTo => null, // MorphTo no se puede ordenar directamente

            $relation instanceof MorphOne, $relation instanceof MorphMany => [
                'table' => $relatedTable,
                'first' => $relatedTable.'.'.$relation->getForeignKeyName(),
                'second' => $parentTable.'.'.$parentModel->getKeyName(),
            ],

            $relation instanceof MorphToMany => [
                'pivot' => [
                    'table' => $relation->getTable(),
                    'first' => $parentTable.'.'.$parentModel->getKeyName(),
                    'second' => $relation->getTable().'.'.$relation->getForeignPivotKeyName(),
                ],
                'table' => $relatedTable,
                'first' => $relation->getTable().'.'.$relation->getRelatedPivotKeyName(),
                'second' => $relatedTable.'.'.$relation->getRelated()->getKeyName(),
            ],

            default => null,
        };
    }

    /**
     * Parsea constraints de relaciones (select, limit, etc.)
     *
     * Ejemplo: "comments:id,text|limit(5)"
     */
    protected function parseRelationConstraints(string $relationPath): array
    {
        if (! str_contains($relationPath, ':') && ! str_contains($relationPath, '|')) {
            return [$relationPath, null];
        }

        $parts = explode(':', $relationPath, 2);
        $relation = $parts[0];
        $constraints = isset($parts[1]) ? $parts[1] : null;

        if (! $constraints) {
            return [$relation, null];
        }

        return [$relation, function ($query) use ($constraints) {
            // Procesar select de campos: "id,title,content"
            if (str_contains($constraints, '|')) {
                [$fields, $extras] = explode('|', $constraints, 2);
                $query->select(explode(',', $fields));

                // Procesar limit: "limit(5)"
                if (preg_match('/limit\((\d+)\)/', $extras, $matches)) {
                    $query->limit((int) $matches[1]);
                }
            } else {
                $query->select(explode(',', $constraints));
            }
        }];
    }

    /**
     * Valida si una relación existe y es accesible
     */
    protected function isValidRelation(Model $model, string $relation): bool
    {
        $method = Str::camel($relation);

        if (! method_exists($model, $method)) {
            return false;
        }

        try {
            $reflection = new ReflectionMethod($model, $method);

            if ($reflection->getNumberOfParameters() > 0) {
                return false;
            }

            $return = $reflection->invoke($model);

            return $return instanceof Relation;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Valida relaciones anidadas (ej: "author.posts.comments")
     */
    protected function isValidNestedRelation(Model $model, array $segments): bool
    {
        $current = array_shift($segments);
        $method = Str::camel($current);

        if (! method_exists($model, $method)) {
            return false;
        }

        try {
            $reflection = new ReflectionMethod($model, $method);

            if ($reflection->getNumberOfParameters() > 0) {
                return false;
            }

            $return = $reflection->invoke($model);

            if (! $return instanceof Relation) {
                return false;
            }

            return empty($segments)
                ? true
                : $this->isValidNestedRelation($return->getRelated(), $segments);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Obtiene las columnas de la tabla con cache
     */
    protected function getCachedTableColumns(): array
    {
        $cacheKey = 'table_columns_'.$this->getTable();

        return Cache::remember($cacheKey, $this->schemaCacheDuration, function () {
            return Schema::getColumnListing($this->getTable());
        });
    }

    /**
     * Obtiene las columnas de la tabla sin cache (legacy)
     */
    protected function getTableColumns(): array
    {
        return $this->getCachedTableColumns();
    }

    /**
     * Limpia el cache de columnas de la tabla
     */
    public function clearSchemaCache(): void
    {
        $cacheKey = 'table_columns_'.$this->getTable();
        Cache::forget($cacheKey);
    }
}

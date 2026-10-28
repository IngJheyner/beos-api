<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Consultas opcionales por query string, el mismo contrato que api-rest-ful.
 *
 * included=currency,prices
 * filter[name]=notebook
 * sort=-price,name
 * perPage=15
 */
trait ApiTrait
{
    /**
     * Carga solo las relaciones declaradas en $allowIncluded.
     */
    public function scopeIncluded(Builder $query): Builder
    {
        if (empty($this->allowIncluded) || empty(request('included'))) {
            return $query;
        }

        $relations = explode(',', request('included'));

        $allowed = collect($this->allowIncluded)
            ->filter(fn (string $relation) => in_array($relation, $relations, true))
            ->all();

        return $allowed === [] ? $query : $query->with($allowed);
    }

    /**
     * Filtra con LIKE sobre los campos declarados en $allowFilter.
     */
    public function scopeFilter(Builder $query): Builder
    {
        $filters = request('filter');

        if (empty($this->allowFilter) || ! is_array($filters)) {
            return $query;
        }

        collect($this->allowFilter)
            ->filter(fn (string $field) => array_key_exists($field, $filters))
            ->each(function (string $field) use ($filters, $query) {
                $query->where($field, 'LIKE', '%'.$filters[$field].'%');
            });

        return $query;
    }

    /**
     * Ordena por los campos de $allowSort. El prefijo "-" invierte el orden.
     */
    public function scopeSort(Builder $query): Builder
    {
        if (empty($this->allowSort) || empty(request('sort'))) {
            return $query;
        }

        collect(explode(',', request('sort')))->each(function (string $field) use ($query) {
            $direction = 'asc';

            if (str_starts_with($field, '-')) {
                $direction = 'desc';
                $field = substr($field, 1);
            }

            if (! in_array($field, $this->allowSort, true)) {
                return;
            }

            $query->orderBy($field, $direction);
        });

        return $query;
    }

    /**
     * Pagina si llega perPage. El máximo es 100 para evitar lecturas enormes.
     */
    public function scopeGetOrPaginate(Builder $query): mixed
    {
        $perPage = (int) request('perPage');

        if ($perPage > 0) {
            return $query->paginate(min($perPage, 100));
        }

        return $query->get();
    }
}

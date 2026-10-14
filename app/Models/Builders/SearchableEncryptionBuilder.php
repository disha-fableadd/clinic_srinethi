<?php

namespace App\Models\Builders;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchableEncryptionBuilder extends Builder
{
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($column instanceof Closure || is_array($column)) {
            return parent::where($column, $operator, $value, $boolean);
        }

        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        if ($mappedColumn = $this->mapSearchableColumn($column)) {
            $column = $mappedColumn;
            $value = $this->normalizeSearchValue($value);
        }

        return parent::where($column, $operator, $value, $boolean);
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        if ($column instanceof Closure || is_array($column)) {
            return parent::orWhere($column, $operator, $value);
        }

        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        if ($mappedColumn = $this->mapSearchableColumn($column)) {
            $column = $mappedColumn;
            $value = $this->normalizeSearchValue($value);
        }

        return parent::orWhere($column, $operator, $value);
    }

    public function orderBy($column, $direction = 'asc')
    {
        if ($mappedColumn = $this->mapSearchableColumn($column)) {
            $column = $mappedColumn;
        }

        return parent::orderBy($column, $direction);
    }

    private function mapSearchableColumn(mixed $column): ?string
    {
        if (!is_string($column) || str_ends_with($column, '_search')) {
            return null;
        }

        $model = $this->getModel();

        if (!method_exists($model, 'isSearchableEncryptedAttribute')) {
            return null;
        }

        $prefix = '';
        $attribute = $column;

        if (str_contains($column, '.')) {
            [$prefix, $attribute] = explode('.', $column, 2);
        }

        if (!$model->isSearchableEncryptedAttribute($attribute)) {
            return null;
        }

        $searchColumn = $model->getSearchColumnName($attribute);

        return $prefix !== '' ? "{$prefix}.{$searchColumn}" : $searchColumn;
    }

    private function normalizeSearchValue(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        return mb_strtolower($value);
    }
}

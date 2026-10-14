<?php

namespace App\Models\Concerns;

use App\Casts\LenientEncrypted;
use App\Casts\LenientEncryptedArray;
use App\Models\Builders\SearchableEncryptionBuilder;

trait EncryptsModelData
{
    public function initializeEncryptsModelData(): void
    {
        $this->hidden = array_values(array_unique(array_merge(
            $this->hidden,
            array_map(fn(string $attribute) => $this->getSearchColumnName($attribute), $this->getSearchableEncryptedAttributes())
        )));
    }

    public static function bootEncryptsModelData(): void
    {
        static::saving(function ($model) {
            foreach ($model->getSearchableEncryptedAttributes() as $attribute) {
                $model->attributes[$model->getSearchColumnName($attribute)] = $model->toSearchableValue(
                    $model->getAttribute($attribute)
                );
            }
        });
    }

    public function newEloquentBuilder($query): SearchableEncryptionBuilder
    {
        return new SearchableEncryptionBuilder($query);
    }

    public function getCasts(): array
    {
        return array_merge(
            parent::getCasts(),
            array_fill_keys($this->encryptedAttributes(), LenientEncrypted::class),
            array_fill_keys($this->encryptedArrayAttributes(), LenientEncryptedArray::class),
        );
    }

    public function getSearchableEncryptedAttributes(): array
    {
        return $this->searchableEncryptedAttributes();
    }

    public function isSearchableEncryptedAttribute(string $attribute): bool
    {
        return in_array($attribute, $this->getSearchableEncryptedAttributes(), true);
    }

    public function getSearchColumnName(string $attribute): string
    {
        return "{$attribute}_search";
    }

    public function toSearchableValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            $value = json_encode($value);
        }

        return mb_strtolower((string) $value);
    }

    private function encryptedAttributes(): array
    {
        return property_exists($this, 'encryptedAttributes') ? $this->encryptedAttributes : [];
    }

    private function encryptedArrayAttributes(): array
    {
        return property_exists($this, 'encryptedArrayAttributes') ? $this->encryptedArrayAttributes : [];
    }

    private function searchableEncryptedAttributes(): array
    {
        return property_exists($this, 'searchableEncryptedAttributes') ? $this->searchableEncryptedAttributes : [];
    }
}

<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class LenientEncrypted implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): mixed
    {
        return $value;
    }

    public function set($model, string $key, $value, array $attributes): mixed
    {
        return $value;
    }
}

<?php

namespace App\Enums;

trait ArrayableValuesTrait
{
    /**
     * Получение массива values из enum
     *
     * @return array
     */
    public static function getValues(): array
    {
        return collect(static::cases())
            ->pluck('value')
            ->toArray();
    }
}

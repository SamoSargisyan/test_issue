<?php

namespace App\Enums;

interface ArrayableValuesContract
{
    /**
     * Получение массива values из enum
     *
     * @return array
     */
    public static function getValues(): array;
}

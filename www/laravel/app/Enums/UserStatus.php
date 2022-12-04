<?php

namespace App\Enums;

enum UserStatus: string implements ArrayableValuesContract
{
    use ArrayableValuesTrait;

    case active = 'active';
    case verified = 'verified';
    case blocked = 'blocked';
    case suspend = 'suspend';
}

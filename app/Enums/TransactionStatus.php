<?php

namespace App\Enums;

enum TransactionStatus
{
    case PENDING;
    case COMPLETED;
    case FAILED;

    public static function toArray() : array
    {
        return array_map(fn(self $enum) => $enum->name, self::cases());
    }

}

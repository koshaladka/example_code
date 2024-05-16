<?php

namespace App\Constants;

use Illuminate\Support\Collection;

final class FormStatuses
{
    public const DRAFT =  'draft';
    public const QUEUE = 'queue';
    public const SUCCESS = 'success';
    public const UNSUCCEESS = 'unsuccess';

    public const ALL = [
        self::DRAFT,
        self::QUEUE,
        self::SUCCESS,
        self::UNSUCCEESS,
    ];

    public const ALL_NAMES = [
        self::DRAFT => 'Черновик',
        self::QUEUE => 'Поставлен в очередь',
        self::SUCCESS => 'Успешная отправка',
        self::UNSUCCEESS => 'Неуспешная отправка',
    ];

    public static function getStatusName(string $status): string
    {
        return self::ALL_NAMES[$status] ?? 'Неизвестный статус';
    }
}

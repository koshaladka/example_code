<?php

namespace App\Helpers;

use Carbon\Carbon;

final class DateHelper
{

    /**
     * Возвращает формат даты 'D MMM YYYY' - 22 авг 2023
     */
    public static function getDateD_MMM_YYYY($date)
    {
        return Carbon::parse($date)->locale('ru')->isoFormat('D MMM YYYY');
    }

}
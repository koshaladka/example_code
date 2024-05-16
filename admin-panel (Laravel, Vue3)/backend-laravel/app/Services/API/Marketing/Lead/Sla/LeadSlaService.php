<?php

namespace App\Services\API\Marketing\Lead\Sla;

use App\Models\LeadSla;
use App\Models\LeadStatus;
use App\Services\Service;

class LeadSlaService extends Service
{

    /**
     * Создание SLA
     */
    public function createLeadSla(
        string $color,
        int $lead_status,
        int $time_from,
    ): ?LeadSla {

        return LeadSla::create([
            'color' => $color,
            'time_from' => $time_from,
            'lead_status_id' => $lead_status,
        ]);
    }

    /**
     * Обновление SLA
     */
    public function updateLeadSla(
        LeadSla $item,
        int $id,
        string $color,
        int $lead_status,
        int $time_from,
    ): bool {

        return $item->update([
            'color' => $color,
            'time_from' => $time_from,
            'lead_status_id' => $lead_status,
        ]);
    }
}

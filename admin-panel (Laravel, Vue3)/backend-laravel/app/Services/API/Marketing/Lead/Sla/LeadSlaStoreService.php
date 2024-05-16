<?php

namespace App\Services\API\Marketing\Lead\Sla;

use App\Models\LeadStatus;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class LeadSlaStoreService extends LeadSlaService
{
    public function execute(
        array $data
    ): array {

        $leadStatus = LeadStatus::where('slug', $data['lead_status'])
            ->first();

        if (is_null($leadStatus))
            throw new Exception('Статус лида не найден', Response::HTTP_NOT_FOUND);


        $leadSla = $this->createLeadSla(
            color: $data['color'],
            lead_status: $leadStatus->id,
            time_from: $data['time_from'],
        );

        return [
            'success' => true,
            'code' => Response::HTTP_CREATED,
            'data' => [
                'lead_sla_id' => $leadSla->id
            ]
        ];
    }
}

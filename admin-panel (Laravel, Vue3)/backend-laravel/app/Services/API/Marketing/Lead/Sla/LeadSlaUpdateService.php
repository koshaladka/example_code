<?php

namespace App\Services\API\Marketing\Lead\Sla;

use App\Models\LeadSla;
use App\Models\LeadStatus;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Exception;

class LeadSlaUpdateService extends LeadSlaService
{
    public function execute(
        int $id,
        array $data
    ): array {

        $leadSla = LeadSla::find($id);

        if (is_null($leadSla))
            throw new Exception('SLA не найден', Response::HTTP_NOT_FOUND);

        $leadStatus = LeadStatus::where('slug', $data['lead_status'])
            ->first();

        if (is_null($leadStatus))
            throw new Exception('Статус лида не найден', Response::HTTP_NOT_FOUND);

        $this->updateLeadSla(
            item: $leadSla,
            id: $leadSla->id,
            color: $data['color'],
            lead_status: $leadStatus->id,
            time_from: $data['time_from'],
        );

        return [
            'success' => true,
            'code' => Response::HTTP_ACCEPTED
        ];
    }
}

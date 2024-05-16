<?php

namespace App\Services\API\Marketing\Lead\Sla;

use App\Http\Resources\API\Marketing\Lead\Sla\LeadSlaResource;
use App\Models\LeadSla;
use Illuminate\Http\Response;
use Exception;

class LeadSlaShowService extends LeadSlaService
{
    public function execute(
        int $id
    ): array {

        $leadSla = LeadSla::find($id);

        if (is_null($leadSla))
            throw new Exception('SLA не найден', Response::HTTP_NOT_FOUND);


        $response = new LeadSlaResource($leadSla);

        return [
            'success' => true,
            'code' => Response::HTTP_OK,
            'data' => $response
        ];
    }

}

<?php

namespace App\Services\API\Marketing\Lead\Sla;

use App\Models\LeadSla;
use Illuminate\Http\Response;
use Exception;

class LeadSlaDestroyService extends LeadSlaService
{
    public function execute(
        int $id
    ): array {

        $leadSla = LeadSla::find($id);

        if (is_null($leadSla))
            throw new Exception('SLA не найден', Response::HTTP_NOT_FOUND);

        $leadSla->delete();

        return [
            'success' => true,
            'code' => Response::HTTP_ACCEPTED
        ];
    }
}

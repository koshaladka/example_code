<?php

namespace App\Http\Resources\API\Marketing\Lead\Sla;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadSlaResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'time_from' => $this->time_from,
            'color' => $this->color,
            'lead_status_slug' => $this->status?->slug,
        ];
    }
}

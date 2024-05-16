<?php

namespace App\Http\Resources\API\Marketing\Lead\Sla;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LeadSlaCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'time_from' => $item->time_from,
                'color' => $item->color,
                'status' => $item->status_name,
            ];
        });
    }
}

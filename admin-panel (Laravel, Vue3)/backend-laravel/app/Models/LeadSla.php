<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadSla extends Model
{
    protected $guarded = ['id'];

    public function status()
    {
        return $this->belongsTo(LeadStatus::class, 'lead_status_id');
    }
}

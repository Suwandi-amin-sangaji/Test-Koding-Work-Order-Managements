<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusUpdates extends Model
{
    use HasFactory;
    protected $fillable = [
        'work_order_id', 'old_status', 'new_status', 'quantity_updated', 'notes'
    ];
}

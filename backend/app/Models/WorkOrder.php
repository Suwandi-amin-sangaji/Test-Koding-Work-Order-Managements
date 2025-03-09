<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_name', 'quantity', 'deadline', 'status', 'wo_number', 'assigned_operator_id'
    ];
    
    
    public function assignedOperator()
    {
        return $this->belongsTo(User::class, 'assigned_operator_id');
    }
    
    public function statusUpdates()
    {
        return $this->hasMany(StatusUpdates::class);
    }
    
    protected static function booted()
    {
        static::creating(function ($workOrder) {
            $date = now()->format('Ymd');
            $lastWO = self::whereDate('created_at', today())->latest()->first();
            $sequence = $lastWO ? intval(substr($lastWO->wo_number, -3)) + 1 : 1;
            $workOrder->wo_number = 'WO-'.$date.'-'.str_pad($sequence, 3, '0', STR_PAD_LEFT);
        });
    }
}

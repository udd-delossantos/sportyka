<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = [
        'daily_operation_id',
        'staff_id',
        'court_id', 
        'customer_name', 
        'start_time',
        'end_time',
        'expected_hours', 
        'expected_minutes', 
        'amount',
        'transaction_no',
        'status',
        'queue_number'
    ];

    public function staff()
{
    return $this->belongsTo(User::class, 'staff_id');
}

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function gameSession()
    {
        return $this->hasOne(GameSession::class, 'queue_id');
    }

    public static function renumber($courtId, $dailyOperationId)
{
    $waitingQueues = self::where('court_id', $courtId)
        ->where('daily_operation_id', $dailyOperationId)
        ->where('status', 'waiting')
        ->orderBy('created_at')
        ->get();

    foreach ($waitingQueues as $index => $queue) {
        $queue->update(['queue_number' => $index + 1]);
    }
}



}

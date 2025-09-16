<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 
        'court_id', 
        'daily_operation_id', 
        'session_date', 
        'customer_name',
        'session_type', 
        'expected_hours', 
        'expected_minutes',
        'session_date', 
        'start_time', 
        'end_time', 
        'amount_paid', 
        'status', 
        'booking_id',
        'queue_id',
    ];

    public function court() {
        return $this->belongsTo(Court::class);
    }

    public function staff() {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function dailyOperation() {
        return $this->belongsTo(DailyOperation::class);
    }

    public function getExpectedDurationAttribute() {
        return sprintf('%02d:%02d', $this->expected_hours, $this->expected_minutes);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

}

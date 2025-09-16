<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'staff_id',
        'court_id',
        'daily_operation_id',
        'booking_date',
        'start_time',
        'end_time',
        'expected_hours',
        'expected_minutes',
        'amount',
        'transaction_no',
        'status',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }


    public function court() {
        return $this->belongsTo(Court::class);
    }

    public function dailyOperation() {
        return $this->belongsTo(DailyOperation::class);
    }

    public function gameSession()
    {
        return $this->hasOne(GameSession::class, 'booking_id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
    'game_session_id',
    'staff_id',
    'daily_operation_id',
    'amount',
    'payment_method',
    'transaction_no',
];


    public function session()
    {
        return $this->belongsTo(GameSession::class, 'game_session_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function operation()
    {
        return $this->belongsTo(DailyOperation::class, 'daily_operation_id');
    }

}

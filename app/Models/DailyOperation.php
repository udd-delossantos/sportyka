<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'opened_by', 'opened_at', 'closed_by', 'closed_at', 'status'
    ];

    protected $dates = ['opened_at', 'closed_at', 'date'];

    protected $casts = [
    'opened_at' => 'datetime',
    'closed_at' => 'datetime',
    'date' => 'date',
];


    public function sessions()
    {
        return $this->hasMany(GameSession::class, 'daily_operation_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'daily_operation_id');
    }

    public function opener()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}

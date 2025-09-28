<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sport',
        'hourly_rate',
        'status',
        'description',
        'images',
    ];

        protected $casts = [
    'images' => 'array',
];


    
}


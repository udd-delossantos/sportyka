<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GcashQrCode extends Model
{
    protected $fillable = ['file_path', 'is_active'];
}

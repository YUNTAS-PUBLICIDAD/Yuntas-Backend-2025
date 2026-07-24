<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasActivityLog;

class ContactMessage extends Model
{
    use HasFactory, HasActivityLog;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'district',
        'request_detail',
        'message',
    ];
}

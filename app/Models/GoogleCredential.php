<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleCredential extends Model
{
    protected $fillable = [
        'access_token',
        'refresh_token',
        'expires_in',
        'token_type',
    ];
}

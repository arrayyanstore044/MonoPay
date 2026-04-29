<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $table = 'user_session';

    protected $fillable = [
        'token',
        'user_id',
        'expired_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

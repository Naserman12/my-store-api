<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

  class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is_read'
    ];
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}


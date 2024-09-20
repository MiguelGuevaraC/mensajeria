<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendApi extends Model
{
    protected $fillable = [
        'quantitySend',
        'errors',
        'success',
        'dateSend',
        'user_id',
        'state',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function whasappSends()
    {
        return $this->hasMany(WhatsappSend::class);
    }
}

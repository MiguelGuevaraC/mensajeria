<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SendApi extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'quantitySend',
        'errors',
        'success',
        'type',
        'dateSend',
        'user_id',
        'programming_id',
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

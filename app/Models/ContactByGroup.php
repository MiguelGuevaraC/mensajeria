<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactByGroup extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'contact_id',
        'groupSend_id',
        'state',
        'stateSend',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function groupSend()
    {
        return $this->belongsTo(GroupSend::class, 'groupSend_id');
    }
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}

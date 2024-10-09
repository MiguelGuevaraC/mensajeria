<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Programming extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'dateProgram',
        'status',
        'user_id',
        'state',
        'created_at',
        'messageSend',
        'messageWhasapp_id',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function detailProgramming()
    {
        return $this->hasMany(DetailProgramming::class);
    }

    public function contactsByGroup()
    {
        return $this->belongsToMany(
            ContactByGroup::class, 'detail_programmings', 
            'programming_id', 'contactByGroup_id'
        )->whereHas('contact', function ($query) {
            $query->where('state', 1); // Filtramos contactos con state = 1
        })->whereHas('groupSend', function ($query) {
            $query->where('state', 1); // Filtramos grupos con state = 1
        });
    }
    
    public function messageWhasapp()
    {
        return $this->belongsTo(MessageWhasapp::class, 'messageWhasapp_id');
    }

    
}

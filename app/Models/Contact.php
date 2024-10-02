<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'documentNumber',
        'names',
        'telephone',
        'address',
        'concept',
        'amount',
        'dateReference',
        'routeFile',

        'migration_id',
        'groupSend_id',
        'state',

        'status',
        'created_at',

    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    // public function company()
    // {
    //     return $this->belongsTo(Company::class, 'company_id');
    // }

    public function contactByGroup()
    {
        return $this->hasMany(ContactByGroup::class);
    }
    public function group()
    {
        return $this->belongsTo(GroupSend::class, 'groupSend_id');
    }
    public function groupSend()
    {
        return $this->belongsToMany(GroupSend::class, 'contact_by_groups', 'contact_id', 'groupSend_id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupSend extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'name',
        'comment',
        'company_id',

        'state',
        'status',
        'created_at',

    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function contactByGroup()
    {
        return $this->hasMany(ContactByGroup::class);
    }

    public function contactos()
    {
        return $this->belongsToMany(Contact::class, 'contact_by_groups', 'groupSend_id', 'contact_id');
    }

  

}

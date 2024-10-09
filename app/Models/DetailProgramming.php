<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailProgramming extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'documentNumber',
        'names',
        'telephone',
        'address',
        'concept',
        'amount',
        'dateReference',
        'routeFile',

        'status',
        'programming_id',
        'contactByGroup_id',
        'state',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function programming()
    {
        return $this->belongsTo(Programming::class, 'programming_id');
    }
    public function contactByGroup()
    {
        return $this->belongsTo(ContactByGroup::class, 'contactByGroup_id');
    }



}

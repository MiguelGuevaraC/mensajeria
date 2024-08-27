<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'typeOfDocument',
        'documentNumber',
        'businessName',
        'tradeName',
        'representativeName',
        'representativeDni',
        'telephone',
        'email',
        'address',
        'status',
        'state',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

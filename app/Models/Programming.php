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
}

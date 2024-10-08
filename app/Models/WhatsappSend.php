<?php

namespace App\Models;

use Faker\Provider\ar_EG\Person;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappSend extends Model
{
    use  HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'sequentialNumber',
        'messageSend',
        'userResponsability',
        'namesPerson',
        'bussinesName',
        'trade_name',
        'documentNumber',
        'telephone',
        'amount',
        'costSend',
        'concept',
        'routeFile',
        'status',
        'created_at',
        'updated_at',
        'contac_id', 
        'user_id', 
        'sendApi_id',
        'messageWhasapp_id', 
        'contactByGroup_id',
    ];
    
    

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contac_id');
    }
    public function messageWhasapp()
    {
        return $this->belongsTo(MessageWhasapp::class, 'messageWhasapp_id');
    }
    public function sendApi()
    {
        return $this->belongsTo(SendApi::class, 'sendApi_id');
    }
    public function contactByGroup()
    {
        return $this->belongsTo(ContactByGroup::class, 'contactByGroup_id');
    }
}

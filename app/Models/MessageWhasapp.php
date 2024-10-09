<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class MessageWhasapp extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'title',
        'block1',
        'block2',
        'block3',
        'block4',
        'user_id',
        'routeFile',
        'state',
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function updateMessageDataInProgramming()
    {
        $message = $this;

        // Obtener todas las programaciones con estado 'Pendiente'
        $programmingList = Programming::where('status', 'Pendiente')->get();

        // Iterar sobre cada programación
        foreach ($programmingList as $programming) {
            $messageBase = MessageWhasapp::where('id', $message->id)->first() ?? (object) [
                'title' => 'titulo',
                'block1' => 'block1',
                'block2' => 'block2',
                'block3' => 'block3',
                'block4' => 'block4',
            ];
    
            // Obtener los detalles de la programación
            if ( $programming->messageWhasapp_id == $message->id) {
                $programming = Programming::find($programming->id);
                $programming->messageSend = $messageBase->title . "\n\n" . $messageBase->block1 . "\n\n" . $messageBase->block2 . "\n\n" .$messageBase->block3 . "\n\n" . $messageBase->block4;
                $programming->save();

            } else {
                Log::error("Error al actualizar el detalle de la programación");
            }
        }
    }

}

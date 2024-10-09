<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

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

    public function updateDetailContactData()
    {
        $contact = $this;

        // Obtener todas las programaciones con estado 'Pendiente'
        $programmingList = Programming::where('status', 'Pendiente')->get();

        // Iterar sobre cada programación
        foreach ($programmingList as $programming) {
            // Obtener los detalles de la programación
            $detailProgrammings = $programming->detailProgramming ?? [];

            // Iterar sobre los detalles de la programación
            foreach ($detailProgrammings as $detail) {
                // Verificar si el contacto coincide con el contacto actual
                if (isset($detail->contactByGroup->contact) && $detail->contactByGroup->contact->id == $contact->id) {
                    // Actualizar el detalle de la programación con la información del contacto
                    $detailProgramming = DetailProgramming::find($detail->id);
                    if ($detailProgramming) {
                        $detailProgramming->documentNumber = $contact->documentNumber;
                        $detailProgramming->names = $contact->names;
                        $detailProgramming->telephone = $contact->telephone;
                        $detailProgramming->address = $contact->address;
                        $detailProgramming->concept = $contact->concept;
                        $detailProgramming->amount = $contact->amount;
                        $detailProgramming->dateReference = $contact->dateReference;
                        $detailProgramming->routeFile = $contact->routeFile;
                        $detailProgramming->save();
                    }
                } else {
                    Log::error("Error al actualizar el detalle de la programación");
                }
            }
        }
    }

}

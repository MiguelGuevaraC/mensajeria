<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateContactByGroupRequest;
use App\Models\Contact;
use App\Models\ContactByGroup;

class ContactByGroupController extends Controller
{
    public function show(int $id)
    {

        $object = ContactByGroup::with(['contact', 'groupSend'])->find($id);
        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'Contacto no encontrado'], 404
        );

    }

    public function update(UpdateContactByGroupRequest $request, $id)
    {

        $contactByGroup = ContactByGroup::find($id);
        if (!$contactByGroup) {
            return response()->json(
                ['message' => 'Contacto no Encontrado'], 404
            );
        }

        $validatedData = $request->validated();
        $contactByGroup->state = 1;
        $contactByGroup->save();

        $contact = Contact::find($contactByGroup->id);
        if (!$contact) {
            return response()->json(
                ['message' => 'Contacto no Encontrado'], 404
            );
        }

        $validatedData = $request->validated();
        $validatedData['state'] = 1;
        $contact->update($validatedData);
        $contact->updateDetailContactData();

        return response()->json($contact);
    }

    public function destroy(int $id)
    {
        $cobtactByGroup = ContactByGroup::find($id);
        if (!$cobtactByGroup) {
            return response()->json(
                ['message' => 'Contacto no Encontrado'], 404
            );
        }
        $cobtactByGroup->state=0;
        $cobtactByGroup->save();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsappJob;
use App\Models\GroupMenu;
use App\Models\Programming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InicioController extends Controller
{
    public function __construct()
    {
        $this->middleware('ensureTokenIsValid');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $typeUser = $user->typeUser;

        $accesses = $typeUser->getAccess($typeUser->id);

        $currentRoute = $request->path();
        $currentRouteParts = explode('/', $currentRoute);
        $lastPart = end($currentRouteParts);

        if (in_array($lastPart, $accesses)) {
            $groupMenu = GroupMenu::getFilteredGroupMenusSuperior($user->typeofUser_id);
            $groupMenuLeft = GroupMenu::getFilteredGroupMenus($user->typeofUser_id);

            $this->programaciones();

            return view('Modulos.inicio.index', compact('user', 'groupMenu', 'groupMenuLeft'));
        } else {
            abort(403, 'Acceso no autorizado.');
        }
    }

    public function programaciones()
    {
        $fechaActual = now();
        $user = Auth::user();
        $company_id = $user->company_id;
        $user_id = $user->id;

        $programaciones = Programming::where('dateProgram', '<=', $fechaActual)
            ->where('status', 'Pendiente') // Puedes ajustar el estado según lo necesites
            ->get();

        foreach ($programaciones as $programming) {
            // Obtenemos los contactos relacionados a la programación
            $contactsByGroups = $programming->contactsByGroup;

            if ($contactsByGroups->isEmpty()) {
                Log::warning('No se encontraron contactos marcados', ['user_id' => $user_id]);
                return response()->json(['error' => 'No se encontraron contactos marcados'], 422);
            }

            $contactByGroupPaquete = [];
            $jobResponses = []; // Array para almacenar las respuestas de cada job

            $totalEnviados = 0;
            $totalExitosos = 0;
            $totalErrores = 0;

            foreach ($contactsByGroups as $contactByGroup) {
                $contact = $contactByGroup->contact;

                // Verificar que el contacto tenga un número de teléfono
                if ($contact && $contact->telephone) {
                    $contactByGroupPaquete[] = $contactByGroup;
                    Log::info('Contacto agregado', ['contact_id' => $contact->id]);
                }

                // Si el paquete de contactos alcanza los 50, despacha un job y almacena la respuesta
                if (count($contactByGroupPaquete) >= 50) {
                    $response = SendWhatsappJob::dispatchNow($contactByGroupPaquete, $user, $message_id, $typeSend, null); // Usamos dispatchNow para obtener la respuesta directamente
                    $jobResponses[] = $response->original; // Almacenar la respuesta del job
                    $totalEnviados += $response->original['quantitySend'];
                    $totalExitosos += $response->original['success'];
                    $totalErrores += $response->original['errors'];
                    Log::info('Enviando paquete de mensajes', ['cantidad' => count($contactByGroupPaquete)]);
                    $contactByGroupPaquete = []; // Limpiar el paquete
                }
            }

            // Si queda algún paquete menor a 50, también despacharlo y almacenar la respuesta
            if (count($contactByGroupPaquete) > 0) {
                $response = SendWhatsappJob::dispatchNow($contactByGroupPaquete, $user, $message_id, $typeSend, null);
                $jobResponses[] = $response->original; // Almacenar la respuesta del job
                $totalEnviados += $response->original['quantitySend'];
                $totalExitosos += $response->original['success'];
                $totalErrores += $response->original['errors'];
                Log::info('Enviando último paquete de mensajes', ['cantidad' => count($contactByGroupPaquete)]);
            }

        }
    }
}

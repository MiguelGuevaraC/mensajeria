<?php

namespace App\Http\Controllers\web;

use App\Exports\ExportExcel;
use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsappJob;
use App\Models\Contact;
use App\Models\ContactByGroup;
use App\Models\GroupMenu;
use App\Models\WhatsappSend;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class WhatsappSendController extends Controller
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

            return view('Modulos.Mensajeria.index', compact('user', 'groupMenu', 'groupMenuLeft'));
        } else {
            abort(403, 'Acceso no autorizado.');
        }
    }

    public function store(Request $request)
    {
        // Validar el ID del mensaje
        $validator = Validator::make(
            $request->all(),
            [
                'message_id' => 'required|exists:message_whasapps,id', // 'exists' valida que el id exista en la tabla 'message_whasapps'
            ],
            [
                'message_id.required' => 'El campo de mensaje es obligatorio.',
                'message_id.exists' => 'El mensaje seleccionado no existe en la base de datos.',
            ]
        );

        // Retornar error si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $message_id = $request->input('message_id');
        $user = Auth::user();
        $company_id = $user->company_id;
        $user_id = $user->id;

        // Iniciar el registro de logs
        Log::info('Iniciando el envío de mensajes', ['user_id' => $user_id, 'company_id' => $company_id]);

        // Obtener los contactos activos para enviar
        $contactsByGroups = ContactByGroup::where('stateSend', 1) // Solo envíos activos
            ->whereHas('groupSend', function ($query) use ($user_id) {
                $query->where('user_id', $user_id)
                    ->where('state', 1); // Solo grupos con estado activo
            })
            ->with('contact') // Optimiza la consulta para traer el contacto asociado
            ->get();

        // Verificar si no se encontraron contactos
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
                $response = SendWhatsappJob::dispatchNow($contactByGroupPaquete, $user, $message_id); // Usamos dispatchNow para obtener la respuesta directamente
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
            $response = SendWhatsappJob::dispatchNow($contactByGroupPaquete, $user, $message_id);
            $jobResponses[] = $response->original; // Almacenar la respuesta del job
            $totalEnviados += $response->original['quantitySend'];
            $totalExitosos += $response->original['success'];
            $totalErrores += $response->original['errors'];
            Log::info('Enviando último paquete de mensajes', ['cantidad' => count($contactByGroupPaquete)]);
        }

        // Retornar una respuesta con el resultado de los jobs
        return response()->json([
            'message' => 'Mensajes enviados correctamente.',
            'totalEnviados' => $totalEnviados,
            'totalExitosos' => $totalExitosos,
            'totalErrores' => $totalErrores,
        ], 200);
    }

    public function excelExport(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $query = WhatsappSend::with(['user', 'contact.group', 'messageWhasapp'])
            ->where('user_id', Auth::user()->id); // Cambia 'id' por 'user_id'

        // Aplicar filtros por fecha si están presentes
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $data = $query->orderBy('id', 'desc')->get();

        $exportData = [];

        foreach ($data as $moviment) {
            $exportData[] = [
                'Grupo' => $moviment->contact->group->name ?? 'N/A',
                'Contacto' =>
                ($moviment->namesPerson ? $moviment->namesPerson : '') .
                ($moviment->documentNumber ? ' | ' . $moviment->documentNumber : '') .
                ($moviment->telephone ? ' | ' . $moviment->telephone : '') .
                ($moviment->address ? ' | ' . $moviment->address : ''),
                'Concepto' => $moviment->concept ?? '-',
                'Monto' => $moviment->amount ?? '',
                'FechaReferencia' => $moviment->contact->concept ?? '-',
                'FechaEnvio' => $moviment->created_at ?? '-',
                'User' => $moviment->user->username ?? '-',
                'Estado' => $moviment->status ?? '-',
                'Mensaje' => $moviment->messageSend ?? '-',
            ];
        }

        return Excel::download(new ExportExcel($exportData, $startDate, $endDate), strtoupper('export-' . $startDate . '-al-' . $endDate . '.xlsx'));

    }

    public function pdfExport(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $userName = Auth()->user()->username;

        $query = WhatsappSend::with(['user', 'contact.group', 'messageWhasapp'])
            ->where('user_id', Auth::user()->id); // Cambia 'id' por 'user_id'

        // Aplicar filtros por fecha si están presentes
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $data = $query->orderBy('id', 'desc')->get();

        $pdf = PDF::loadView('pdf.export', ['data' => $data,
            'dateStart' => $startDate, 'dateEnd' => $endDate])
            ->setPaper('a4', 'landscape')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        return $pdf->download(strtoupper($userName . '-Reporte-Envios-del-' . $startDate . '-al-' . $endDate . '.pdf'));

    }

}

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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

    public function all(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 15);
        $filters = $request->input('filters', []);

        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $query = WhatsappSend::with(['user', 'user.person', 'conminmnet', 'student'])->whereHas('student', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })
            ->where('state', 1);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        // Aplicar filtros por columna
        foreach ($request->get('columns') as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                $searchValue = trim($column['search']['value'], '()'); // Quitar paréntesis adicionales

                switch ($column['data']) {
                    case 'student.names':
                        $query->whereHas('student', function ($query) use ($searchValue) {
                            $query->where(function ($query) use ($searchValue) {
                                $query->where('names', 'like', '%' . $searchValue . '%')
                                    ->orWhere('fatherSurname', 'like', '%' . $searchValue . '%')
                                    ->orWhere('motherSurname', 'like', '%' . $searchValue . '%')
                                    ->orWhere('documentNumber', 'like', '%' . $searchValue . '%')
                                    ->orWhere('identityNumber', 'like', '%' . $searchValue . '%');;
                            });
                        });
                        break;
                    case 'student.representativeDni':
                        $query->whereHas('student', function ($query) use ($searchValue) {
                            $query->where(function ($query) use ($searchValue) {
                                $query->where('representativeDni', 'like', '%' . $searchValue . '%')
                                    ->orWhere('representativeNames', 'like', '%' . $searchValue . '%');
                            });
                        });
                        break;
                    case 'student.telephone':
                        $query->whereHas('student', function ($query) use ($searchValue) {
                            $query->where(function ($query) use ($searchValue) {
                                $query->where('telephone', 'like', '%' . $searchValue . '%');
                            });
                        });
                        break;
                    case 'conminmnet.cuotaNumber':
                        $query->whereHas('conminmnet', function ($query) use ($searchValue) {
                            $query->where('cuotaNumber', 'like', '%' . $searchValue . '%');
                        });
                        break;
                    case 'student.level':
                        $query->whereHas('student', function ($query) use ($searchValue) {
                            $query->where('grade', 'like', '%' . $searchValue . '%')
                                ->orWhere('section', 'like', '%' . $searchValue . '%')
                                ->orWhere('level', 'like', '%' . $searchValue . '%');
                        });
                        break;
                    case 'conminmnet.paymentAmount':
                        $query->whereHas('conminmnet', function ($query) use ($searchValue) {
                            $query->where('paymentAmount', 'like', '%' . $searchValue . '%');
                        });
                        break;
                    case 'conminmnet.expirationDate':
                        $query->whereHas('conminmnet', function ($query) use ($searchValue) {
                            $query->where('expirationDate', 'like', '%' . $searchValue . '%');
                        });
                        break;
                    case 'conminmnet.conceptDebt':
                        $query->whereHas('conminmnet', function ($query) use ($searchValue) {
                            $query->where('conceptDebt', 'like', '%' . $searchValue . '%');
                        });
                        break;
                    case 'conminmnet.created_at':
                        $query->whereHas('conminmnet', function ($query) use ($searchValue) {
                            // Convertimos el searchValue a formato de fecha compatible con la base de datos
                            try {
                                $date = Carbon::createFromFormat('d-m-Y H:i:s', $searchValue);
                                $searchValue = $date->format('Y-m-d H:i:s');
                            } catch (\Exception $e) {
                                // Si el formato no es válido, no aplicamos ningún filtro
                                $searchValue = null;
                            }

                            if ($searchValue) {
                                $query->where('created_at', 'like', '%' . $searchValue . '%');
                            }
                        });
                        break;

                }
            }
        }

        $totalRecords = $query->count();

        $list = $query->orderBy('id', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        //  dd(json_decode($list));

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $list,
        ]);
    }

    public function store(Request $request)
    {
        $validator = validator()->make(
            $request->all(),
            [
                'message_id' => 'required|exists:message_whasapps,id', // 'exists' valida que el id exista en la tabla 'message_whasapps'
            ],
            [
                'message_id.required' => 'El campo de mensaje es obligatorio.',
                'message_id.exists' => 'El mensaje seleccionado no existe en la base de datos.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $message_id = $request->input('message_id');
        $user = Auth::user();
        $company_id = $user->company_id;
        $user_id = $user->_id;

        // Iniciar el registro de logs
        Log::info('Iniciando el envío de mensajes', ['user_id' => $user->id, 'company_id' => $company_id]);

        $contactsByGroups = ContactByGroup::where('stateSend', 1) // Solo envíos activos
            ->whereHas('groupSend', function ($query) use ($user_id) {
                $query->where('user_id', $user_id)
                    ->where('state', 1); // Solo grupos con estado activo
            })
            ->get();

        // Verificar si no se encontraron contactos
        if ($contactsByGroups->isEmpty()) {
            Log::warning('No se encontraron contactos marcados', ['user_id' => $user->id]);
            return response()->json(['error' => 'No se encontraron contactos marcados'], 422);
        }

        $contactByGroupPaquete = []; // Inicializar el array para los contactos

        foreach ($contactsByGroups as $contactByGroup) {
            $contactByGroupBD = ContactByGroup::find($contactByGroup->id);
            $contact = Contact::find($contactByGroupBD->contact_id);

            if ($contactByGroupBD && $contact->telephone) {
                $contactByGroupPaquete[] = $contactByGroupBD;
                Log::info('Contacto agregado', ['contact_id' => $contactByGroupBD->contact_id]);
            }

            if (count($contactByGroupPaquete) >= 50) {
                SendWhatsappJob::dispatch($contactByGroupPaquete, $user, $message_id);
                Log::info('Enviando paquete de mensajes', ['cantidad' => count($contactByGroupPaquete)]);
                $contactByGroupPaquete = [];
            }
        }

        if (count($contactByGroupPaquete) > 0) {
            SendWhatsappJob::dispatch($contactByGroupPaquete, $user, $message_id);
            Log::info('Enviando último paquete de mensajes', ['cantidad' => count($contactByGroupPaquete)]);
        }

        // Respuesta 200
        return response()->json(['message' => 'Mensajes enviados correctamente.'], 200);
    }

    public function excelExport(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $query = WhatsappSend::with(['user', 'user.person', 'conminmnet', 'student'])
            ->whereHas('student', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })
            ->where('state', 1);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $data = $query->orderBy('id', 'desc')->get();

        $exportData = [];

        foreach ($data as $moviment) {
            $student = '';
            if ($moviment->student->typeofDocument != 'RUC') {
                $student = $moviment->student->names . ' ' . $moviment->student->fatherSurname;
            } else {
                $student = $moviment->student->businessName ?? '';
            }
            $student = $moviment->student->documentNumber . ' | ' . $student;

            $exportData[] = [
                'CuotasVencidas' => $moviment->cuota,
                'Estudiante' => $moviment->dniStudent . ' | ' . $moviment->namesStudent,
                'Padres' => $moviment->namesParent ?? '-',
                'InfoEstudiante' => $moviment->infoStudent ?? '',
                'Telefono' => $moviment->telephone ?? '-',
                'Meses' => $moviment->conceptSend ?? '-',
                'MontoPago' => $moviment->paymentAmount ?? '-',
                'FechaEnvio' => $moviment->created_at ? $moviment->created_at->format('Y-m-d H:i:s') : '-',
                'Mensaje' => $moviment->description ?? '-',
            ];
        }

        return Excel::download(new ExportExcel($exportData, $startDate, $endDate), 'export.xlsx');
    }

    public function pdfExport(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $query = WhatsappSend::with(['user', 'user.person', 'conminmnet', 'student'])
            ->whereHas('student', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })
            ->where('state', 1);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $data = $query->orderBy('id', 'desc')->get();

        $pdf = PDF::loadView('pdf.export', ['data' => $data,
            'dateStart' => $startDate, 'dateEnd' => $endDate])
            ->setPaper('a4', 'landscape')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->download('export.pdf');
    }

}

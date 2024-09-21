<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Imports\PersonImport;
use App\Models\ContactByGroup;
use App\Models\GroupMenu;
use App\Models\GroupSend;
use App\Models\MessageWhasapp;
use App\Models\MigrationExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
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

            return view('Modulos.Contact.index', compact('user', 'groupMenu', 'groupMenuLeft'));
        } else {
            abort(403, 'Acceso no autorizado.');
        }
    }

    public function summarySend()
    {
        $user = Auth::user();
        $company_id = $user->company_id;

        // Agrupar y contar contactos por grupo directamente en la base de datos
        $groupSends = ContactByGroup::selectRaw('group_sends.id as idGroupSend,group_sends.name as groupName, COUNT(contact_by_groups.contact_id) as contactCount')
            ->join('group_sends', 'contact_by_groups.groupSend_id', '=', 'group_sends.id')
            ->join('contacts', 'contact_by_groups.contact_id', '=', 'contacts.id')
            ->where('group_sends.user_id', $user->id)
            ->where('group_sends.state', 1) // Solo grupos con estado activo
            ->where('contacts.state', 1) // Solo contactos con estado activo
            ->where('contact_by_groups.stateSend', 1) // Solo envíos activos
            ->groupBy('group_sends.id', 'group_sends.name') // Agrupamos por grupo
            ->get();

        // Contar el total de grupos y el total de contactos
        $totalGroups = $groupSends->count();
        $totalContacts = $groupSends->sum('contactCount');

        $mensajes = MessageWhasapp::where('state', 1)
            ->where('user_id', $user->id) // Filtro por company_id
            ->orderBy('id', 'desc')->get();

        // Preparar el array de respuesta
        $data = [
            "countTotalgroupSends" => $totalGroups, // Cantidad total de grupos
            "countTotalContact" => $totalContacts, // Cantidad total de contactos
            "arrayGroups" => $groupSends, // Grupos con su nombre y cantidad de contactos
            "mensajes" => $mensajes,
        ];

        return response()->json($data);
    }

    public function contactsForSendByGroup($id)
    {
        $user = Auth::user();
        $company_id = $user->company_id;

        $groupSend = GroupSend::find($id);
        if (!$groupSend) {
            return response()->json(
                ['message' => 'Grupo No Encontrado'], 404
            );
        }

        $contactsByGroup = ContactByGroup::select(
            'contact_by_groups.id as idContactByGroup',
            'contacts.names as name',
            'contacts.telephone as telephone'
        )
            ->join('group_sends', 'contact_by_groups.groupSend_id', '=', 'group_sends.id')
            ->join('contacts', 'contact_by_groups.contact_id', '=', 'contacts.id')
            ->where('group_sends.user_id', $user->id)
            ->where('group_sends.state', 1) // Solo grupos con estado activo
            ->where('group_sends.id', $id)
            ->where('contacts.state', 1) // Solo contactos con estado activo
            ->where('contact_by_groups.stateSend', 1) // Solo envíos activos
            ->get();

        $data = [
            "arrayContactsByGroup" => $contactsByGroup, // Contactos por grupo
            "totalContacts" => $contactsByGroup->count(), // Total de contactos
            "groupName" => $groupSend->name ?? '',
        ];

        return response()->json($data);
    }

    public function all(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 15);
        $filters = $request->input('filters', []);
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        // Filtrar por company_id para asegurar que solo se obtengan los registros relacionados con esa compañía
        $query = ContactByGroup::with([
            'contact',
            'groupSend' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            },
        ])->whereHas('groupSend', function ($query) use ($user_id) {
            // Asegurar que el filtro user_id esté en todos los groupSend
            $query->where('user_id', $user_id);
        })->where('state', 1)->orderBy('contact_id', 'desc');

        // Aplicar filtros por columna
        foreach ($request->get('columns') as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                $searchValue = trim($column['search']['value'], '()'); // Quitar paréntesis adicionales

                switch ($column['data']) {
                    case 'group_send.name':
                        // Filtrar por el nombre del grupo, asegurando el company_id
                        $query->whereHas('groupSend', function ($query) use ($searchValue, $company_id) {
                            $query->where('name', 'like', '%' . $searchValue . '%')
                                ->where('company_id', $company_id);
                        });
                        break;

                    case 'contact.names':
                        // Filtrar por el nombre del contacto
                        $query->whereHas('contact', function ($query) use ($searchValue) {
                            $query->where('names', 'like', '%' . $searchValue . '%')
                                ->orWhere('documentNumber', 'like', '%' . $searchValue . '%');
                        });
                        break;

                    case 'contact.telephone':
                        $query->whereHas('contact', function ($query) use ($searchValue) {
                            $query->where('telephone', 'like', '%' . $searchValue . '%');
                        });
                        break;

                    case 'contact.address':
                        $query->whereHas('contact', function ($query) use ($searchValue) {
                            $query->where('address', 'like', '%' . $searchValue . '%');
                        });
                        break;

                    case 'contact.concept':
                        $query->whereHas('contact', function ($query) use ($searchValue) {
                            $query->where('concept', 'like', '%' . $searchValue . '%');
                        });
                        break;

                    case 'contact.amount':
                        $query->whereHas('contact', function ($query) use ($searchValue) {
                            $query->where('amount', 'like', '%' . $searchValue . '%');
                        });
                        break;

                    case 'contact.dateReference':

                        $query->whereHas('contact', function ($query) use ($searchValue) {
                            $query->whereRaw("DATE_FORMAT(dateReference, '%Y-%m-%d') like ?", ['%' . $searchValue . '%']);
                        });

                        break;

                    case 'created_at':

                        $query->where('created_at', 'like', '%' . $searchValue . '%');
                        break;
                }
            }
        }

        // Contar el total de registros después de aplicar los filtros
        $totalRecords = $query->count();

        // Paginación
        $list = $query->skip($start)
            ->take($length)

            ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $list,
        ]);
    }

    public function importExcel(Request $request)
    {
        $messages = [
            'excelFile.required' => 'El archivo Excel es obligatorio.',
            'excelFile.file' => 'El archivo debe ser válido.',
            'excelFile.mimes' => 'El archivo debe tener una extensión válida: xlsx o xls.',
            'groupsend.required' => 'El ID del grupo es obligatorio.',
            'groupsend.integer' => 'El ID del grupo debe ser un número entero.',
            'groupsend.exists' => 'El grupo seleccionado no es válido o no existe.',
        ];

        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'excelFile' => 'required|file|mimes:xlsx,xls', // Validar que el archivo sea Excel
            'groupsend' => 'required|integer|exists:group_sends,id', // Validar que el ID del grupo sea entero y exista en la tabla group_sends
        ], $messages); // Pasar los mensajes personalizados

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        }
        $excelFile = $request->file('excelFile');
        $groupsend = $request->input('groupsend');

        try {
            $user = Auth::user();
            $currentTime = now();
            $filename = $currentTime->format('YmdHis') . '_' . $excelFile->getClientOriginalName();
            $path = $excelFile->storeAs('public/import/student', $filename);
            $rutaImagen = Storage::url($path);

            $tipo = 'D' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
            $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(number, LOCATE("-", number) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM migration_exports a WHERE SUBSTRING(number, 1, 4) = ?', [$tipo])[0]->siguienteNum;

            $siguienteNum = (int) $resultado;

            $dataMigration = [
                'number' => $tipo . "-" . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
                'type' => 'Estudiantes',
                'comment' => $request->input('comment') ?? '-',
                'routeExcel' => $rutaImagen ?? '-',
                'user_id' => Auth::user()->id,
            ];

            if ($excelFile) {

                $extension = $excelFile->getClientOriginalExtension();

                if ($extension === 'xls') {
                    Excel::import(new PersonImport($groupsend), $excelFile, null, \Maatwebsite\Excel\Excel::XLS);
                } elseif ($extension === 'xlsx') {
                    Excel::import(new PersonImport($groupsend), $excelFile, null, \Maatwebsite\Excel\Excel::XLSX);
                }
                MigrationExport::create($dataMigration);
                return redirect()->back()->with('success', 'Datos importados correctamente.');
            }

            // Redireccionar con mensaje de éxito
            return redirect()->back()->with('success', 'Datos importados correctamente.');
        } catch (\Exception $e) {
            // Capturar cualquier excepción y redirigir con mensaje de error
            return redirect()->back()->with('error', 'Error al importar el archivo: ' . $e->getMessage());
        }
    }

    public function stateSend($id)
    {
        $contactByGroup = ContactByGroup::find($id);

        if (!$contactByGroup) {
            return response()->json(['error' => 'Contacto no encontrado'], 404);
        }

        $contactByGroup->stateSend = !$contactByGroup->stateSend;
        $contactByGroup->save();

        return response()->json(['success' => 'Estado actualizado'], 200);
    }
    public function stateSendByGroup($id)
    {
        // Verificar si el ID es -1
        if ($id == -1) {
            $company_id = Auth::user()->company_id;
            $user_id = Auth::user()->id;

            $updatedRows = ContactByGroup::whereHas('groupSend', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->update(['stateSend' => 1]);

            return response()->json(['success' => 'Estado actualizado para contactos de la empresa'], 200);
        } else if ($id == -2) {
            $company_id = Auth::user()->company_id;
            $user_id = Auth::user()->id;

            $updatedRows = ContactByGroup::whereHas('groupSend', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->update(['stateSend' => 0]);

            return response()->json(['success' => 'Estado actualizado para contactos de la empresa'], 200);

        } else {
            // Buscar el grupo por ID
            $groupSend = GroupSend::find($id);

            if (!$groupSend) {
                return response()->json(['error' => 'Grupo no encontrado'], 404);
            }

            // Actualizar contactos del grupo
            $updatedRows = ContactByGroup::where('groupSend_id', $id)
                ->update(['stateSend' => 1]);

            return response()->json(['success' => 'Estado actualizado para el grupo'], 200);
        }
    }

    public function disabledSendByGroup($id)
    {

        $groupSend = GroupSend::find($id);

        if (!$groupSend) {
            return response()->json(['error' => 'Grupo no encontrado'], 404);
        }

        // Actualizar contactos del grupo
        $updatedRows = ContactByGroup::where('groupSend_id', $id)
            ->update(['stateSend' => 0]);

        return response()->json(['success' => 'Estado actualizado para el grupo'], 200);

    }

}

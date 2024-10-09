<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\ContactByGroup;
use App\Models\DetailProgramming;
use App\Models\GroupMenu;
use App\Models\Programming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProgrammingController extends Controller
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

            return view('Modulos.Programacion.index', compact('user', 'groupMenu', 'groupMenuLeft'));
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
        $user = Auth::user();

        try {
            // Iniciar la consulta con las relaciones necesarias
            $query = Programming::with([
                'user',
                'user.company',
                'messageWhasapp',
                'contactsByGroup',
            ])
            ;

            if ($user->typeofUser_id == 1) {
            } else if ($user->typeofUser_id == 2) {
                $query->whereHas('user', function ($q) use ($user) {
                    $q->where('company_id', $user->company_id);
                });
            } else {
                $query->where('user_id', $user->id);
            }

            // Aplicar filtros por fecha si están presentes
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            // Aplicar filtros por columnas
            foreach ($request->get('columns') as $column) {
                if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                    $searchValue = trim($column['search']['value'], '()'); // Quitar paréntesis adicionales

                    switch ($column['data']) {

                        case 'user.username':
                            // Filtrar por el nombre del grupo, asegurando el company_id
                            $query->where(function ($q) use ($searchValue) {
                                // Filtro por username
                                $q->whereHas('user', function ($query) use ($searchValue) {
                                    $query->where('username', 'like', '%' . $searchValue . '%');
                                })

                                // Filtro por businessName y documentNumber
                                    ->orWhereHas('user.company', function ($query) use ($searchValue) {
                                        $query->where('businessName', 'like', '%' . $searchValue . '%')
                                            ->orWhere('documentNumber', 'like', '%' . $searchValue . '%');
                                    });
                            });
                            break;

                        case 'dateSend':
                            $query->where('dateSend', 'like', '%' . $searchValue . '%');
                            break;

                        case 'dateProgram':
                            $query->where('dateProgram', 'like', '%' . $searchValue . '%');
                            break;
                        case 'created_at':
                            $query->where('created_at', 'like', '%' . $searchValue . '%');
                            break;
                        case 'status':
                            $query->where('status', 'like', '%' . $searchValue . '%');
                            break;
                    }
                }
            }

            // Obtener el total de registros filtrados
            $totalRecords = $query->count();

            // Obtener la lista de registros
            $list = $query->orderBy('id', 'desc')
                ->skip($start)
                ->take($length)
                ->get();

            // Retornar la respuesta JSON
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $list,
            ]);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y registrar el error
            Log::error('Error en la función all: ' . $e->getMessage(), [
                'code' => $e->getCode(),
                'request' => $request->all(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            // Retornar un mensaje de error genérico al cliente
            return response()->json([
                'error' => true,
                'message' => 'Ocurrió un error al procesar la solicitud. Inténtalo de nuevo más tarde.',
            ], 500);
        }
    }

    public function showPendientes($id)
    {
        $object = ContactByGroup::with(['contact', 'groupSend'])->find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'Contacto no encontrado'], 404
            );
        }

        // Incluir registros eliminados con withTrashed() en el modelo y sus relaciones
        $programmings = Programming::with('messageWhasapp')->where('status', 'Pendiente')->get();

        return response()->json(["contactByGroup" => $object, "programmings" => $programmings], 200);
    }

    public function show($id)
    {
        // Incluir registros eliminados con withTrashed() en el modelo y sus relaciones
        $message = Programming::withTrashed()
            ->with([
                'user' => function ($query) {
                    // Puedes agregar más filtros o campos en esta consulta si es necesario
                },
                'user.company' => function ($query) {
                    // Puedes agregar más filtros o campos en esta consulta si es necesario
                },
                'detailProgramming' => function ($query) {
                    $query->withTrashed() // Incluir detalles de programación eliminados
                        ->where('state', 1); // Filtrar solo los registros con state 1
                },
            ])
            ->find($id);

        // Verificar si se encontró la programación
        if (!$message) {
            return response()->json(
                ['message' => 'Programación no encontrada'], 404
            );
        }

        // Agregar el conteo de los detailProgramming (incluyendo los eliminados)
        $message->count = $message->detailProgramming->count();

        return response()->json($message, 200);
    }

    public function addDetailProgramming(Request $request)
    {
        $idContactByGroup = $request->get('idContactByGroup');
        $idProgramming = $request->get('idProgramming');

        // Incluir registros eliminados con withTrashed() en el modelo y sus relaciones
        $contactByGroup = ContactByGroup::find($idContactByGroup);

        if (!$contactByGroup) {
            return response()->json(
                ['message' => 'El Contacto no fué encontrado'], 404
            );
        }
    
        $programming = Programming::where('status', 'Pendiente')->find($idProgramming);

        if (!$programming) {
            return response()->json(
                ['message' => 'La programación no está en Pendiente'], 404
            );
        }

        $detailNuevo = DetailProgramming::create([
            'status' => 'Pendiente', // Puedes usar otro estado que necesites
            'programming_id' => $programming->id,
            'contactByGroup_id' => $contactByGroup->id,
            'state' => 1, // Asumimos que 1 es el estado de activo
        ]);
        $contactByGroup->contact->updateDetailContactData();

        return response()->json($detailNuevo, 200);
    }
}

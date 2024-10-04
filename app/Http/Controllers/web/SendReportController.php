<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\GroupMenu;
use App\Models\WhatsappSend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SendReportController extends Controller
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

    // Asegúrate de importar el facade de Log

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
            $query = WhatsappSend::with(['user', 'contact.group', 'messageWhasapp'])
            ; // Cambia 'id' por 'user_id'

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
                        case 'contact.group.name':
                            // Filtrar por el nombre del grupo, asegurando el company_id
                            $query->whereHas('contact.group', function ($query) use ($searchValue) {
                                $query->where('name', 'like', '%' . $searchValue . '%')
                                ;
                            });
                            break;
                            case 'user.username':
                                // Filtrar por el nombre del grupo, asegurando el company_id
                                $query->whereHas('user', function ($query) use ($searchValue) {
                                    $query->where('username', 'like', '%' . $searchValue . '%')
                                    ;
                                });
                                break;

                        case 'namesPerson':
                            $query->where('namesPerson', 'like', '%' . $searchValue . '%')
                                ->orWhere('documentNumber', 'like', '%' . $searchValue . '%')
                                ->orWhere('telephone', 'like', '%' . $searchValue . '%');
                            break;

                        case 'concept':
                            $query->where('concept', 'like', '%' . $searchValue . '%');
                            break;

                        case 'amount':
                            $query->where('amount', 'like', '%' . $searchValue . '%');
                            break;

                        case 'contact.dateReference':
                            $query->whereHas('contact', function ($query) use ($searchValue) {
                                $query->whereRaw("DATE_FORMAT(dateReference, '%Y-%m-%d') like ?", ['%' . $searchValue . '%']);
                            });
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

}

<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupSendRequest;
use App\Http\Requests\UpdateGroupSendRequest;
use App\Models\GroupMenu;
use App\Models\GroupSend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupSendController extends Controller
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

            return view('Modulos.GroupSend.index', compact('user', 'groupMenu', 'groupMenuLeft'));
        } else {
            abort(403, 'Acceso no autorizado.');
        }
    }

    public function allGroupSend()
    {

        $user = Auth::user();
        $user_id = $user->id;

        $query = GroupSend::where('state', 1)
            ->where('user_id', $user_id)
        ;
        if ($user->typeofUser_id == 1) {
            // $query->whereHas('user', function ($q) use ($user) {
            //     $q->where('company_id', $user->company_id);
            // });
        } else if ($user->typeofUser_id == 2) {

            $query->whereHas('user', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            });

        } else {
            $query->where('user_id', $user->id);
        }
        $groupSends = $query->get();

        $data = [
            "groupSends" => $groupSends,
        ];

        return response()->json($data);
    }

    public function groupsWithContacts()
    {
        $user = Auth::user();
        $user_id = $user->id;

        $query = GroupSend::where('group_sends.state', 1)
        // ->where('user_id', $user_id)
            ->whereHas('contactos', function ($query) {
                $query->where('contacts.state', 1);
            });

        if ($user->typeofUser_id == 1) {
        } else if ($user->typeofUser_id == 2) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            });
        } else {
            $query->where('user_id', $user->id);
        }
        $groupSends = $query->orderBy('created_at', 'asc')->get();

        $data = [
            "groupSends" => $groupSends,
        ];

        return response()->json($data);
    }

    public function all(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 15);
        $filters = $request->input('filters', []);

        $user = Auth::user();

        $query = GroupSend::with(['user.company'])->where('state', 1);

        if ($user->typeofUser_id == 1) {
            // $query->whereHas('user', function ($q) use ($user) {
            //     $q->where('company_id', $user->company_id);
            // });
        } else if ($user->typeofUser_id == 2) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            });
        } else {
            $query->where('user_id', $user->id);
        }

        $query->orderBy('id', 'desc');

        // Aplicar filtros por columna
        foreach ($request->get('columns') as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                $searchValue = trim($column['search']['value'], '()'); // Quitar paréntesis adicionales

                switch ($column['data']) {
                    case 'name':
                        $query->where('name', 'like', '%' . $searchValue . '%');
                        break;
                    case 'user.username':
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
                    case 'comment':
                        $query->where('comment', 'like', '%' . $searchValue . '%');
                        break;
                    case 'created_at':
                        $query->where('created_at', 'like', '%' . $searchValue . '%');
                        break;
                }
            }
        }

        // Obtener el total de registros filtrados y totales
        $totalRecords = $query->count();
        $filteredRecords = $query->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords, // Para filtros aplicados puedes calcular los registros filtrados
            'data' => $filteredRecords,
        ]);
    }

    public function show(int $id)
    {

        $object = GroupSend::find($id);
        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'Grupo No Encontrado'], 404
        );

    }
    public function store(StoreGroupSendRequest $request)
    {
        $validatedData = $request->validated();
        // $company_id = Auth::user()->company->id;
        $user = Auth::user();
        $user_id = $user->id;
        // Crear el nuevo usuario
        $data = [
            'name' => $validatedData['name'],
            'comment' => $validatedData['comment'],
            'user_id' => $user_id,
        ];
        $object = GroupSend::create($data);

        // Mostrar el nuevo usuario
        $object = GroupSend::find($object->id);

        return response()->json($object, 200);
    }
    public function update(UpdateGroupSendRequest $request, $id)
    {
        // Validar los datos
        $validatedData = $request->validated();
        $user = Auth::user();
        $user_id = $user->id;
        // Buscar el groupSend por ID
        $groupSend = GroupSend::findOrFail($id);

        // Obtener el ID de la empresa del usuario autenticado
        // $company_id = Auth::user()->company->id;

        // Preparar los datos a actualizar
        $data = [
            'name' => $validatedData['name'],
            'comment' => $validatedData['comment'],
            'user_id' => $user_id,
        ];

        // Actualizar el registro existente
        $groupSend->update($data);

        // Devolver el objeto actualizado
        return response()->json($groupSend, 200);
    }
    public function destroy(int $id)
    {
        $object = GroupSend::find($id);

        if (!$object) {
            return response()->json(['message' => 'Grupo No Encontrado'], 404);
        }
        $object->contactos()->update(['state' => 0]); // Esto actualizará el estado de todos los contactos a 1

        // Elimina todos los contactos relacionados con el grupo
        $object->contactos()->delete(); // Esto eliminará todos los contactos de la base de datos

        // Cambia el estado del grupo a 0 (desactivado)
        $object->state = 0;
        $object->save();

        // Finalmente, elimina el grupo
        $object->delete();

        return response()->json(['message' => 'Grupo y contactos eliminados correctamente'], 200);
    }

}

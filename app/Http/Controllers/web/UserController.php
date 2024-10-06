<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\GroupMenu;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('ensureTokenIsValid');
    }

    /**
     * Get all Group menus
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/user",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */

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

            return view('Modulos.User.index', compact('user', 'groupMenu', 'groupMenuLeft'));
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
        $user = Auth::user();
        $query = User::with(['typeUser', 'company']);
        if ($user->typeofUser_id != 1) {
            $query->where('company_id', $user->company_id);
        }

        $query->whereNotIn('id', [1, 2]) // Excluye ciertos usuarios si es necesario
            ->orderBy('id', 'desc');

        // Aplicar filtros por columna
        foreach ($request->get('columns') as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                $searchValue = trim($column['search']['value'], '()'); // Quitar paréntesis adicionales

                switch ($column['data']) {
                    case 'username':
                        $query->where('username', 'like', '%' . $searchValue . '%');
                        break;
                    case 'email':
                        $query->where('email', 'like', '%' . $searchValue . '%');
                        break;
                    case 'type_user':
                        $query->whereHas('typeUser', function ($q) use ($searchValue) {
                            $q->where('name', 'like', '%' . $searchValue . '%');
                        });
                        break;
                    case 'company':
                        // Concatenar campos de la relación `company`
                        $query->whereHas('company', function ($q) use ($searchValue) {
                            $q->where(DB::raw("CONCAT(LOWER(businessName), ' | ', LOWER(tradeName), ' | ', LOWER(documentNumber))"), 'like', '%' . strtolower($searchValue) . '%');
                        });
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

    /**
     * @OA\Post(
     *      path="/tecnimotors-backend/public/api/user",
     *      summary="Store a new user",
     *      tags={"User"},
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"username","password","typeofUser_id","worker_id"},
     *              @OA\Property(property="username", type="string", example="username", description="Username of the user"),
     *              @OA\Property(property="password", type="string", example="12345678", description="Password of the user"),
     *              @OA\Property(property="typeofUser_id", type="integer", example="1", description="Type of user"),
     *              @OA\Property(property="worker_id", type="integer", example="1", description="Worker of user")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User created",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *      )
     * )
     */

    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();

        // Verificar si ya existe un usuario de tipo 2 para la empresa especificada
        if ($validatedData['typeofUser_id'] == 2) {
            $existingUser = User::where('company_id', $validatedData['company_id'])
                ->where('typeofUser_id', 2)
                ->first();

            if ($existingUser) {
                return response()->json([
                    'error' => 'Ya existe un usuario de tipo Administrador para esta empresa.',
                ], 422); // Código de estado 422 para errores de validación
            }
        }
        // Cifrar la contraseña
        $hashedPassword = Hash::make($validatedData['password']);

        // Crear el nuevo usuario
        $data = [
            'username' => $validatedData['username'],
            'password' => $hashedPassword,
            'typeofUser_id' => $validatedData['typeofUser_id'],
            'company_id' => $validatedData['company_id'],
        ];
        $object = User::create($data);
        $object->createMensajeBase();
        $object->createGroupSend();

        // Mostrar el nuevo usuario
        $object = $this->show($object->id);

        return response()->json($object, 200);
    }

    /**
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/user/{id}",
     *     summary="Update user by ID",
     *     tags={"User"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of user",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"username","password","typeofUser_id","worker_id"},
     *              @OA\Property(property="username", type="string", example="username", description="Username of the user"),
     *              @OA\Property(property="password", type="string", example="12345678", description="Password of the user"),
     *              @OA\Property(property="typeofUser_id", type="integer", example="1", description="Type of user"),
     *              @OA\Property(property="worker_id", type="integer", example="1", description="Worker of user")
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="User updated",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User  not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     * )
     *
     */
    public function update(UpdateUserRequest $request, string $id)
    {

        $object = User::find($id);

        if (!$object) {
            return response()->json(
                ['message' => 'User not found'], 404
            );
        }
        $validatedData = $request->validated();

        // Verificar si ya existe un usuario de tipo 2 para la empresa especificada
        // if ($validatedData['typeofUser_id'] == 2) {
        //     $existingUser = User::where('company_id', $validatedData['company_id'])
        //         ->where('typeofUser_id', 2)
        //         ->first();

        //     if ($existingUser) {
        //         return response()->json([
        //             'error' => 'Ya existe un usuario de tipo Administrador para esta empresa.',
        //         ], 422); // Código de estado 422 para errores de validación
        //     }
        // }
        // Cifrar la contraseña
        $hashedPassword = Hash::make($validatedData['password']);

        // Crear el nuevo usuario
        $data = [
            'username' => $validatedData['username'],
            'password' => $hashedPassword,
            'typeofUser_id' => $validatedData['typeofUser_id'],
            'company_id' => $validatedData['company_id'],
        ];

        $object->update($data);
        $object = $this->show($object->id);

        return response()->json($object, 200);
    }

    public function updatePassword(Request $request)
{
    $user = User::find(Auth()->user()->id);
    $validator = validator()->make($request->all(), [
        'username' => [
            'required',
            Rule::unique('users')->ignore($user->id)->whereNull('deleted_at'),
        ],
        'passOld' => 'required|string',
        'passNew' => 'required|string|min:6', // Puedes ajustar la longitud mínima según tus necesidades
        'passConf' => 'required|string|same:passNew', // Verifica que coincidan con passNew
    ], [
        'username.required' => 'El nombre de usuario es obligatorio.',
        'username.unique' => 'El nombre de usuario ya está en uso, elige otro.',
        'passOld.required' => 'La contraseña anterior es obligatoria.',
        'passOld.string' => 'La contraseña anterior debe ser un texto.',
        'passNew.required' => 'La nueva contraseña es obligatoria.',
        'passNew.string' => 'La nueva contraseña debe ser un texto.',
        'passNew.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
        'passConf.required' => 'La confirmación de la contraseña es obligatoria.',
        'passConf.string' => 'La confirmación de la contraseña debe ser un texto.',
        'passConf.same' => 'La confirmación de la contraseña no coincide con la nueva contraseña.',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }
    
    if (!Hash::check($request->passOld, $user->password)) {
        return response()->json(['error' => 'La contraseña anterior que ingresaste es incorrecta.'], 422);
    }

    $user->username = $request->username;
    $user->password = Hash::make($request->passNew);
    $user->save();
    
    return response()->json(['username' => $user->username], 200);
}


    /**
     * Show the specified Group menu
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/user/{id}",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the User",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User found",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */

    public function show(int $id)
    {

        $object = User::find($id);
        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'User not found'], 404
        );

    }

    /**
     * Remove the specified Group menu
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/user/{id}",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the User",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="User has option menus associated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User has option menus associated"
     *             )
     *         )
     *     )
     * )
     *
     */
    public function destroy(int $id)
    {
        $object = User::find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'User not found'], 404
            );
        }

        $object->delete();

    }

    public function searchByDni($dni)
    {

        $validator = Validator::make(['dni' => $dni], [
            'dni' => 'required|numeric|digits:8',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $respuesta = array();
        $client = new Client();
        try {
            $res = $client->get('http://facturae-garzasoft.com/facturacion/buscaCliente/BuscaCliente2.php?' . 'dni=' . $dni . '&fe=N&token=qusEj_w7aHEpX');

            if ($res->getStatusCode() == 200) { // 200 OK
                $response_data = $res->getBody()->getContents();
                $respuesta = json_decode($response_data);
                return response()->json([
                    $respuesta,
                ]);
            } else {
                return response()->json([
                    "status" => 0,
                    "msg" => "Server Error",
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => 0,
                "msg" => "Server Error: " . $e->getMessage(),
            ], 500);
        }
    }

    public function searchByRuc($ruc)
    {

        $validator = Validator::make(['ruc' => $ruc], [
            'ruc' => 'required|numeric|digits:11',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $respuesta = array();

        $client = new Client([
            'verify' => false,
        ]);
        $res = $client->get('https://comprobante-e.com/facturacion/buscaCliente/BuscaClienteRuc.php?fe=N&token=qusEj_w7aHEpX&' . 'ruc=' . $ruc);
        if ($res->getStatusCode() == 200) { // 200 OK
            $response_data = $res->getBody()->getContents();
            $respuesta = json_decode($response_data);
        } else {
            return response()->json([
                "status" => 0,
                "msg" => "Server error",
            ], 500);
        }
        return response()->json([
            $respuesta,
        ]);
    }
}

<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\Company;
use App\Models\GroupMenu;
use App\Models\Optionmenu;
use App\Models\TypeUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TypeUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('ensureTokenIsValid');
      
    }

    /**
     * Get all TypeUsers
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/typeUser",
     *     tags={"TypeUser"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active TypeUsers",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TypeUser")
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
        $groupMenu = GroupMenu::getFilteredGroupMenusSuperior($user->typeofUser_id);
        $groupMenuLeft = GroupMenu::getFilteredGroupMenus($user->typeofUser_id);

        $user = Auth::user();
        $typeUser = $user->typeUser;

        $accesses = $typeUser->getAccess($typeUser->id);

        $currentRoute = $request->path();
        $currentRouteParts = explode('/', $currentRoute);
        $lastPart = end($currentRouteParts);

       
        if (in_array($lastPart, $accesses)) {
            $groupMenu = GroupMenu::getFilteredGroupMenusSuperior($user->typeofUser_id);
            $groupMenuLeft = GroupMenu::getFilteredGroupMenus($user->typeofUser_id);

            return view('Modulos.Roles.index', compact('user', 'groupMenu', 'groupMenuLeft'));
        } else {
            abort(403, 'Acceso no autorizado.');
        }
    
    }

    public function allTypeUserAndCompanies()
    {

        $typeUsers = TypeUser::where('state', 1)
            ->whereNotIn('id', [1,2])
            ->get();

        $typeUsers->transform(function ($typeUser) {
            $typeUser->optionMenuAccess = $typeUser->getAccess($typeUser->id);
            return $typeUser;
        });

        $company = Company::get();
        $data = [
            "typeuser" => $typeUsers,
            "company" => $company,
        ];

        return response()->json($data);
    }
    public function all()
    {

        $typeUsers = TypeUser::where('state', 1)
            ->whereNotIn('id', [1, 2])
            ->simplePaginate();

        $typeUsers->transform(function ($typeUser) {
            $typeUser->optionMenuAccess = $typeUser->getAccess($typeUser->id);
            return $typeUser;
        });

   

        return response()->json($typeUsers);
    }

    /**
     * Show the specified TypeUser
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/typeUser/{id}",
     *     tags={"TypeUser"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the TypeUser",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TypeUser found",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="TypeUser not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="TypeUser not found"
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
        $typeUser = TypeUser::find($id);

        if (!$typeUser) {
            return response()->json(['message' => 'TypeUser not found'], 404);
        }

        $accesses = Access::with('optionMenu')
            ->where('typeuser_id', $id)->orderBy('id', 'desc')
            ->get()
            ->pluck('optionMenu')
            ->map(function ($option) {
                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'route' => $option->route,
                    'checked' => true, // Marcar como seleccionado
                ];
            });

        $allAccesses = Optionmenu::whereNotIn('id', $accesses->pluck('id')->toArray())->orderBy('id', 'desc')
            ->get()
            ->map(function ($option) {
                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'route' => $option->route,
                    'checked' => false, // Marcar como no seleccionado
                ];
            });

        $combinedAccesses = $accesses->merge($allAccesses);

        return response()->json(['data' => $combinedAccesses, 'name' => $typeUser->name], 200);
    }

    /**
     * Create a new Type User
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/typeUser",
     *     tags={"TypeUser"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="Admin"
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="New Type User created",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/TypeUser"
     *         )
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Unauthenticated"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                Rule::unique('type_users')->whereNull('deleted_at'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
        ];

        $object = TypeUser::create($data);
        $object = TypeUser::find($object->id);
        $object->optionMenuAccess = $object->getAccess($object->id);
        return response()->json($object, 200);
    }

    /**
     * Set Access for a Type User
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/typeUser/setAccess",
     *     tags={"TypeUser"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"typeUser_id", "accesses"},
     *              @OA\Property(
     *                  property="typeUser_id",
     *                  type="integer",
     *                  example=1
     *              ),
     *              @OA\Property(
     *                  property="accesses",
     *                  type="array",
     *                  @OA\Items(type="integer"),
     *                  example={1, 2, 3}
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Accesses set for Type User",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/TypeUser"
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
     *         response=404,
     *         description="TypeUser not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="TypeUser not found"
     *             )
     *         )
     *     )
     * )
     */
    public function setAccess(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'typeUser_id' => 'required|integer|exists:type_users,id',
            'accesses' => 'required|array',
            'accesses.*' => 'integer|exists:optionmenus,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $typeUserId = $request->input('typeUser_id');
        $accesses = $request->input('accesses');

        $typeUser = TypeUser::find($typeUserId);
        $typeUser->setAccess($typeUserId, $accesses);

        $typeUser->optionMenuAccess = $typeUser->getAccess($typeUser->id);
        return response()->json($typeUser, 200);
    }

    /**
     * Update the specified Type User
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/typeUser/{id}",
     *     tags={"TypeUser"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the TypeUser",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="Admin"
     *              ),
     *              @OA\Property(
     *                  property="icon",
     *                  type="string",
     *                  example="fas fa-user"
     *              ),

     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TypeUser updated",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/TypeUser"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="TypeUser not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="TypeUser not found"
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
    public function update(Request $request, int $id)
    {

        $object = TypeUser::find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'TypeUser not found'], 404
            );
        }
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                Rule::unique('type_users')->ignore($id)->whereNull('deleted_at'),
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
        ];

        $object->update($data);
        $object = TypeUser::find($object->id);
        return response()->json($object, 200);

    }

    /**
     * Remove the specified TypeUser
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/typeUser/{id}",
     *     tags={"TypeUser"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the TypeUser",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TypeUser deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="TypeUser deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="TypeUser not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="TypeUser not found"
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

     * )
     *
     */
    public function destroy(int $id)
    {
        $object = TypeUser::find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'TypeUser not found'], 404
            );
        }
        if (count($object->getAccess($id)) > 0) {
            return response()->json(
                ['message' => 'TypeUser has Access associated'], 409
            );
        }
        $object->state = 0;
        $object->save();
    }
}

<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\GroupMenu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
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

            return view('Modulos.Company.index', compact('user', 'groupMenu', 'groupMenuLeft'));
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

        $query = Company::where('state', 1)
            ->orderBy('id', 'desc');

        // Aplicar filtros por columna
        foreach ($request->get('columns') as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                $searchValue = trim($column['search']['value'], '()'); // Quitar paréntesis adicionales

                switch ($column['data']) {
                    case 'documentNumber':
                        $query->where('documentNumber', 'like', '%' . $searchValue . '%');
                        break;
                    case 'businessName':
                        $query->where('businessName', 'like', '%' . $searchValue . '%');
                        break;
                    case 'representativeName':
                        $query->where('representativeName', 'like', '%' . $searchValue . '%');
                        break;
                    case 'representativeDni':
                        $query->where('representativeDni', 'like', '%' . $searchValue . '%');
                        break;
                    case 'telephone':
                        $query->where('telephone', 'like', '%' . $searchValue . '%');
                        break;
                    case 'email':
                        $query->where('email', 'like', '%' . $searchValue . '%');
                        break;
                    case 'address':
                        $query->where('address', 'like', '%' . $searchValue . '%');
                        break;
                    case 'status':
                        $query->where('status', 'like', '%' . $searchValue . '%');
                        break;
                }
            }
        }

        $totalRecords = $query->count();

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

    public function store(StoreCompanyRequest $request)
    {
        // Validar los datos
        $validatedData = $request->validated();

        $validatedData['state'] = 1;
        $validatedData['typeOfDocument'] = 'RUC';

        $company = Company::create($validatedData);

        $hashedPassword = Hash::make($company->documentNumber);
        $data = [
            'username' => $company->documentNumber,
            'password' => $hashedPassword,
            'typeofUser_id' => 2,
            'company_id' => $company->id,
        ];
        $object = User::create($data);
        $object->createMensajeBase();
        $object->createGroupSend();

        return response()->json([
            'data' => $company,

        ], 201);
    }

    public function update(UpdateCompanyRequest $request, $id)
    {

        $company = Company::find($id);
        if (!$company) {
            return response()->json(
                ['message' => 'Empresa no Encontrada'], 404
            );
        }
        $validatedData = $request->validated();
        $validatedData['state'] = 1;
        $validatedData['typeOfDocument'] = 'RUC';

        $company->update($validatedData);

        return response()->json($company);
    }

    public function show($id)
    {
        $object = Company::findOrFail($id);

        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'Person not found'], 404
        );
    }
    public function destroy(int $id)
    {
        $company = Company::find($id);
        if (!$company) {
            return response()->json(
                ['message' => 'Group Menu not found'], 404
            );
        }
        if ($company->users()->count() > 0) {
            return response()->json(
                ['message' => 'Empresa ya tiene Usuarios'], 409
            );
        }
        $company->delete();

        return response()->json(
            ['message' => 'Group Menu deleted successfully']
        );

    }

}

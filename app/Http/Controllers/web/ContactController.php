<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Imports\PersonImport;
use App\Models\ContactByGroup;
use App\Models\GroupMenu;
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

    public function all(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 15);
        $filters = $request->input('filters', []);
        $company_id = Auth::user()->company_id;
    
        // Filtrar por company_id para asegurar que solo se obtengan los registros relacionados con esa compañía
        $query = ContactByGroup::with([
            'contact',
            'groupSend' => function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            }
        ])->whereHas('groupSend', function ($query) use ($company_id) {
            // Asegurar que el filtro company_id esté en todos los groupSend
            $query->where('company_id', $company_id);
        })->where('state', 1);
    
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
                      ->orderBy('id','desc')
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
}

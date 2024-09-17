<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\GroupMenu;
use App\Models\MessageWhasapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
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

        // $message = MessageWhasapp::where('responsable_id', $user->person_id)->first() ?? '';

        if (in_array($lastPart, $accesses)) {
            $groupMenu = GroupMenu::getFilteredGroupMenusSuperior($user->typeofUser_id);
            $groupMenuLeft = GroupMenu::getFilteredGroupMenus($user->typeofUser_id);

            return view('Modulos.Message.index', compact('user', 'groupMenu', 'groupMenuLeft'));
        } else {
            abort(403, 'Acceso no autorizado.');
        }
    }

    public function show($id)
    {
        $message = MessageWhasapp::find($id);
        if (!$message) {
            return response()->json(
                ['message' => 'Mensaje no Encontrado'], 404
            );
        }
        return response()->json($message, 200);

    }

    public function showExample($id)
    {
        $user = Auth::user();
        $message = MessageWhasapp::where('id', $id)->first() ?? (object) [
            'title' => 'titulo',
            'block1' => 'block1',
            'block2' => 'block2',
            'block3' => 'block3',
            'block4' => 'block4',
            'routeFile' => 'routeFile',
        ];

        $contact = [
            'names' => 'Miguel Guevara',
            'documentNumber' => '12345678',
            'telephone' => '987654321',
            'address' => 'Av. Principal 456, Lima',
            'concept' => 'Matrícula del semestre 2024',
            'amount' => 100.00,
            'dateReference' => '2024-09-01',
            'routeFile' => '/storage/document/documento.pdf',
        ];

        $tags = [
            '{{names}}',
            '{{documentNumber}}',
            '{{telephone}}',
            '{{address}}',
            '{{concept}}',
            '{{amount}}',
            '{{dateReference}}',

        ];

        $values = [
            $contact['names'], // {{nombreAlumno}}
            $contact['documentNumber'], // {{codigoAlumno}}
            $contact['telephone'], // {{telefono}}
            $contact['address'], // {{direccion}}
            $contact['concept'], // {{concepto}}
            $contact['amount'], // {{montoPago}}
            $contact['dateReference'], // {{fechaReferencia}}
            $contact['routeFile'], // {{rutaArchivo}}
        ];

        $blocks = [
            'title' => str_replace($tags, $values, $message->title),
            'block1' => str_replace($tags, $values, $message->block1),
            'block2' => str_replace($tags, $values, $message->block2),
            'block3' => str_replace($tags, $values, $message->block3),
            'block4' => str_replace($tags, $values, $message->block4),
        ];

        return response()->json($blocks);
    }

    public function all(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 15);
        $filters = $request->input('filters', []);

        $query = MessageWhasapp::where('state', 1)
            ->orderBy('id', 'desc');

        // Aplicar filtros por columna
        foreach ($request->get('columns') as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                $searchValue = trim($column['search']['value'], '()'); // Quitar paréntesis adicionales

                switch ($column['data']) {
                    case 'title':
                        $query->where('title', 'like', '%' . $searchValue . '%');
                        break;
                    case 'block1':
                        $query->where('block1', 'like', '%' . $searchValue . '%');
                        break;
                    case 'block2':
                        $query->where('block2', 'like', '%' . $searchValue . '%');
                        break;
                    case 'block3':
                        $query->where('block3', 'like', '%' . $searchValue . '%');
                        break;
                    case 'block4':
                        $query->where('block4', 'like', '%' . $searchValue . '%');
                        break;
                    case 'status':
                        $query->where('status', 'like', '%' . $searchValue . '%');
                        break;
                    case 'created_at':

                        $query->where('created_at', 'like', '%' . $searchValue . '%');

                        // Si la conversión falla, manejar el error según sea necesario

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

    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'title' => 'required|string|max:430',
            'block1' => 'required|string|max:430',
            'block2' => 'required|string|max:430',
            'block3' => 'required|string|max:430',
            'block4' => 'required|string|max:430',
            'fileUpload' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
        ], [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 430 caracteres.',
            'block1.required' => 'El párrafo 1 es obligatorio.',
            'block1.string' => 'El párrafo 1 debe ser una cadena de texto.',
            'block1.max' => 'El párrafo 1 no debe exceder los 430 caracteres.',
            'block2.required' => 'El párrafo 2 es obligatorio.',
            'block2.string' => 'El párrafo 2 debe ser una cadena de texto.',
            'block2.max' => 'El párrafo 2 no debe exceder los 430 caracteres.',
            'block3.required' => 'El párrafo 3 es obligatorio.',
            'block3.string' => 'El párrafo 3 debe ser una cadena de texto.',
            'block3.max' => 'El párrafo 3 no debe exceder los 430 caracteres.',
            'block4.required' => 'El párrafo 4 es obligatorio.',
            'block4.string' => 'El párrafo 4 debe ser una cadena de texto.',
            'block4.max' => 'El párrafo 4 no debe exceder los 430 caracteres.',
            'fileUpload.mimes' => 'El archivo debe ser un PDF, PNG, JPG o JPEG.',
            'fileUpload.max' => 'El archivo no debe ser mayor a 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        }

        $user = Auth::user();

        // Preparar los datos para actualizar o crear
        $messageData = [
            'title' => $request->input('title' ?? 'titulo'),
            'block1' => $request->input('block1' ?? 'block1'),
            'block2' => $request->input('block2' ?? 'block2'),
            'block3' => $request->input('block3' ?? 'block3'),
            'block4' => $request->input('block4' ?? 'block4'),
        ];

        // Validar etiquetas permitidas
        $allowedTags = [
            '{{names}}',
            '{{documentNumber}}',
            '{{telephone}}',
            '{{address}}',
            '{{concept}}',
            '{{amount}}',
            '{{dateReference}}',

        ];

        foreach ($messageData as $key => $value) {
            if (preg_match_all('/{{(.*?)}}/', $value, $matches)) {
                foreach ($matches[1] as $tag) {
                    if (!in_array('{{' . $tag . '}}', $allowedTags)) {
                        return response()->json(['error' => 'Etiqueta no permitida: ' . $tag], 422);
                    }
                }
            }
        }
        $messageData[] =
            ['company_id' => Auth::user()->company_id];
// Actualizar o crear el mensaje
        $compromiso = MessageWhasapp::create(
            $messageData
        );

        if ($request->hasFile('fileUpload')) {
            $file = $request->file('fileUpload');

            // Almacena el archivo en la carpeta 'uploads/messages' del disco 'public'
            $filePath = $file->store('uploads/messages', 'public');

            $compromiso->routeFile = 'storage/app/public/' . $filePath;
            $compromiso->save(); // Guarda los cambios en la base de datos
        }

        return response()->json($compromiso, 200);
    }

    public function update(Request $request, $id)
    {

        // Validación de datos
        $validator = validator()->make($request->all(), [
            'title' => 'required|string|max:430',
            'block1' => 'required|string|max:430',
            'block2' => 'required|string|max:430',
            'block3' => 'required|string|max:430',
            'block4' => 'required|string|max:430',
            'fileUpload' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
        ], [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 430 caracteres.',
            'block1.required' => 'El párrafo 1 es obligatorio.',
            'block1.string' => 'El párrafo 1 debe ser una cadena de texto.',
            'block1.max' => 'El párrafo 1 no debe exceder los 430 caracteres.',
            'block2.required' => 'El párrafo 2 es obligatorio.',
            'block2.string' => 'El párrafo 2 debe ser una cadena de texto.',
            'block2.max' => 'El párrafo 2 no debe exceder los 430 caracteres.',
            'block3.required' => 'El párrafo 3 es obligatorio.',
            'block3.string' => 'El párrafo 3 debe ser una cadena de texto.',
            'block3.max' => 'El párrafo 3 no debe exceder los 430 caracteres.',
            'block4.required' => 'El párrafo 4 es obligatorio.',
            'block4.string' => 'El párrafo 4 debe ser una cadena de texto.',
            'block4.max' => 'El párrafo 4 no debe exceder los 430 caracteres.',
            'fileUpload.mimes' => 'El archivo debe ser un PDF, PNG, JPG o JPEG.',
            'fileUpload.max' => 'El archivo no debe ser mayor a 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        }

        $message = MessageWhasapp::find($id);

        if (!$message) {
            return response()->json(['error' => 'Mensaje no encontrado.'], 404);
        }

        // Preparar los datos para actualizar
        $messageData = [
            'title' => $request->input('title'),
            'block1' => $request->input('block1'),
            'block2' => $request->input('block2'),
            'block3' => $request->input('block3'),
            'block4' => $request->input('block4'),
        ];

        // Validar etiquetas permitidas
        $allowedTags = [
            '{{names}}',
            '{{documentNumber}}',
            '{{telephone}}',
            '{{address}}',
            '{{concept}}',
            '{{amount}}',
            '{{dateReference}}',
        ];

        foreach ($messageData as $key => $value) {
            if (preg_match_all('/{{(.*?)}}/', $value, $matches)) {
                foreach ($matches[1] as $tag) {
                    if (!in_array('{{' . $tag . '}}', $allowedTags)) {
                        return response()->json(['error' => 'Etiqueta no permitida: ' . $tag], 422);
                    }
                }
            }
        }

        $filePath = $message->routeFile;

// Eliminar la parte 'public/' de la ruta si está al principio
        $filePath = preg_replace('/^.*public\//', '', $filePath);

        // Actualizar los datos del mensaje
        $message->update($messageData);

        // Manejar la subida de archivos
        if ($request->hasFile('fileUpload')) {
            // Eliminar el archivo anterior si existe
            if ($filePath && Storage::disk('public')->exists($filePath)) {

                Storage::disk('public')->delete($filePath);
            }

            $file = $request->file('fileUpload');

            // Almacena el archivo en la carpeta 'uploads/messages' del disco 'public'
            $filePath = $file->store('uploads/messages', 'public');

            // Guardar la ruta del archivo en la base de datos
            $message->routeFile = 'storage/app/public/' . $filePath;
        }

        // Guardar los cambios en la base de datos
        $message->save();

        return response()->json($message, 200);
    }

    public function destroy($id)
    {
        $message = MessageWhasapp::find($id);
        if (!$message) {
            return response()->json(
                ['message' => 'Mensaje no Encontrado'], 404
            );
        }

        // FALTA VALIDAR SI TIENE ENVIOS NO SE BORRE
        $message->state = 0;
        $message->save();
        return response()->json($message, 200);
    }

}

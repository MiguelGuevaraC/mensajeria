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

        $user = Auth::user();

        $query = MessageWhasapp::with(['user.company'])
            ->where('state', 1);

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

        // Ejecutar la consulta y obtener los resultados
        $messages = $query->orderBy('id', 'desc')->get();

        // Aplicar filtros por columna
        foreach ($request->get('columns') as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                $searchValue = trim($column['search']['value'], '()'); // Quitar paréntesis adicionales

                switch ($column['data']) {
                    case 'title':
                        $query->where('title', 'like', '%' . $searchValue . '%');
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
    public function countSpecialChars($text)
    {
        $specialChars = [
            'á' => 5, 'é' => 5, 'í' => 5, 'ó' => 5, 'ú' => 5,
            'ü' => 5, 'ñ' => 5,
            'Á' => 5, 'É' => 5, 'Í' => 5, 'Ó' => 5, 'Ú' => 5,
            'Ü' => 5, 'Ñ' => 5,
            '/' => 3, '\\' => 3,
        ];

        // Mapa de etiquetas y su peso en caracteres
        $tagsCount = [
            '{{names}}' => 40,
            '{{documentNumber}}' => 12,
            '{{telephone}}' => 9,
            '{{address}}' => 50,
            '{{concept}}' => 30,
            '{{amount}}' => 7,
            '{{dateReference}}' => 10,
        ];

        $length = mb_strlen($text); // Largo total del texto
        $specialCount = 0;

        // Contar caracteres especiales
        foreach (preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) as $char) {
            if (isset($specialChars[$char])) {
                $specialCount += $specialChars[$char] - 1; // Restar 1 porque ya cuenta como 1
            }
        }

        // Contar etiquetas y sumar caracteres según corresponda
        foreach ($tagsCount as $tag => $count) {
            $occurrences = substr_count($text, $tag);
            $specialCount += $occurrences * ($count - mb_strlen($tag)); // Restar longitud real de la etiqueta
        }

        return $length + $specialCount;
    }
    public function store(Request $request)
    {
        // Función para contar caracteres teniendo en cuenta caracteres especiales y etiquetas

        // Validar la solicitud
        $validator = validator()->make($request->all(), [
            'title' => 'required|string|max:65',
            'block1' => 'required|string|max:400',
            'block2' => 'required|string|max:400',
            'block3' => 'required|string|max:400',
            'block4' => 'required|string|max:400',
            'fileUpload' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
        ], [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 65 caracteres.',
            'block1.required' => 'El párrafo 1 es obligatorio.',
            'block1.string' => 'El párrafo 1 debe ser una cadena de texto.',
            'block1.max' => 'El párrafo 1 no debe exceder los 400 caracteres.',
            'block2.required' => 'El párrafo 2 es obligatorio.',
            'block2.string' => 'El párrafo 2 debe ser una cadena de texto.',
            'block2.max' => 'El párrafo 2 no debe exceder los 400 caracteres.',
            'block3.required' => 'El párrafo 3 es obligatorio.',
            'block3.string' => 'El párrafo 3 debe ser una cadena de texto.',
            'block3.max' => 'El párrafo 3 no debe exceder los 400 caracteres.',
            'block4.required' => 'El párrafo 4 es obligatorio.',
            'block4.string' => 'El párrafo 4 debe ser una cadena de texto.',
            'block4.max' => 'El párrafo 4 no debe exceder los 400 caracteres.',
            'fileUpload.mimes' => 'El archivo debe ser un PDF, PNG, JPG o JPEG.',
            'fileUpload.max' => 'El archivo no debe ser mayor a 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        }

        // Longitudes de cada párrafo
        $lengthParagraph1 = $this->countSpecialChars($request->input('block1', ''));
        $lengthParagraph2 = $this->countSpecialChars($request->input('block2', ''));
        $lengthParagraph3 = $this->countSpecialChars($request->input('block3', ''));
        $lengthParagraph4 = $this->countSpecialChars($request->input('block4', ''));

        // Sumar las longitudes de los cuatro párrafos
        $totalLength = $lengthParagraph1 + $lengthParagraph2 + $lengthParagraph3 + $lengthParagraph4;

        if ($totalLength > 900) {
            $excess = $totalLength - 900;

            // Identificar cuál párrafo reducir
            $longestParagraph = max($lengthParagraph1, $lengthParagraph2, $lengthParagraph3, $lengthParagraph4);
            $longestParagraphName = '';
            if ($longestParagraph == $lengthParagraph1) {
                $longestParagraphName = 'párrafo 1';
            }

            if ($longestParagraph == $lengthParagraph2) {
                $longestParagraphName = 'párrafo 2';
            }

            if ($longestParagraph == $lengthParagraph3) {
                $longestParagraphName = 'párrafo 3';
            }

            if ($longestParagraph == $lengthParagraph4) {
                $longestParagraphName = 'párrafo 4';
            }

            return response()->json([
                'error' => 'La suma de los 4 párrafos es: ' . $totalLength . ' caracteres, excede los 900 caracteres por ' . $excess . ' caracteres. Considera reducir el ' . $longestParagraphName . '.',
                'totalLength' => $totalLength,
                'excess' => $excess,
                'lengthParagraph1' => $lengthParagraph1,
                'lengthParagraph2' => $lengthParagraph2,
                'lengthParagraph3' => $lengthParagraph3,
                'lengthParagraph4' => $lengthParagraph4,
                'longestParagraph' => $longestParagraphName,
            ], 422);
        }

        // Preparar los datos para actualizar o crear
        $user = Auth::user();
        $messageData = [
            'title' => $request->input('title', 'Título por defecto'),
            'block1' => $request->input('block1', 'Bloque 1 por defecto'),
            'block2' => $request->input('block2', 'Bloque 2 por defecto'),
            'block3' => $request->input('block3', 'Bloque 3 por defecto'),
            'block4' => $request->input('block4', 'Bloque 4 por defecto'),
            'user_id' => $user->id,
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

        // Actualizar o crear el mensaje
        $message = MessageWhasapp::create($messageData);

        // Manejo de archivo
        if ($request->hasFile('fileUpload')) {
            $file = $request->file('fileUpload');
            $filePath = $file->store('uploads/messages', 'public');
            $message->routeFile = 'storage/app/public/' . $filePath;
            $message->save();
        }

        return response()->json($message, 200);
    }

    public function update(Request $request, $id)
    {
        // Función para contar caracteres teniendo en cuenta caracteres especiales y etiquetas

        // Validar la solicitud
        $validator = validator()->make($request->all(), [
            'title' => 'required|string|max:65',
            'block1' => 'required|string|max:400',
            'block2' => 'required|string|max:400',
            'block3' => 'required|string|max:400',
            'block4' => 'required|string|max:400',
            'fileUpload' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
        ], [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 65 caracteres.',
            'block1.required' => 'El párrafo 1 es obligatorio.',
            'block1.string' => 'El párrafo 1 debe ser una cadena de texto.',
            'block1.max' => 'El párrafo 1 no debe exceder los 400 caracteres.',
            'block2.required' => 'El párrafo 2 es obligatorio.',
            'block2.string' => 'El párrafo 2 debe ser una cadena de texto.',
            'block2.max' => 'El párrafo 2 no debe exceder los 400 caracteres.',
            'block3.required' => 'El párrafo 3 es obligatorio.',
            'block3.string' => 'El párrafo 3 debe ser una cadena de texto.',
            'block3.max' => 'El párrafo 3 no debe exceder los 400 caracteres.',
            'block4.required' => 'El párrafo 4 es obligatorio.',
            'block4.string' => 'El párrafo 4 debe ser una cadena de texto.',
            'block4.max' => 'El párrafo 4 no debe exceder los 400 caracteres.',
            'fileUpload.mimes' => 'El archivo debe ser un PDF, PNG, JPG o JPEG.',
            'fileUpload.max' => 'El archivo no debe ser mayor a 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        }

        // Longitudes de cada párrafo
        $lengthParagraph1 = $this->countSpecialChars($request->input('block1', ''));
        $lengthParagraph2 = $this->countSpecialChars($request->input('block2', ''));
        $lengthParagraph3 = $this->countSpecialChars($request->input('block3', ''));
        $lengthParagraph4 = $this->countSpecialChars($request->input('block4', ''));

        // Sumar las longitudes de los cuatro párrafos
        $totalLength = $lengthParagraph1 + $lengthParagraph2 + $lengthParagraph3 + $lengthParagraph4;

        if ($totalLength > 900) {
            $excess = $totalLength - 900;

            // Identificar cuál párrafo reducir
            $longestParagraph = max($lengthParagraph1, $lengthParagraph2, $lengthParagraph3, $lengthParagraph4);
            $longestParagraphName = '';
            if ($longestParagraph == $lengthParagraph1) {
                $longestParagraphName = 'párrafo 1';
            }

            if ($longestParagraph == $lengthParagraph2) {
                $longestParagraphName = 'párrafo 2';
            }

            if ($longestParagraph == $lengthParagraph3) {
                $longestParagraphName = 'párrafo 3';
            }

            if ($longestParagraph == $lengthParagraph4) {
                $longestParagraphName = 'párrafo 4';
            }

            return response()->json([
                'error' => 'La suma de los 4 párrafos es: ' . $totalLength . ' caracteres, excede los 900 caracteres por ' . $excess . ' caracteres. Considera reducir el ' . $longestParagraphName . '.',
                'totalLength' => $totalLength,
                'excess' => $excess,
                'lengthParagraph1' => $lengthParagraph1,
                'lengthParagraph2' => $lengthParagraph2,
                'lengthParagraph3' => $lengthParagraph3,
                'lengthParagraph4' => $lengthParagraph4,
                'longestParagraph' => $longestParagraphName,
            ], 422);
        }

        // Obtener el mensaje existente
        $message = MessageWhasapp::findOrFail($id);

        // Preparar los datos para actualizar
        $user = Auth::user();
        $messageData = [
            'title' => $request->input('title', $message->title),
            'block1' => $request->input('block1', $message->block1),
            'block2' => $request->input('block2', $message->block2),
            'block3' => $request->input('block3', $message->block3),
            'block4' => $request->input('block4', $message->block4),
            'user_id' => $user->id,
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

        // Actualizar el mensaje
        $message->update($messageData);
        $filePath = $message->routeFile;

        $filePath = preg_replace('/^.*public\//', '', $filePath);

        // Manejo de archivo
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

        $message->updateMessageDataInProgramming();

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

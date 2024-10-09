<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsappJob;
use App\Models\Programming;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SendWhatsappMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:whatsapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar mensaje de WhatsApp basado en la programación';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Obtener la fecha actual
        $fechaActual = Carbon::now();
   
        $typeSend='Programada';

        $programaciones = Programming::where('dateProgram', '<=', $fechaActual)
            ->where('status', 'Pendiente') // Puedes ajustar el estado según lo necesites
            ->get();

        foreach ($programaciones as $programming) {
            // Obtenemos los contactos relacionados a la programación
            $contactsByGroups = $programming->contactsByGroup;
            $message_id= $programming->messageWhasapp_id;
            $user = User::find($programming->user_id);
            $company_id = $user->company_id;
            $user_id = $user->id;

            if ($contactsByGroups->isEmpty()) {
                Log::warning('No se encontraron contactos marcados', ['user_id' => $user_id]);
                return response()->json(['error' => 'No se encontraron contactos marcados'], 422);
            }

            $contactByGroupPaquete = [];
            $jobResponses = []; // Array para almacenar las respuestas de cada job

            $totalEnviados = 0;
            $totalExitosos = 0;
            $totalErrores = 0;

            foreach ($contactsByGroups as $contactByGroup) {
                $contact = $contactByGroup->contact;

                // Verificar que el contacto tenga un número de teléfono
                if ($contact && $contact->telephone) {
                    $contactByGroupPaquete[] = $contactByGroup;
                    Log::info('Contacto agregado', ['contact_id' => $contact->id]);
                }

                // Si el paquete de contactos alcanza los 50, despacha un job y almacena la respuesta
                if (count($contactByGroupPaquete) >= 50) {
                    $response = SendWhatsappJob::dispatchNow($contactByGroupPaquete, $user, $message_id, $typeSend, $programming->id); // Usamos dispatchNow para obtener la respuesta directamente
                    $jobResponses[] = $response->original; // Almacenar la respuesta del job
                    $totalEnviados += $response->original['quantitySend'];
                    $totalExitosos += $response->original['success'];
                    $totalErrores += $response->original['errors'];
                    Log::info('Enviando paquete de mensajes', ['cantidad' => count($contactByGroupPaquete)]);
                    $contactByGroupPaquete = []; // Limpiar el paquete
                }
            }

            // Si queda algún paquete menor a 50, también despacharlo y almacenar la respuesta
            if (count($contactByGroupPaquete) > 0) {
                $response = SendWhatsappJob::dispatchNow($contactByGroupPaquete, $user, $message_id, $typeSend, $programming->id);
                $jobResponses[] = $response->original; // Almacenar la respuesta del job
                $totalEnviados += $response->original['quantitySend'];
                $totalExitosos += $response->original['success'];
                $totalErrores += $response->original['errors'];
                Log::info('Enviando último paquete de mensajes', ['cantidad' => count($contactByGroupPaquete)]);
            }

            $programming->quantitySend = $totalEnviados;
            $programming->errors = $totalErrores;
            $programming->success = $totalExitosos;
            $programming->dateSend = $fechaActual;

            $programming->save();

        }
    }

}

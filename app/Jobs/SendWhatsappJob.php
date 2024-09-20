<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\MessageWhasapp;
use App\Models\SendApi;
use App\Models\User;
use App\Models\WhatsappSend;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $contactsByGroups;
    protected $user;
    protected $message_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contactsByGroups, $user, $message_id)
    {
        $this->contactsByGroups = $contactsByGroups;
        $this->user = $user;
        $this->message_id = $message_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        try {
            $mensajes = [];
            $user = $this->user;
            $costSend = $this->user->company->costSend ?? '0';
            $companyTradeName = $this->user->company->tradeName ?? '';
            $companyName = $this->user->company->documentNumber . '-' . $this->user->company->businessName;

            $messageBase = MessageWhasapp::where('id', $this->message_id)->first() ?? (object) [
                'title' => 'titulo',
                'block1' => 'block1',
                'block2' => 'block2',
                'block3' => 'block3',
            ];

            $whatsappSends = []; // Array para almacenar los envíos realizados

            foreach ($this->contactsByGroups as $contactByGroup) {

                $contact = Contact::find($contactByGroup->contact_id);

                $cadenaNombres = $contact->names ?? '';
                $documentNumber = $contact->documentNumber ?? '';
                $telephoneStudent = $contact->telephone ?? '903017426';
                $address = $contact->address ?? '';
                $concept = $contact->concept ?? '';
                $amount = $contact->amount ?? 0;
                $dateReference = $contact->dateReference ?? null;

                $tags = [
                    '{{names}}',
                    '{{documentNumber}}',
                    '{{telephone}}',
                    '{{address}}',
                    '{{concept}}',
                    '{{amount}}',
                    '{{dateReference}}',
                ];
                $values = [$cadenaNombres,
                    $documentNumber,
                    $telephoneStudent,
                    $address,
                    $concept,
                    $amount,
                    $dateReference];

                $title = str_replace($tags, $values, $messageBase->title);

                // Elimina caracteres especiales de los bloques
                $specialChars = ["\n", "\r", "\t", "\\"];
                $block1 = str_replace($specialChars, '', str_replace($tags, $values, $messageBase->block1));
                $block2 = str_replace($specialChars, '', str_replace($tags, $values, $messageBase->block2));
                $block3 = str_replace($specialChars, '', str_replace($tags, $values, $messageBase->block3));
                $block4 = str_replace($specialChars, '', str_replace($tags, $values, $messageBase->block4));

                $mensajes[] = [
                    "cellphone_number" => $telephoneStudent,
                    "title" => $title,
                    "content" => [$block1, $block2, $block3, $block4],
                ];
                $user_id = $this->user->id;
                $tipo = 'E' . str_pad($user_id, 3, '0', STR_PAD_LEFT); // Rellenar con ceros a la izquierda
                $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(sequentialNumber, LOCATE("-", sequentialNumber) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM whatsapp_sends a WHERE SUBSTRING(sequentialNumber, 1, 4) = ?', [$tipo])[0]->siguienteNum;
                $siguienteNum = (int) $resultado;

                $data = [
                    'sequentialNumber' => $tipo . "-" . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
                    'messageSend' => $title . "\n\n" . $block1 . "\n\n" . $block2 . "\n\n" . $block3 . "\n\n" . $block4,
                    'userResponsability' => $user_id . '-' . $this->user->username,
                    'namesPerson' => $cadenaNombres ?? '',
                    'bussinesName' => $companyName ?? '',
                    'trade_name' => $companyTradeName ?? '',
                    'documentNumber' => $documentNumber ?? '',
                    'telephone' => $telephoneStudent,
                    'amount' => $amount,
                    'costSend' => $costSend,
                    'routeFile' => $messageBase->routeFile,
                    'contac_id' => $contact->id,
                    'user_id' => $user->id,
                    'messageWhasapp_id' => $messageBase->id,

                ];

                $messageSend = WhatsappSend::create($data);
                $whatsappSends[$telephoneStudent][] = $messageSend; // Guardar el envío por número de teléfono
            }

            // Llamada a la API
            $url = 'https://sistema.gesrest.net/api/send-massive-wa-messages';

            $response = Http::withHeaders([
                'Authorization' => '}*rA3>#pyM<dITk]]DFP2,/wc)1md_Y/',
            ])->timeout(120)->post($url, [
                "messages" => $mensajes,
            ]);

            $data = [
                'quantitySend' => null,
                'errors' => null,
                'success' => null,
                'dateSend' => now(),
                'user_id' => $user->id,

            ];

            $sendApi = SendApi::create($data);
            $totalErrors = 0;
            $totalSuccess = 0;
            $totalSend = 0;

            if ($response->successful()) {
                $responseData = $response->json();

                foreach ($responseData['messages'] as $phone => $status) {
                    if (isset($whatsappSends[$phone])) {
                        foreach ($whatsappSends[$phone] as $messageSend) {
                            $messageSend->status = $status === 'success' ? 'Envio Exitoso' : 'Envio Fallido';
                            $messageSend->sendApi_id = $sendApi->id;
                            $messageSend->save();
                            $totalSuccess++;
                            $totalSend++;
                        }
                    }
                }
                $jsonMensajes = json_encode($mensajes);
                Log::info($jsonMensajes);
                Log::info($response);
                Log::info('WhatsApp messages sent successfully');
            } else {
                foreach ($whatsappSends as $phone => $messages) {
                    foreach ($messages as $messageSend) {
                        $messageSend->status = 'Envio Fallido';
                        $messageSend->sendApi_id = $sendApi->id;
                        $messageSend->save();
                        
                        $totalErrors++;
                        $totalSend++;
                    }
                }
                Log::info($response);
                Log::error('Failed to send WhatsApp messages. Response: ' . $response->body());
            }

            $sendApi->quantitySend = $totalSend;
            $sendApi->errors = $totalErrors;
            $sendApi->success = $totalSuccess;
            $sendApi->save();

            return response()->json(['message' => 'El mensaje de WhatsApp se ha enviado correctamente'], 200);
        } catch (Exception $e) {
            Log::error('Error al enviar WhatsApp: ' . $e->getMessage());
            return response()->json(['error' => 'Hubo un error al enviar el mensaje de WhatsApp'], 500);
        }
    }

}

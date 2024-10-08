<?php

namespace App\Console\Commands;

use App\Models\Programming;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
    
        // Buscar todas las programaciones con fecha menor o igual a la fecha actual y con estado "pendiente"
        $programaciones = Programming::where('dateProgram', '<=', $fechaActual)
                                     ->where('status', 'pendiente') // Puedes ajustar el estado según lo necesites
                                     ->get();
    
        // Verificar si hay programaciones para enviar
        if ($programaciones->isEmpty()) {
            $this->info('No hay programaciones pendientes para enviar.');
            return;
        }
    
        foreach ($programaciones as $programacion) {
   
            $programacion->status = "ENVIADO";
            $programacion->dateSend = Carbon::now(); // Guarda la fecha de envío
            $programacion->save();
    
            $this->info('Mensaje de WhatsApp enviado para la programación ID: ' . $programacion->id);
        }
    }
    
    
}

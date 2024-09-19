<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageWhatsappSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('message_whasapps')->insert([
            'title' => 'Invitación al Programa de Desarrollo de Habilidades',
            'block1' => 'Mediante la presente comunicamos que han sido seleccionados desde la cátedra de Aplicaciones de Negocios Electrónicos semestre 2024I-UNPRG',
            'block2' => 'Motivo por el cual los invitamos a participar en el Programa de Desarrollo de Habilidades de nuestra empresa GarzasoftLa sesión de presentación se llevará a cabo el martes 17/setiembre a horas 11am vía Google MeetCon una duración de 30 minutos Agenda',
            'block3' => 'Objetivo del programaMétodo de aprendizajeTecnología de desarrollo de Garzasoft por perfil : Back-End / Front-EndCoordinar próxima reunión',
            'block4' => 'Saludos Martín Ampuero P. | GerenteGarzasoft EIRL (Perú) | Nicolás la Torre 126 - Urb. Magisterial -Chiclayo',
            'routeFile' => '/storage/documents/document.pdf',
            'state' => true,
            'company_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('message_whasapps')->insert([
            'title' => 'Invitación al Programa de Desarrollo de Habilidades - Backend',
            'block1' => 'Mediante la presente comunicamos, que la primera reunión de desarrollo de habilidades backend, se llevará a cabo el 19 de septiembre a las 11:00 a.m',
            'block2' => 'La reunión será virtual, y puede unirse a través del siguiente enlace: https://meet.google.com/tcj-jkdh-ymd.',
            'block3' => 'Objetivo de reunión: Iniciar con el desarrollo de habilidades en PHP.',
            'block4' => 'Saludos Martín Ampuero P. | GerenteGarzasoft EIRL (Perú) | Nicolás la Torre 126 - Urb. Magisterial -Chiclayo',
            'routeFile' => '/storage/documents/document.pdf',
            'state' => true,
            'company_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
    }
}

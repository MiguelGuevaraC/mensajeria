<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = [
            $data = [
                'businessName' => 'EMPRESA DEMO',
                'documentNumber' => '11111111111',
                'tradeName' => 'Empresa DEMO',
                'representativeName' => 'Demo',
                'telephone' => '999999999',
                'email' => 'demo@gmail.com',
                'address' => 'Dirección Demo',
                'costSend' => 0.0,
            ],
            
            [
                'businessName' => 'GARZASOFT E.I.R.L.',
                'documentNumber' => '20602871119',
                'tradeName' => 'Mr Soft',
                'representativeName' => 'Martin Ampuero',
                'telephone' => '979293176',
                'email' => 'martin.ampuero@garzasoft.com',
                'address' => 'CAL. NICOLAS LA TORRE NRO. 126 URB. MAGISTERIAL LAMBAYEQUE - CHICLAYO - CHICLAYO',
                'costSend' => 0.0,
            ],

        ];

        // Insertar los datos en la base de datos
        DB::table('companies')->insert($companies);
    }
}

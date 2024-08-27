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
            [
                'businessName' => 'GARZASOFT E.I.R.L.',
                'documentNumber' => '20602871119',
                'tradeName' => 'Mr Soft', 
                'representativeName' => 'Martin Ampuero',
                'telephone' => '979293176',
                'email' => 'martin.ampuero@garzasoft.com',
                'address' => 'CAL. NICOLAS LA TORRE NRO. 126 URB. MAGISTERIAL LAMBAYEQUE - CHICLAYO - CHICLAYO',
            ],
            
        ];

        // Insertar los datos en la base de datos
        DB::table('companies')->insert($companies);
    }
}

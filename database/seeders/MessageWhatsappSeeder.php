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

            'title' => 'TITULO',
            'block1' => 'BLOQUE 1',
            'block2' => 'BLOQUE 2',
            'block3' => 'BLOQUE 3',
            'block4' => 'BLOQUE 4',
            'state' => true,
            'company_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

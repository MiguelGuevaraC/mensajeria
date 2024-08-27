<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Person;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {



    

        $this->call(GroupMenuSeeder::class);
        $this->call(TypeUserSeeder::class);
        $this->call(CompaniesTableSeeder::class);

        $this->call(OptionMenuSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(AccessSeeder::class);
        $this->call(MessageWhatsappSeeder::class);
        $this->call(WhatsappSendSeeder::class);
    }
}

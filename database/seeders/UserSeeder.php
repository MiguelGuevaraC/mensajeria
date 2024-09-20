<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            ['id' => '1', 'username' => 'PRUEBADEMO', 'password' => 'PRUEBADEMO', 'company_id' => 1, 'typeofUser_id' => '2'],
            ['id' => '2', 'username' => 'administrador', 'password' => 'adminBack2024', 'company_id' => 1, 'typeofUser_id' => '1'],
            ['id' => '3', 'username' => '20602871119', 'password' => '20602871119', 'company_id' => 1, 'typeofUser_id' => '2'],
        ];

        foreach ($users as $user) {
            // Buscar el registro por su ID
            $user1 = User::find($user['id']);

            // Hashear la contraseña
            $user['password'] = Hash::make($user['password']);

            // Si el usuario existe, actualizarlo; de lo contrario, crear uno nuevo
            if ($user1) {
                $object=$user1->update($user);
            } else {
                $object=User::create($user);
            }

            $object->createMensajeBase();
            $object->createGroupSend();
        }
    }
}

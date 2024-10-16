<?php

namespace Database\Seeders;

use App\Models\Optionmenu;
use Illuminate\Database\Seeder;

class OptionMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ['id' => '1', 'name' => 'Mensajes', 'route' => 'message', 'groupmenu_id' => 1, 'icon' => 'fa fa-comments'],
            ['id' => '2', 'name' => 'Grupos', 'route' => 'groupSend', 'groupmenu_id' => 1, 'icon' => 'fa-solid fa-users-rectangle'],
            ['id' => '3', 'name' => 'Contactos', 'route' => 'contacts', 'groupmenu_id' => 1, 'icon' => 'fa-solid fa-id-badge'],
            ['id' => '4', 'name' => 'Empresas', 'route' => 'company', 'groupmenu_id' => 1, 'icon' => 'fa-solid fa-building'],

            ['id' => '5', 'name' => 'Envíos', 'route' => 'send-report', 'groupmenu_id' => 2, 'icon' => 'fa-brands fa-whatsapp'],
            
            ['id' => '6', 'name' => 'Dashboard', 'route' => 'dashboard', 'groupmenu_id' => 3, 'icon' => 'fa fa-chart-line'],
            
            ['id' => '7', 'name' => 'Gestión de Accesos', 'route' => 'access', 'groupmenu_id' => 4, 'icon' => 'fa fa-key'],
            ['id' => '8', 'name' => 'Usuarios', 'route' => 'user', 'groupmenu_id' => 4, 'icon' => 'fa fa-user-cog'],
            ['id' => '9', 'name' => 'Inicio', 'route' => 'vistaInicio', 'groupmenu_id' => 1, 'icon' => 'fa fa-user-cog'],
            ['id' => '10', 'name' => 'Perfil', 'route' => 'perfilD', 'groupmenu_id' => 4, 'icon' => 'fa fa-user-cog'],
            ['id' => '11', 'name' => 'Programaciones', 'route' => 'programming', 'groupmenu_id' => 2, 'icon' => 'fa-solid fa-calendar-plus'],

        ];
        

        foreach ($array as $object) {
            $option = Optionmenu::find($object['id']);
            if ($option) {
                $option->update($object);
            } else {
                Optionmenu::create($object);
            }
        }
    }
}

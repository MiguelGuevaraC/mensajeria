<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WhatsappSend>
 */
class WhatsappSendFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'sequentialNumber' => $this->faker->numberBetween(1, 1000),
            'messageSend' => $this->faker->sentence,
            'userResponsability' => $this->faker->name,
            'namesPerson' => $this->faker->name,
            'bussinesName' => $this->faker->company,
            'trade_name' => $this->faker->word,
            // 'documentNumber' => $this->faker->numberBetween(10000000, 99999999),
            'telephone' => $this->faker->phoneNumber,
            'amount' => $this->faker->randomFloat(2, 0, 500),
            'costSend' => '0.6',
            'concept' => $this->faker->word,
            'routeFile' => $this->faker->fileExtension,
            'status' => 'Activo',
            'created_at' => $this->faker->dateTimeBetween('-12 months', 'now'),
            'updated_at' => now(),
            'contac_id' => $this->faker->numberBetween(1, 6),
            'user_id' => 2,
            'sendApi_id' => null,
            'messageWhasapp_id' => 1,
        ];
    }
}

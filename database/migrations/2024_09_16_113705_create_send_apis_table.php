<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('send_apis', function (Blueprint $table) {
            $table->id(); // ID automático
            $table->integer('quantitySend')->nullable(); // Cantidad enviada
            $table->integer('errors')->nullable(); // Errores, puede ser nulo
            $table->integer('success')->nullable(); // Éxitos, puede ser nulo
            $table->date('dateSend')->nullable(); // Fecha de envío
            $table->foreignId('user_id')->nullable()->unsigned()->constrained('users');
            
            $table->boolean('state')->nullable()->default(1); // Campo 'state'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('send_apis');
    }
};

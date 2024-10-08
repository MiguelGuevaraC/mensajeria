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
        Schema::create('programmings', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable()->default("Pendiente"); // Campo 'type'
            $table->dateTime('dateProgram')->nullable(); // Campo 'dataReference'
            $table->dateTime('dateSend')->nullable(); // Fecha de envío
            $table->foreignId('user_id')->nullable()->unsigned()->constrained('users');
            
            $table->boolean('state')->nullable()->default(1); // Campo 'state'
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programmings');
    }
};

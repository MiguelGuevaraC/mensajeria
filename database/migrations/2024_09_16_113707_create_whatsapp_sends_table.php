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
        Schema::create('whatsapp_sends', function (Blueprint $table) {
            $table->id(); 
            $table->string('sequentialNumber')->nullable(); 
            $table->text('messageSend')->nullable(); 
            $table->string('userResponsability')->nullable(); 
            $table->string('namesPerson')->nullable();
            $table->string('bussinesName')->nullable(); 
            $table->string('trade_name')->nullable(); 
            $table->string('documentNumber')->nullable(); 
            $table->string('telephone')->nullable();
            $table->decimal('amount', 8, 2)->nullable(); 
            $table->decimal('costSend', 8, 2)->nullable(); 
            $table->string('concept')->nullable(); 
            $table->string('routeFile')->nullable()->nullable(); 
            $table->string('status')->nullable(); 

            $table->foreignId('contac_id')->nullable()->unsigned()->constrained('contacts');
            $table->foreignId('user_id')->nullable()->unsigned()->constrained('users');
            $table->foreignId('messageWhasapp_id')->nullable()->unsigned()->constrained('message_whasapps');
            $table->foreignId('sendApi_id')->nullable()->unsigned()->constrained('send_apis');
            $table->foreignId('detailProgramming_id')->nullable()->unsigned()->constrained('detail_programmings');

            // Timestamps (created_at, updated_at)
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
        Schema::dropIfExists('whatsapp_sends');
    }
};

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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('typeofDocument', 200)->nullable();
            $table->string('documentNumber', 200)->nullable();
            $table->string('businessName', 200)->nullable();
            $table->string('tradeName', 200)->nullable();
            $table->string('representativeName', 200)->nullable();
            $table->string('representativeDni', 200)->nullable();
            $table->string('telephone', 200)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('address', 200)->nullable();
            $table->decimal('costSend', 8, 2)->nullable(); 
            
            $table->string('status', 200)->nullable();
            $table->boolean('state')->default(true);

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
        Schema::dropIfExists('companies');
    }
};

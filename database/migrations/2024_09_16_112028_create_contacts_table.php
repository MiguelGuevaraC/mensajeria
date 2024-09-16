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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id(); // Campo 'id'
            $table->string('documentNumber')->nullable(); // Campo 'documentNumber'
            $table->string('names')->nullable(); // Campo 'names'
            $table->string('telephone')->nullable(); // Campo 'telephone'
            $table->string('address')->nullable(); // Campo 'address'
            $table->string('concept')->nullable(); // Campo 'concept'
            $table->decimal('amount', 8, 2)->nullable(); // Campo 'amount'
            $table->date('dateReference')->nullable(); // Campo 'dataReference'
            $table->string('routeFile')->nullable()->nullable(); // Campo 'routeFile'

            // Campos adicionales
        
            $table->boolean('state')->nullable(); // Campo 'state'
            $table->string('status')->nullable(); // Campo 'status'

            $table->foreignId('migration_id')->nullable()->unsigned()->constrained('migration_exports');
            $table->foreignId('groupSend_id')->nullable()->unsigned()->constrained('group_sends');
       
            $table->timestamps(); // Campos 'created_at' y 'updated_at'
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
        Schema::dropIfExists('contacts');
    }
};

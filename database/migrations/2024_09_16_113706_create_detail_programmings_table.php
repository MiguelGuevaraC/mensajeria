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
        Schema::create('detail_programmings', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable()->default("Pendiente"); // Campo 'type'

            $table->string('documentNumber')->nullable(); // Campo 'documentNumber'
            $table->string('names')->nullable(); // Campo 'names'
            $table->string('telephone')->nullable(); // Campo 'telephone'
            $table->string('address')->nullable(); // Campo 'address'
            $table->string('concept')->nullable(); // Campo 'concept'
            $table->decimal('amount', 8, 2)->nullable(); // Campo 'amount'
            $table->date('dateReference')->nullable(); // Campo 'dataReference'
            $table->string('routeFile')->nullable()->nullable(); // Campo 'routeFile'
            
            $table->foreignId('programming_id')->nullable()->unsigned()->constrained('programmings');
            $table->foreignId('contactByGroup_id')->nullable()->unsigned()->constrained('contact_by_groups');

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
        Schema::dropIfExists('detail_programmings');
    }
};

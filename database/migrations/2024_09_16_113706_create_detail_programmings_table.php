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

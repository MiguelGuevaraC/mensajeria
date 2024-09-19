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
        Schema::create('contact_by_groups', function (Blueprint $table) {
            $table->id();
            $table->boolean('state')->nullable(); // Campo 'state'

            $table->foreignId('contact_id')->nullable()->unsigned()->constrained('contacts');
            $table->foreignId('groupSend_id')->nullable()->unsigned()->constrained('group_sends');

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
        Schema::dropIfExists('contact_by_groups');
    }
};

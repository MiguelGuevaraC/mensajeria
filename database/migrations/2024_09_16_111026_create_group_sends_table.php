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
        Schema::create('group_sends', function (Blueprint $table) {
            $table->id();

            $table->string('name', 255);
            $table->string('comment', 255);
            $table->boolean('state')->default(true);
            $table->text('status')->nullable();

            $table->foreignId('user_id')->nullable()->unsigned()->constrained('users');

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
        Schema::dropIfExists('group_sends');
    }
};

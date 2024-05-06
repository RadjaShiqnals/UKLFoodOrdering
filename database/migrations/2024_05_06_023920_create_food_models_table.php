<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Schema::create('food', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->enum('spicy_level', ['Mild', 'Medium', 'Spicy'])->default('Mild');
        //     $table->decimal('price', 10, 2);
        //     $table->string('image')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('food');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('position');
            $table->string('empID');
            $table->string('gender');
            $table->string('status')-> nullable();
            $table->bigInteger('phone')->unsigned();
            $table->string('manager')-> nullable();
            $table->date('dateOfBirth')-> nullable();
            $table->string('address')-> nullable();
            $table->string('bio')->nullable();
            // $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};

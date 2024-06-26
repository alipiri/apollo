<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('admin');
            $table->string('email')->unique();
            $table->string('phone_number')->unique();
            $table->string('certificate')->nullable();
            $table->string('api_token')->nullable();
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();
            $table->fullText('name');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

<?php

use App\Models\V1\Company;
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
        Schema::create('contacts', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Company::class)->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username')->unique();
            $table->boolean('is_active')->default(false);
            $table->string('email')->unique();
            $table->string('email_verified_at')->nullable();
            $table->string('mobile_number')->nullable()->unique();
            $table->string('password');
            $table->string('api_token')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['username', 'api_token']);
            $table->fullText('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};

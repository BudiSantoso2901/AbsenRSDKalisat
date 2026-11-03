<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobil_cinta_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('mobil_cinta_roles')->restrictOnDelete();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->index('role_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobil_cinta_accounts');
    }
};

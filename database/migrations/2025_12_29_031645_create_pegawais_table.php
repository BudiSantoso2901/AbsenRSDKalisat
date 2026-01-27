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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('nip')->unique();
            $table->date('tanggal_lahir')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('id_jabatan')
                ->constrained('jabatan')
                ->restrictOnDelete();
            $table->foreignId('id_lokasi')
                ->constrained('lokasi')
                ->restrictOnDelete();
            $table->foreignId('id_jam_kerja')
                ->constrained('jam_kerja')
                ->restrictOnDelete();
            $table->string('foto_pegawai')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};

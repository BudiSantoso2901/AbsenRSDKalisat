<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pegawai')->constrained('pegawai')->cascadeOnDelete();
            $table->date('tanggal');
            $table->dateTime('waktu_datang')->nullable();
            $table->dateTime('waktu_pulang')->nullable();
            $table->string('foto_datang')->nullable();
            $table->string('foto_pulang')->nullable();
            $table->enum('status', ['hadir', 'izin', 'sakit'])->default('hadir');
            $table->string('surat')->nullable();
            $table->timestamps();

            $table->unique(['id_pegawai', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};

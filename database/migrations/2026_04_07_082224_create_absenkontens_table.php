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
        Schema::create('absenkontens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pegawai')->constrained('pegawai')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('bukti_foto');
            $table->text('link_fb')->nullable();
            $table->text('link_ig')->nullable();
            $table->text('link_tiktok')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('status_verifikasi', ['pending', 'valid', 'ditolak'])
                ->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absenkontens');
    }
};

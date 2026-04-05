<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {

            // 1️⃣ Tambah index khusus untuk FK id_pegawai
            $table->index('id_pegawai');
        });

        Schema::table('absensi', function (Blueprint $table) {

            // 2️⃣ Drop unique lama
            $table->dropUnique('absensi_id_pegawai_tanggal_unique');

            // 3️⃣ Tambah unique baru
            $table->unique(['id_pegawai', 'tanggal', 'shift_id']);
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {

            $table->dropUnique(['id_pegawai', 'tanggal', 'shift_id']);

            $table->unique(['id_pegawai', 'tanggal']);
        });
    }
};

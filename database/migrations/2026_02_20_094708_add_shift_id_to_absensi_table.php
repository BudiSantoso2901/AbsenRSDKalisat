<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {

            // Tambah shift_id setelah id_pegawai (opsional posisi)
            $table->foreignId('shift_id')
                ->nullable()
                ->after('id_pegawai')
                ->constrained('jam_kerja')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {

            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });
    }
};

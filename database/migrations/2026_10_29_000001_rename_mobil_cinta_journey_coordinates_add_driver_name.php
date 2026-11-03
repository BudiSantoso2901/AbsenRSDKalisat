<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['lintang_berangkat', 'bujur_berangkat', 'lintang_sampai', 'bujur_sampai'] as $column) {
            if (!Schema::hasColumn('mobil_cinta_perjalanan', $column)) {
                throw new RuntimeException('Skema perjalanan tidak cocok untuk migration koordinat; tidak ada perubahan dilakukan.');
            }
        }
        if (Schema::hasColumn('mobil_cinta_perjalanan', 'nama_sopir')) {
            throw new RuntimeException('Kolom snapshot nama_sopir sudah ada; periksa migration status sebelum melanjutkan.');
        }

        Schema::table('mobil_cinta_perjalanan', function (Blueprint $table) {
            $table->renameColumn('lintang_berangkat', 'latitude_berangkat');
            $table->renameColumn('bujur_berangkat', 'longitude_berangkat');
            $table->renameColumn('lintang_sampai', 'latitude_sampai');
            $table->renameColumn('bujur_sampai', 'longitude_sampai');
            $table->string('nama_sopir')->nullable()->after('sopir_id');
        });

        $assignments = DB::table('mobil_cinta_perjalanan')
            ->join('mobil_cinta_akun', 'mobil_cinta_akun.id', '=', 'mobil_cinta_perjalanan.sopir_id')
            ->whereNull('mobil_cinta_perjalanan.nama_sopir')
            ->get(['mobil_cinta_perjalanan.id', 'mobil_cinta_akun.nama']);
        foreach ($assignments as $assignment) {
            DB::table('mobil_cinta_perjalanan')->where('id', $assignment->id)->update(['nama_sopir' => $assignment->nama]);
        }

        $orphanedAssignments = DB::table('mobil_cinta_perjalanan as p')
            ->leftJoin('mobil_cinta_akun as a', 'a.id', '=', 'p.sopir_id')
            ->whereNotNull('p.sopir_id')
            ->whereNull('a.id')
            ->count();

        if ($orphanedAssignments > 0) {
            throw new RuntimeException(
                "Migration dibatalkan: ditemukan {$orphanedAssignments} perjalanan dengan akun sopir yang tidak ditemukan."
            );
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Rollback skema koordinat tidak otomatis didukung. Migrasi ini mengubah kontrak aplikasi dan menambah snapshot nama sopir; pulihkan melalui rencana terverifikasi.');
    }
};

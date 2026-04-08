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
        Schema::table('absenkontens', function (Blueprint $table) {
            $table->foreignId('id_ruangan')
                ->after('id_pegawai')
                ->nullable()
                ->constrained('ruangans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('absenkontens', function (Blueprint $table) {
            $table->dropForeign(['id_ruangan']);
            $table->dropColumn('id_ruangan');
        });
    }
};

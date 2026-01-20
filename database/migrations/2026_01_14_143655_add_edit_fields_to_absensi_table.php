<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('absensi', function (Blueprint $table) {

            $table->text('alasan_edit')
                ->nullable()
                ->after('keterangan');

            $table->unsignedBigInteger('edited_by')
                ->nullable()
                ->after('alasan_edit');

            $table->dateTime('edited_at')
                ->nullable()
                ->after('edited_by');

            // OPTIONAL: foreign key ke users/admin
            $table->foreign('edited_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('absensi', function (Blueprint $table) {

            // OPTIONAL: drop FK dulu jika dipakai
            $table->dropForeign(['edited_by']);

            $table->dropColumn([
                'alasan_edit',
                'edited_by',
                'edited_at',
            ]);
        });
    }
};

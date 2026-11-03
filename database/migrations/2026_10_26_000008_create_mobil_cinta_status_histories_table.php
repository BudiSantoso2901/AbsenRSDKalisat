<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobil_cinta_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_request_id')->constrained('mobil_cinta_trip_requests')->restrictOnDelete();
            $table->foreignId('changed_by')->constrained('mobil_cinta_accounts')->restrictOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('note')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();
            $table->index('trip_request_id');
            $table->index('changed_by');
            $table->index(['trip_request_id', 'changed_at'], 'mc_status_request_changed_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobil_cinta_status_histories');
    }
};

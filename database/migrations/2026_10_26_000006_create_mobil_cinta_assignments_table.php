<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobil_cinta_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_request_id')->constrained('mobil_cinta_trip_requests')->restrictOnDelete();
            $table->foreignId('driver_id')->constrained('mobil_cinta_accounts')->restrictOnDelete();
            $table->foreignId('vehicle_id')->constrained('mobil_cinta_vehicles')->restrictOnDelete();
            $table->foreignId('assigned_by')->constrained('mobil_cinta_accounts')->restrictOnDelete();
            $table->string('assignment_status')->default('active')->index();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();
            $table->index('trip_request_id');
            $table->index('driver_id');
            $table->index('vehicle_id');
            $table->index('assigned_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobil_cinta_assignments');
    }
};

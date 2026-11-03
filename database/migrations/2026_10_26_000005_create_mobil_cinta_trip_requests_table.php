<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobil_cinta_trip_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('requested_by')->constrained('mobil_cinta_accounts')->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('ruangans')->nullOnDelete();
            $table->string('patient_name');
            $table->string('medical_record_no');
            $table->string('patient_gender')->nullable();
            $table->foreignId('guarantor_id')->nullable()->constrained('mobil_cinta_guarantors')->nullOnDelete();
            $table->string('destination');
            $table->text('notes')->nullable();
            $table->string('status')->default('requested')->index();
            $table->timestamp('submitted_at')->nullable()->index();
            $table->timestamps();
            $table->index('requested_by');
            $table->index('room_id');
            $table->index('guarantor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobil_cinta_trip_requests');
    }
};

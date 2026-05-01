<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('illimi_medical_profiles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->index();
            $table->uuid('student_id')->unique();
            $table->text('blood_group')->nullable();
            $table->text('genotype')->nullable();
            $table->text('allergies')->nullable();
            $table->text('chronic_conditions')->nullable();
            $table->text('disabilities')->nullable();
            $table->text('current_medications')->nullable();
            $table->text('doctor_name')->nullable();
            $table->text('doctor_phone')->nullable();
            $table->text('health_insurance')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_medical_profiles');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('illimi_immunizations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->index();
            $table->uuid('student_id')->index();
            $table->string('vaccine_name');
            $table->unsignedInteger('dose_number')->default(1);
            $table->date('date_given')->nullable();
            $table->date('due_date')->nullable();
            $table->string('administered_by')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('status');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_immunizations');
    }
};

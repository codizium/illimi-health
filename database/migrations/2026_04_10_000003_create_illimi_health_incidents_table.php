<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        !Schema::hasTable('illimi_health_incidents') && Schema::create('illimi_health_incidents', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->index();
            $table->uuid('student_id')->index();
            $table->uuid('reported_by')->index();
            $table->date('incident_date');
            $table->text('description');
            $table->string('severity');
            $table->string('location')->nullable();
            $table->json('witnesses')->nullable();
            $table->text('action_taken')->nullable();
            $table->boolean('escalated')->default(false);
            $table->uuid('escalated_to')->nullable();
            $table->timestamp('escalated_at')->nullable();
            $table->boolean('parent_notified')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_health_incidents');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('illimi_medical_visits', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->index();
            $table->uuid('student_id')->index();
            $table->uuid('attended_by')->index();
            $table->date('visit_date');
            $table->time('time_in');
            $table->time('time_out')->nullable();
            $table->text('complaint');
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->json('medication_given')->nullable();
            $table->string('outcome');
            $table->boolean('parent_notified')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_medical_visits');
    }
};

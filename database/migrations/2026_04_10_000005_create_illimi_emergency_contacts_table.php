<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('illimi_emergency_contacts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->index();
            $table->uuid('student_id')->index();
            $table->string('name');
            $table->string('relationship')->nullable();
            $table->string('phone');
            $table->string('alternate_phone')->nullable();
            $table->unsignedInteger('priority')->default(1);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('illimi_emergency_contacts');
    }
};

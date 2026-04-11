<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('alumni_number')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('final_grade_average')->nullable();
            $table->string('higher_education')->nullable();
            $table->string('major')->nullable();
            $table->string('current_occupation')->nullable();
            $table->string('current_company')->nullable();
            $table->string('current_city')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('testimonial')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('graduated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'alumni_number']);
            $table->index(['tenant_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};

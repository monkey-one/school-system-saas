<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreign('homeroom_teacher_id')
                ->references('id')
                ->on('teachers')
                ->nullOnDelete();
        });

        Schema::table('classroom_subjects', function (Blueprint $table) {
            $table->foreign('teacher_id')
                ->references('id')
                ->on('teachers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropForeign(['homeroom_teacher_id']);
        });

        Schema::table('classroom_subjects', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
        });
    }
};

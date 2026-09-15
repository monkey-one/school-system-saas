<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Day-to-day student services: online leave requests (izin/sakit), student
// savings & cashless transactions, and guidance & counseling (BK) records.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('attachment')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('review_note', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['student_id', 'start_date', 'end_date']);
        });

        Schema::create('savings_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->decimal('amount', 14, 2);
            $table->decimal('balance_after', 14, 2);
            $table->string('reference');
            $table->string('description')->nullable();
            $table->string('merchant')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('transacted_at');
            $table->timestamps();

            $table->unique(['tenant_id', 'reference']);
            $table->index(['student_id', 'id']);
        });

        Schema::create('violation_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('severity')->default('light');
            $table->unsignedSmallInteger('points')->default(5);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('student_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('violation_type_id')->nullable()->constrained()->nullOnDelete();
            $table->date('occurred_at');
            $table->unsignedSmallInteger('points');
            $table->text('description')->nullable();
            $table->string('action_taken')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('visible_to_parent')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'occurred_at']);
        });

        Schema::create('counseling_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('session_date');
            $table->string('category')->default('academic');
            $table->text('summary');
            $table->text('follow_up')->nullable();
            $table->boolean('is_confidential')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'session_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counseling_notes');
        Schema::dropIfExists('student_violations');
        Schema::dropIfExists('violation_types');
        Schema::dropIfExists('savings_transactions');
        Schema::dropIfExists('leave_requests');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Direct messaging between parents/students and teachers needs an explicit
// recipient, a subject line and (optionally) the child the message is about.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('recipient_id')->nullable()->after('sender_id')->constrained('users')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->after('recipient_id')->constrained()->nullOnDelete();
            $table->string('subject')->nullable()->after('student_id');

            $table->index(['tenant_id', 'recipient_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'recipient_id', 'read_at']);
            $table->dropConstrainedForeignId('recipient_id');
            $table->dropConstrainedForeignId('student_id');
            $table->dropColumn('subject');
        });
    }
};

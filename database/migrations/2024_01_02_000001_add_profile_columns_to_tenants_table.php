<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->text('vision')->nullable()->after('principal_name');
            $table->text('mission')->nullable()->after('vision');
            $table->text('description')->nullable()->after('mission');
            $table->string('accreditation')->nullable()->after('description');
            $table->integer('founded_year')->nullable()->after('accreditation');
            $table->string('website')->nullable()->after('founded_year');
            $table->json('gallery')->nullable()->after('website');
            $table->json('social_links')->nullable()->after('gallery');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'vision', 'mission', 'description', 'accreditation',
                'founded_year', 'website', 'gallery', 'social_links',
            ]);
        });
    }
};

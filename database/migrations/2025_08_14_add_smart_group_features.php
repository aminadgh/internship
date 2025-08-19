<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contact_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_groups', 'category')) {
                $table->string('category')->nullable()->after('area_name');
            }
            if (!Schema::hasColumn('contact_groups', 'tags')) {
                $table->json('tags')->nullable()->after('category');
            }
            if (!Schema::hasColumn('contact_groups', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('tags');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_groups', function (Blueprint $table) {
            $table->dropColumn(['category', 'tags', 'is_active']);
        });
    }
};

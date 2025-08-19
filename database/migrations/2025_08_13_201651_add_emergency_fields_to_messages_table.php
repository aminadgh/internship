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
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_emergency')->default(false)->after('content');
            $table->enum('priority', ['low', 'normal', 'high', 'critical'])->default('normal')->after('is_emergency');
            $table->enum('broadcast_type', ['group', 'all', 'zone'])->default('group')->after('priority');
            $table->string('emergency_category')->nullable()->after('broadcast_type');
            $table->timestamp('scheduled_at')->nullable()->after('sent_at');
            $table->boolean('requires_approval')->default(false)->after('scheduled_at');
            $table->string('approved_by')->nullable()->after('requires_approval');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn([
                'is_emergency',
                'priority',
                'broadcast_type',
                'emergency_category',
                'scheduled_at',
                'requires_approval',
                'approved_by',
                'approved_at'
            ]);
        });
    }
};

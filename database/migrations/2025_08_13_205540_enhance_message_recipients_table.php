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
        Schema::table('message_recipients', function (Blueprint $table) {
            $table->string('twilio_sid')->nullable()->after('error_message');
            $table->string('twilio_status')->nullable()->after('twilio_sid');
            $table->timestamp('delivered_at')->nullable()->after('twilio_status');
            $table->timestamp('read_at')->nullable()->after('delivered_at');
            $table->integer('attempts')->default(1)->after('read_at');
            $table->timestamp('last_attempt_at')->nullable()->after('attempts');
            
            // Add index for better performance
            $table->index(['status', 'delivered_at']);
            $table->index(['twilio_sid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_recipients', function (Blueprint $table) {
            $table->dropIndex(['status', 'delivered_at']);
            $table->dropIndex(['twilio_sid']);
            
            $table->dropColumn([
                'twilio_sid',
                'twilio_status',
                'delivered_at',
                'read_at',
                'attempts',
                'last_attempt_at'
            ]);
        });
    }
};

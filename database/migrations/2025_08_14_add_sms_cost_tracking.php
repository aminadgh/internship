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
        // Add cost tracking to messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->decimal('total_cost', 10, 4)->default(0)->after('user_id');
            $table->integer('total_recipients')->default(0)->after('total_cost');
            $table->string('cost_currency', 3)->default('USD')->after('total_recipients');
        });

        // Add cost tracking to message_recipients table
        Schema::table('message_recipients', function (Blueprint $table) {
            $table->decimal('cost', 10, 4)->default(0)->after('twilio_status');
            $table->string('cost_currency', 3)->default('USD')->after('cost');
            $table->string('carrier', 100)->nullable()->after('cost_currency');
            $table->string('country_code', 2)->nullable()->after('carrier');
        });

        // Add SMS pricing configuration table
        Schema::create('sms_pricing', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2);
            $table->string('carrier', 100)->nullable();
            $table->decimal('inbound_cost', 10, 4)->default(0);
            $table->decimal('outbound_cost', 10, 4)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['country_code', 'carrier']);
        });

        // Insert default US pricing (Twilio standard rates)
        DB::table('sms_pricing')->insert([
            [
                'country_code' => 'US',
                'carrier' => null,
                'inbound_cost' => 0.0075,
                'outbound_cost' => 0.0079,
                'currency' => 'USD',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_code' => 'CA',
                'carrier' => null,
                'inbound_cost' => 0.0075,
                'outbound_cost' => 0.0079,
                'currency' => 'USD',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove cost tracking from messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['total_cost', 'total_recipients', 'cost_currency']);
        });

        // Remove cost tracking from message_recipients table
        Schema::table('message_recipients', function (Blueprint $table) {
            $table->dropColumn(['cost', 'cost_currency', 'carrier', 'country_code']);
        });

        // Drop SMS pricing table
        Schema::dropIfExists('sms_pricing');
    }
};




<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the first user ID
        $userId = DB::table('users')->orderBy('id')->value('id');
        if ($userId) {
            DB::table('contacts')->whereNull('user_id')->update(['user_id' => $userId]);
            DB::table('contact_groups')->whereNull('user_id')->update(['user_id' => $userId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally set user_id back to null
        DB::table('contacts')->update(['user_id' => null]);
        DB::table('contact_groups')->update(['user_id' => null]);
    }
}; 
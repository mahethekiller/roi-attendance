<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('attendances')
            ->where('show_status', 'late')
            ->update(['show_status' => 'present']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed as late status is deprecated
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('company')->constrained('companies')->nullOnDelete();
        });

        // Backfill existing employees matching their company string to companies.name
        $companies = DB::table('companies')->get(['id', 'name']);
        foreach ($companies as $comp) {
            DB::table('employees')
                ->where('company', $comp->name)
                ->orWhere('company', 'like', '%' . trim($comp->name) . '%')
                ->update(['company_id' => $comp->id]);
        }
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 50)->nullable();
            $table->string('status', 20)->default('active')->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed initial 8 companies
        $initialCompanies = [
            ['id' => 1, 'name' => 'I2K2 Networks Private Limited', 'code' => 'I2K2', 'status' => 'active'],
            ['id' => 2, 'name' => 'ROI Mantra Private Limited', 'code' => 'ROIM', 'status' => 'active'],
            ['id' => 3, 'name' => 'General Infomatics', 'code' => 'GI', 'status' => 'active'],
            ['id' => 4, 'name' => 'iXcheck Technology', 'code' => 'IXCHECK', 'status' => 'active'],
            ['id' => 5, 'name' => 'Stack Services Pvt. Ltd.', 'code' => 'STACK-SRV', 'status' => 'active'],
            ['id' => 7, 'name' => 'Stack Systems', 'code' => 'STACK-SYS', 'status' => 'active'],
            ['id' => 8, 'name' => 'i2k2 Comtrade Private Limited', 'code' => 'I2K2-COM', 'status' => 'active'],
            ['id' => 9, 'name' => 'Xtra4 Systems Private Limited', 'code' => 'XTRA4', 'status' => 'active'],
        ];

        $now = now();
        foreach ($initialCompanies as $c) {
            DB::table('companies')->updateOrInsert(
                ['id' => $c['id']],
                [
                    'name'        => trim($c['name']),
                    'code'        => $c['code'],
                    'status'      => $c['status'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

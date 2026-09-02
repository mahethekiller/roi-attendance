<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('trigger_type', 50)->default('cron')->index(); // 'cron', 'command', 'manual_ui', 'webhook'
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 20)->default('success')->index(); // 'success', 'failed'
            $table->integer('imported_count')->default(0);
            $table->integer('updated_count')->default(0);
            $table->text('message')->nullable();
            $table->json('payload_summary')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};

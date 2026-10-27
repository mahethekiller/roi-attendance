<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('greythr_push_logs', function (Blueprint $table) {
            $table->id();
            $table->string('trigger_type', 50)->default('cron')->index(); // cron, manual_ui, command
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->integer('records_count')->default(0);
            $table->integer('swipes_count')->default(0);
            $table->string('status', 50)->default('success')->index(); // success, failed, empty
            $table->integer('http_status')->nullable();
            $table->text('message')->nullable();
            $table->json('payload_sample')->nullable();
            $table->json('response_payload')->nullable();
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['created_at', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('greythr_push_logs');
    }
};

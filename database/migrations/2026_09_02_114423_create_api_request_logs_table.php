<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('token_name')->nullable()->index();
            $table->string('ip_address', 45)->index();
            $table->string('method', 10)->index();
            $table->string('url', 500);
            $table->json('query_params')->nullable();
            $table->integer('status_code')->index();
            $table->float('duration_ms')->default(0);
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_request_logs');
    }
};

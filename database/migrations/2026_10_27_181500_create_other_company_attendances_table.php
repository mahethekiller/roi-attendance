<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('other_company_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('card_no')->nullable()->index();
            $table->string('badgenumber')->nullable()->index();
            $table->string('employee_name')->nullable()->index();
            $table->string('company_name')->nullable()->default('Other Company')->index();
            $table->date('punch_date')->index();
            $table->dateTime('check_in_datetime')->nullable();
            $table->dateTime('check_out_datetime')->nullable();
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->string('show_status', 50)->default('present')->index();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['card_no', 'punch_date']);
            $table->index(['company_name', 'punch_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('other_company_attendances');
    }
};

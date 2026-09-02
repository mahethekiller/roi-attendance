<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('card_no')->nullable()->index();
            $table->date('punch_date')->index();
            $table->dateTime('check_in_datetime')->nullable();
            $table->dateTime('check_out_datetime')->nullable();
            $table->string('badgenumber')->nullable()->index();
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->string('show_status', 50)->default('present')->index();
            $table->timestamps();

            $table->index(['card_no', 'punch_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

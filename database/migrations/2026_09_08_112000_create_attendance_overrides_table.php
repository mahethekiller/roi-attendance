<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->nullable()->index();
            $table->string('card_no')->nullable()->index();
            $table->string('employee_name')->nullable();
            $table->time('check_in_window_start')->default('10:00:00');
            $table->time('check_in_window_end')->default('10:20:00');
            $table->unsignedSmallInteger('adjusted_in_min_minute')->default(20);
            $table->unsignedSmallInteger('adjusted_in_max_minute')->default(35);
            $table->decimal('min_duration_hours', 4, 2)->default(9.00);
            $table->boolean('is_active')->default(true)->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_overrides');
    }
};

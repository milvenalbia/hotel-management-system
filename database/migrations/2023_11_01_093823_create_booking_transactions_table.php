<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('guest_id');
            $table->string('check_in');
            $table->string('check_out');
            $table->integer('room');
            $table->integer('adult');
            $table->integer('children')->nullable();
            $table->integer('extra_bed')->nullable();
            $table->integer('extend_hours')->nullable();
            $table->integer('extedn_days')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('remaining_amount', 10, 2);
            $table->decimal('order_cost', 10, 2)->nullable();
            $table->boolean('check_in_status')->default(false);
            $table->boolean('check_out_status')->default(false);
            $table->boolean('booking_status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_transactions');
    }
};

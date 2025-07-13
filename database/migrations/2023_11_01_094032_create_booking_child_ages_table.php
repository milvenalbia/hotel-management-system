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
        Schema::create('booking_child_ages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_transaction_id')->constrained()->onDelete('cascade');
            $table->integer('child_ages');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_child_ages');
    }
};

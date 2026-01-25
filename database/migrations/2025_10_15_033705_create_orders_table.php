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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_pesanan')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('status')->default('belum bayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

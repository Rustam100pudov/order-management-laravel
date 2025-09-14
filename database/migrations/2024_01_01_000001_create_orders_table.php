<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('customer_inn')->nullable();
            $table->string('company_name')->nullable();
            $table->string('customer_address')->nullable();
            $table->enum('status', ['new', 'in_progress', 'completed'])->default('new');
            $table->foreignId('operator_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
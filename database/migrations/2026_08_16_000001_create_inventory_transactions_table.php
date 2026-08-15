<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->enum('type', ['purchase', 'sale']);
            $table->date('transaction_date')->unique();
            $table->unsignedBigInteger('quantity');
            $table->decimal('unit_cost', 18, 2)
                ->comment('Purchase price or weighted average cost applied to a sale');
            $table->decimal('total_cost', 18, 2)
                ->comment('Purchase value or total cost assigned to a sale');
            $table->timestamps();

            $table->index(['product_id', 'transaction_date']);
            $table->index(['type', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};

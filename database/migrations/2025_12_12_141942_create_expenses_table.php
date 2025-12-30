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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['electricity', 'rent', 'salaries', 'shipping', 'maintenance', 'marketing', 'office']);
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'bank', 'vodafone_cash', 'credit_card']);
            $table->date('expense_date');
            $table->string('attachment')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

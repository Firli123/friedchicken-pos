<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // TRX-20260609-0001
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->bigInteger('subtotal');
            $table->bigInteger('discount')->default(0);
            $table->bigInteger('tax')->default(0);
            $table->bigInteger('total');
            $table->enum('payment_method', ['cash', 'qris']);
            $table->bigInteger('amount_paid')->default(0);
            $table->bigInteger('change_amount')->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->string('product_name'); // snapshot
            $table->bigInteger('product_price'); // snapshot
            $table->integer('quantity');
            $table->bigInteger('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transactions');
    }
};

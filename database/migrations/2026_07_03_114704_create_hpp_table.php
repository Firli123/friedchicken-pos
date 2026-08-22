<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hpp', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('keterangan');
            $table->bigInteger('jumlah');
            $table->enum('kategori', ['bahan_baku', 'operasional', 'lainnya'])
                  ->default('bahan_baku');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hpp');
    }
};
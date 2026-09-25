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
        Schema::create('closings', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->decimal('total_sistem', 12, 2);
            $table->decimal('total_fisik', 12, 2);
            $table->decimal('selisih', 12, 2);
            $table->enum('status', ['Selisih', 'Buka', 'Tutup'])->default('Buka');
            $table->text('catatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('closings');
    }
};

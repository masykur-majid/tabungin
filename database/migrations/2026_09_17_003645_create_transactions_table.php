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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('no_slip');
            $table->string('jenis_transaksi')->default('setoran');
            $table->decimal('jumlah_transaksi', 9, 2);
            $table->string('status');
            $table->timestamp('tanggal_pengajuan_batal')->nullable();
            $table->string('alasan_batal')->nullable();
            $table->timestamp('tanggal_dibatalkan')->nullable();
            $table->foreignId('approver')->constrained('users', 'id')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();          
        });
    }
    
     /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

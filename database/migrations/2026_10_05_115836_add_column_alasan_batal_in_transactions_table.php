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
        Schema::table('transactions', function (Blueprint $table) {
            //kolom untuk status transaksi
            $table->string('status')->default('berhasil')->change();

            //pengajuan pembatalan oleh petugas
            $table->foreignId('diajukan_batal_oleh')->nullable()->constrained('users');
            $table->timestamp('tanggal_pengajuan_batal')->nullable();
            $table->string('alasan_batal')->nullable();

            //persetujuan batal oleh guru
            $table->foreignId('dibatalkan_oleh')->nullable()->constrained('users');
            $table->timestamp('tanggal_dibatalkan')->nullable();

            $table->foreignId('closing_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('status', ['berhasil', 'pengajuan pembatalan', 'dibatalkan'])->default('berhasil')->after('jumlah_transaksi')->change();

            $table->dropConstrainedForeignId('tutup_akun_id');
            $table->dropConstrainedForeignId('dibatalkan_oleh');
            $table->dropConstrainedForeignId('diajukan_batal_oleh');
            $table->dropColumn([
                'tanggal_pengajuan_batal', 'alasan_batal', 'tanggal_dibatalkan',
            ]);
        });
    }
};

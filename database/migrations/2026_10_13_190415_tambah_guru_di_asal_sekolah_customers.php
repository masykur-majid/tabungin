<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('customers', 'tipe')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->enum('tipe', ['siswa', 'guru'])->default('siswa')->after('nama');
            });
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->enum('asal_sekolah', [
                'SMKS 1 PARAHYANGAN',
                'SMP PARAHYANGAN',
                'GURU',
            ])->nullable()->change();
        });

        DB::table('customers')
            ->where('asal_sekolah', 'GURU')
            ->update([
                'tipe' => 'guru',
                'asal_sekolah' => null,
            ]);

        Schema::table('customers', function (Blueprint $table) {
            $table->enum('asal_sekolah', [
                'SMKS 1 PARAHYANGAN',
                'SMP PARAHYANGAN',
            ])->nullable()->change();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('customers', 'tipe')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('tipe');
            });
        }
    }
};
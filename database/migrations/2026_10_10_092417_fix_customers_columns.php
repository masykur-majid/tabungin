<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('alamat', 'alamat_rumah');
            $table->renameColumn('jenis kelamin', 'jenis_kelamin');
            $table->renameColumn('asal-sekolah', 'asal_sekolah');
            $table->renameColumn('no-telepon', 'no_telepon');
        });

        DB::statement("ALTER TABLE customers MODIFY asal_sekolah ENUM('SMP','SMK','SMKS 1 PARAHYANGAN','SMP PARAHYANGAN') NOT NULL");
        DB::table('customers')->where('asal_sekolah', 'SMK')->update(['asal_sekolah' => 'SMKS 1 PARAHYANGAN']);
        DB::table('customers')->where('asal_sekolah', 'SMP')->update(['asal_sekolah' => 'SMP PARAHYANGAN']);
        DB::statement("ALTER TABLE customers MODIFY asal_sekolah ENUM('SMKS 1 PARAHYANGAN','SMP PARAHYANGAN') NOT NULL");
    }

    public function down(): void
    {
        //
    }
};
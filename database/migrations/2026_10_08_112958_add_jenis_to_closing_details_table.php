<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('closing_details', function (Blueprint $table) {
            $table->string('jenis', 10)->nullable();
        });

        /*
         * Backfill data lama: per closing, 4 baris pertama = koin,
         * sisanya = kertas (sesuai urutan simpan sebelumnya).
         * Sesuaikan nama kolom "closing_id" jika berbeda.
         */
        $perClosing = DB::table('closing_details')
            ->orderBy('closing_id')
            ->orderBy('id')
            ->get()
            ->groupBy('closing_id');

        foreach ($perClosing as $rows) {
            foreach ($rows->values() as $index => $row) {
                DB::table('closing_details')
                    ->where('id', $row->id)
                    ->update(['jenis' => $index < 4 ? 'koin' : 'kertas']);
            }
        }
    }

    public function down(): void
    {
        Schema::table('closing_details', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};

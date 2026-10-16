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
        if (! Schema::hasColumn('customers', 'nik')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('nik', 16)->nullable()->unique()->after('nisn');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customers', 'nik')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('nik');
            });
        }
    }
};

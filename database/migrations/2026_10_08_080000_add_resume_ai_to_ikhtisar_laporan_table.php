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
        Schema::table('ikhtisar_laporan', function (Blueprint $table) {
            if (!Schema::hasColumn('ikhtisar_laporan', 'resume_ai')) {
                $table->longText('resume_ai')->nullable()->after('catatan_khusus');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ikhtisar_laporan', function (Blueprint $table) {
            if (Schema::hasColumn('ikhtisar_laporan', 'resume_ai')) {
                $table->dropColumn('resume_ai');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->string('no_lhp', 100)->nullable()->after('penugasan_id');
            $table->string('judul_lhp', 255)->nullable()->after('no_lhp');
            $table->date('tgl_lhp')->nullable()->after('judul_lhp');
        });
    }

    public function down(): void
    {
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->dropColumn(['no_lhp', 'judul_lhp', 'tgl_lhp']);
        });
    }
};

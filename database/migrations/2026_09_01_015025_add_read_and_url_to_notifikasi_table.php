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
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->string('judul', 150)->nullable()->after('jenis');
            $table->string('url_target', 255)->nullable()->after('pesan');
            $table->timestamp('dibaca_pada')->nullable()->after('dikirim_pada');
            $table->index(['user_id', 'dibaca_pada']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'dibaca_pada']);
            $table->dropColumn(['judul', 'url_target', 'dibaca_pada']);
        });
    }
};

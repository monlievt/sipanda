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
        Schema::table('penugasan', function (Blueprint $table) {
            $table->text('dasar_penugasan')->nullable()->after('uraian_penugasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasan', function (Blueprint $table) {
            $table->dropColumn('dasar_penugasan');
        });
    }
};

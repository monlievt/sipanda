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
        Schema::table('pkppt', function (Blueprint $table) {
            $table->unsignedBigInteger('pkppt_induk_id')->nullable()->after('versi_revisi');
            $table->foreign('pkppt_induk_id')->references('id')->on('pkppt')->nullOnDelete();

            $table->text('catatan_revisi')->nullable()->after('pkppt_induk_id');
            $table->timestamp('direviu_pada')->nullable()->after('catatan_revisi');
            $table->unsignedBigInteger('direviu_oleh')->nullable()->after('direviu_pada');
            $table->foreign('direviu_oleh')->references('id')->on('users')->nullOnDelete();

            $table->index('pkppt_induk_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkppt', function (Blueprint $table) {
            $table->dropForeign(['pkppt_induk_id']);
            $table->dropForeign(['direviu_oleh']);
            $table->dropColumn(['pkppt_induk_id', 'catatan_revisi', 'direviu_pada', 'direviu_oleh']);
        });
    }
};

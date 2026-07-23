<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah FK ke irbans (setelah tabel irbans ada)
            $table->foreignId('irban_id')->nullable()->constrained('irbans')->nullOnDelete()->after('golongan');
            $table->index('irban_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['irban_id']);
            $table->dropColumn('irban_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'status_jabatan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('status_jabatan', 20)->default('definitif')->after('jabatan');
            });
        }

        // Set status_jabatan untuk Plt. Inspektur & Plt. Irban aktif saat ini
        DB::table('users')
            ->where('email', 'onowiji2@gmail.com')
            ->orWhere('jabatan', 'like', '%Plt%')
            ->orWhere('jabatan', 'like', '%plt%')
            ->update(['status_jabatan' => 'plt']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'status_jabatan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('status_jabatan');
            });
        }
    }
};

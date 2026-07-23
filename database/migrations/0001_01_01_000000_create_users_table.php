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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 200);
            $table->string('nama_tanpa_gelar', 150)->nullable();
            $table->string('nip', 30)->nullable()->unique();
            $table->string('email', 150)->unique();
            $table->string('no_hp', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // nullable: bisa SSO only
            $table->string('google_id', 100)->nullable()->unique();
            $table->rememberToken();
            $table->string('jabatan', 150)->nullable();
            $table->string('pangkat', 100)->nullable();
            $table->string('golongan', 10)->nullable();
            // irban_id & role akan di-add di modify_users migration (setelah irbans tersedia)
            $table->boolean('is_active')->default(true);
            $table->enum('tipe_akun', ['internal', 'opd'])->default('internal');
            $table->unsignedBigInteger('objek_penugasan_id')->nullable(); // hanya untuk opd
            $table->enum('status_undangan', ['pending', 'aktif'])->default('aktif');
            $table->string('token_undangan', 100)->nullable();
            $table->timestamp('token_kedaluwarsa')->nullable();
            $table->timestamps();

            // Index
            $table->index('tipe_akun');
            $table->index('is_active');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};

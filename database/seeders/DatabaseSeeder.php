<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,            // 1. Buat roles & permissions
            IrbanSeeder::class,           // 2. Buat Irban
            MasterDataSeeder::class,      // 3. Jenis & sumber penugasan
            ObjekPenugasanSeeder::class,  // 4. Objek penugasan (OPD & Kecamatan Trenggalek)
            UserSeeder::class,            // 5. User dari data-pegawai.csv
            RegulasiHukumSeeder::class,   // 6. Bank regulasi dasar hukum APIP
            FaqArtikelSeeder::class,      // 7. Bank artikel tanya jawab resmi APIP
            UatFeedbackSeeder::class,     // 8. Contoh masukan kotak UAT
        ]);

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('  SIPANDA — Database Seeder Selesai ✓');
        $this->command->info('═══════════════════════════════════════════');
    }
}

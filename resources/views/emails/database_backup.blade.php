<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Salinan Cadangan Database SIPANDA</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #334155; background-color: #f8fafc; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="background-color: #047857; padding: 24px; text-align: center; color: #ffffff;">
            <h2 style="margin: 0; font-size: 20px; font-weight: bold;">SIPANDA KABUPATEN TRENGGALEK</h2>
            <p style="margin: 4px 0 0; font-size: 12px; opacity: 0.9;">Sistem Pengawasan Internal dan Tindak Lanjut Daerah</p>
        </div>

        <div style="padding: 24px;">
            <h3 style="margin-top: 0; color: #0f172a; font-size: 16px;">Yth. Administrator Sistem / Super Admin,</h3>
            <p style="font-size: 13px; color: #475569;">
                Berikut terlampir salinan arsip cadangan (<em>database backup</em>) otomatis aplikasi SIPANDA Inspektorat Daerah Kabupaten Trenggalek yang telah berhasil dibuat:
            </p>

            <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin: 16px 0; background-color: #f1f5f9; border-radius: 8px; overflow: hidden;">
                <tr>
                    <td style="padding: 10px 14px; font-weight: bold; width: 35%; color: #475569; border-bottom: 1px solid #e2e8f0;">Tanggal & Waktu</td>
                    <td style="padding: 10px 14px; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ $backupDate }} WIB</td>
                </tr>
                <tr>
                    <td style="padding: 10px 14px; font-weight: bold; color: #475569; border-bottom: 1px solid #e2e8f0;">Nama Berkas Lampiran</td>
                    <td style="padding: 10px 14px; font-family: monospace; color: #047857; font-weight: bold; border-bottom: 1px solid #e2e8f0;">{{ $fileName }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 14px; font-weight: bold; color: #475569;">Ukuran Berkas</td>
                    <td style="padding: 10px 14px; color: #0f172a;">{{ $fileSize }}</td>
                </tr>
            </table>

            <p style="font-size: 12px; color: #64748b; line-height: 1.5;">
                🔒 <strong>Catatan Keamanan:</strong> Berkas database ini memuat seluruh data transaksi pengawasan, SPT, LHP, dan matriks tindak lanjut. Harap simpan berkas cadangan ini dengan aman untuk keperluan mitigasi risiko bencana data (<em>Disaster Recovery</em>).
            </p>
        </div>

        <div style="background-color: #f8fafc; padding: 16px 24px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 11px; color: #94a3b8;">
            Email ini dibuat dan dikirimkan secara otomatis oleh Sistem Otomasi Cadangan SIPANDA.<br>
            Inspektorat Daerah Kabupaten Trenggalek &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>

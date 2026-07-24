<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Hasil Konsultasi - {{ $konsultasi->nomor_tiket }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 10pt;
            font-style: italic;
        }
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 13pt;
            margin-top: 15px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .nomor {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 20px;
        }
        table.meta {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        table.meta td {
            vertical-align: top;
            padding: 3px 0;
        }
        .box {
            border: 1px solid #000;
            padding: 12px;
            margin-bottom: 15px;
            background-color: #fcfcfc;
        }
        .signatures {
            width: 100%;
            margin-top: 30px;
        }
        .signatures td {
            text-align: center;
            vertical-align: top;
            width: 50%;
        }
        .no-print {
            margin-bottom: 20px;
            text-align: right;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 8px 16px; background-color: #007bff; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak Dokumen PDF
        </button>
    </div>

    <!-- Kop Surat Inspektorat Trenggalek -->
    <div class="header">
        <h3>PEMERINTAH KABUPATEN TRENGGALEK</h3>
        <h2>INSPEKTORAT DAERAH</h2>
        <p>Jl. Gajah Mada No. 1 Trenggalek, Jawa Timur | Telp. (0355) 791407</p>
    </div>

    <div class="title">BERITA ACARA HASIL KONSULTASI APIP</div>
    <div class="nomor">Nomor: {{ $konsultasi->nomor_tiket }}</div>

    <p>Pada hari ini <strong>{{ $konsultasi->updated_at->isoFormat('D MMMM Y') }}</strong>, telah dilaksanakan Layanan Konsultasi dan Advisory Pengawasan APIP antara Inspektorat Kabupaten Trenggalek dengan Perangkat Daerah pemohon sebagai berikut:</p>

    <table class="meta">
        <tr>
            <td width="30%">1. Perangkat Daerah / Objek</td>
            <td width="3%">:</td>
            <td><strong>{{ $konsultasi->objekPenugasan?->nama ?? $konsultasi->pemohon?->nama }}</strong></td>
        </tr>
        <tr>
            <td>2. Pemohon Konsultasi</td>
            <td>:</td>
            <td>{{ $konsultasi->pemohon?->nama }} ({{ $konsultasi->pemohon?->nip ?? '-' }})</td>
        </tr>
        <tr>
            <td>3. Area / Topik Konsultasi</td>
            <td>:</td>
            <td><strong>{{ $konsultasi->area_konsultasi }}</strong></td>
        </tr>
        <tr>
            <td>4. Metode Konsultasi</td>
            <td>:</td>
            <td>{{ $konsultasi->metode_disetujui === 'online' ? 'Online Chat Daring' : 'Tatap Muka (' . ($konsultasi->tanggal_tatap_muka ? $konsultasi->tanggal_tatap_muka->format('d/m/Y H:i') : '-') . ')' }}</td>
        </tr>
    </table>

    <div class="title" style="text-align: left; font-size: 11pt; text-decoration: none; border-bottom: 1px solid #000; padding-bottom: 2px;">I. POKOK PERMASALAHAN KONSULTASI</div>
    <div class="box">
        <strong>Judul:</strong> {{ $konsultasi->judul_permasalahan }}<br><br>
        <strong>Uraian:</strong><br>
        {{ $konsultasi->uraian_permasalahan }}
    </div>

    <div class="title" style="text-align: left; font-size: 11pt; text-decoration: none; border-bottom: 1px solid #000; padding-bottom: 2px;">II. KESIMPULAN & ADVIS RESMI APIP</div>
    <div class="box">
        {{ $konsultasi->kesimpulan_advis }}
    </div>

    <div class="title" style="text-align: left; font-size: 11pt; text-decoration: none; border-bottom: 1px solid #000; padding-bottom: 2px;">III. SUSUNAN TIM KONSULTASI APIP</div>
    <table class="meta" style="border: 1px solid #000; padding: 5px; margin-top: 5px;">
        @foreach($konsultasi->tim as $tMember)
            <tr>
                <td width="35%">- {{ $tMember->peran_label }}</td>
                <td width="3%">:</td>
                <td><strong>{{ $tMember->user?->nama }}</strong> (NIP. {{ $tMember->user?->nip ?? '-' }})</td>
            </tr>
        @endforeach
    </table>

    <p style="margin-top: 20px;">Demikian Berita Acara Hasil Konsultasi ini dibuat untuk dapat dipergunakan sebagai bahan pertimbangan dan pedoman pelaksanaan tugas pada Perangkat Daerah terkait.</p>

    <table class="signatures">
        <tr>
            <td>
                Pemohon Konsultasi<br>
                <strong>{{ $konsultasi->objekPenugasan?->nama ?? 'Perangkat Daerah' }}</strong>
                <br><br><br><br>
                <strong><u>{{ $konsultasi->pemohon?->nama }}</u></strong><br>
                NIP. {{ $konsultasi->pemohon?->nip ?? '-' }}
            </td>
            <td>
                Tim Konsultasi APIP<br>
                <strong>Inspektorat Kabupaten Trenggalek</strong>
                <br><br><br><br>
                <strong><u>{{ $konsultasi->tim->where('peran', 'ketua_tim')->first()?->user?->nama ?? 'Ketua Tim APIP' }}</u></strong><br>
                NIP. {{ $konsultasi->tim->where('peran', 'ketua_tim')->first()?->user?->nip ?? '-' }}
            </td>
        </tr>
    </table>
</body>
</html>

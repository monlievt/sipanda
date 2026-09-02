<?php

namespace App\Http\Controllers;

use App\Services\CsvImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportController extends Controller
{
    public function __construct(
        protected CsvImportService $importService
    ) {}

    /**
     * Tampilkan Halaman Dashboard Import Data Historis.
     */
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'penugasan');
        return view('import.index', compact('tab'));
    }

    /**
     * Unduh Template Standar untuk Import (Format Excel .xlsx atau CSV .csv).
     */
    public function template(Request $request, string $type): StreamedResponse|RedirectResponse
    {
        $format = strtolower($request->query('format', 'xlsx'));

        $templates = [
            'penugasan' => [
                'sheet_title' => 'Template Penugasan SPT',
                'filename'    => 'Template_Import_Penugasan_SPT',
                'headers'     => [
                    'no_spt', 'uraian_penugasan', 'irban', 'jenis_penugasan',
                    'sumber_penugasan', 'objek_penugasan', 'tanggal_mulai', 'tanggal_selesai', 'status'
                ],
                'samples'     => [
                    ['SPT/700/01/2025', 'Audit Kinerja Pengelolaan Keuangan', 'Irban I', 'Audit Kinerja', 'PKPT', 'Dinas Pendidikan, Dinas Kesehatan', '2025-02-01', '2025-02-15', 'selesai'],
                    ['SPT/700/02/2025', 'Reviu RKPD Kabupaten Trenggalek 2025', 'Irban II', 'Reviu Dokumen', 'Mandatori', 'Bappedalitbang', '2025-03-01', '2025-03-10', 'selesai'],
                ]
            ],
            'tindak_lanjut' => [
                'sheet_title' => 'Template Matriks LHP',
                'filename'    => 'Template_Import_Matriks_LHP',
                'headers'     => [
                    'no_lhp', 'no_spt', 'uraian_temuan', 'rekomendasi_wajib',
                    'nilai_rekomendasi_rp', 'target_penyelesaian', 'status_tindak_lanjut',
                    'judul_lhp', 'tanggal_lhp'
                ],
                'samples'     => [
                    ['LHP/700/01/2025', 'SPT/700/01/2025', 'Terdapat kelebihan bayar perjalanan dinas pada 3 kegiatan', 'Menyetorkan kelebihan bayar sebesar Rp 15.000.000 ke Rekening Kas Daerah', 15000000, '2025-04-30', 'selesai', 'LHP Kinerja Dinas Pendidikan 2025', '2025-02-28'],
                    ['LHP/700/02/2025', 'SPT/700/02/2025', 'Penatausahaan aset BMD belum tertib kartu inventaris barang', 'Melakukan inventarisasi dan mencatat seluruh BMD pada SIMDA BMD', 0, '2025-05-31', 'proses', 'LHP Aset Bappedalitbang 2025', '2025-03-15'],
                ]
            ],
            'objek' => [
                'sheet_title' => 'Template Master Objek',
                'filename'    => 'Template_Import_Objek_Pengawasan',
                'headers'     => ['nama_instansi_objek', 'kategori', 'status_aktif'],
                'samples'     => [
                    ['Dinas Pariwisata dan Kebudayaan', 'opd', 'aktif'],
                    ['Kecamatan Watulimo', 'kecamatan', 'aktif'],
                    ['Desa Prigi', 'desa', 'aktif'],
                    ['Kelurahan Sumbergedong', 'kelurahan', 'aktif'],
                ]
            ],
        ];

        if (! isset($templates[$type])) {
            return back()->with('error', 'Tipe template import tidak dikenali.');
        }

        $tpl = $templates[$type];

        // Format 1: Microsoft Excel (.xlsx) — Tiap kolom terpisah rapi
        if ($format === 'xlsx') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($tpl['sheet_title']);

            // Baris Header
            $sheet->fromArray([$tpl['headers']], null, 'A1');

            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($tpl['headers']));

            // Styling Header: Emerald Gelap dengan teks putih tebal
            $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(26);

            // Baris Contoh Data
            $sheet->fromArray($tpl['samples'], null, 'A2');

            $lastRow = count($tpl['samples']) + 1;

            // Border tipis untuk seluruh tabel template
            $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CBD5E1'],
                    ],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Auto-size kolom
            for ($c = 1; $c <= count($tpl['headers']); $c++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }

            $filename = $tpl['filename'] . '.xlsx';

            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        // Format 2: CSV (.csv) Standar
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$tpl['filename']}.csv\"",
        ];

        return response()->stream(function () use ($tpl) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, $tpl['headers']);
            foreach ($tpl['samples'] as $sample) {
                fputcsv($handle, $sample);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Preview File Excel (.xlsx, .xls) atau CSV sebelum Eksekusi Import.
     */
    public function preview(Request $request): View|RedirectResponse
    {
        $request->validate([
            'tipe' => ['required', 'in:penugasan,tindak_lanjut,objek'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:10240'],
        ], [
            'file.mimes' => 'Format berkas harus berupa Excel (.xlsx, .xls) atau CSV (.csv).',
            'file.max'   => 'Ukuran berkas maksimal 10 MB.',
        ]);

        $type = $request->tipe;
        $file = $request->file('file');

        $rows = $this->importService->parseFile($file);

        if (empty($rows)) {
            return back()->with('error', 'File import kosong atau kolom tidak dapat dibaca.');
        }

        $header = array_shift($rows);
        $totalRows = count($rows);
        $previewRows = array_slice($rows, 0, 10);

        // Simpan file sementara di session / temp storage jika user ingin konfirmasi
        $tempPath = $file->store('temp_imports');

        return view('import.preview', compact('type', 'header', 'previewRows', 'totalRows', 'tempPath'));
    }

    /**
     * Eksekusi Import Data.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tipe'      => ['required', 'in:penugasan,tindak_lanjut,objek'],
            'file'      => ['nullable', 'file', 'mimes:xlsx,xls,csv,txt', 'max:10240'],
            'temp_path' => ['nullable', 'string'],
        ]);

        $type = $request->tipe;

        if ($request->hasFile('file')) {
            $rows = $this->importService->parseFile($request->file('file'));
        } elseif ($request->temp_path && \Illuminate\Support\Facades\Storage::exists($request->temp_path)) {
            $fullPath = \Illuminate\Support\Facades\Storage::path($request->temp_path);
            $rows = $this->importService->parseFile($fullPath);
            \Illuminate\Support\Facades\Storage::delete($request->temp_path);
        } else {
            return back()->with('error', 'File import tidak ditemukan.');
        }

        $userId = auth()->id() ?? 1;

        $result = match($type) {
            'penugasan'     => $this->importService->importPenugasan($rows, $userId),
            'tindak_lanjut' => $this->importService->importTindakLanjut($rows, $userId),
            'objek'         => $this->importService->importObjek($rows, $userId),
        };

        $msg = "✓ Berhasil mengimpor {$result['success']} data {$type}.";
        if (! empty($result['errors'])) {
            $msg .= ' Beberapa baris dilewati karena kendala format.';
            return redirect()->route('import.index', ['tab' => $type])
                ->with('status', $msg)
                ->with('import_errors', $result['errors']);
        }

        return redirect()->route('import.index', ['tab' => $type])->with('status', $msg);
    }
}

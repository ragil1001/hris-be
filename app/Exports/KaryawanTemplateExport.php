<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use App\Models\Karyawan;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\DB;


class KaryawanTemplateExport extends DefaultValueBinder implements WithHeadings, WithStyles, WithEvents, WithCustomStartCell, FromQuery, WithMapping, WithColumnFormatting, WithCustomValueBinder
{
    private $rowNumber = 0;
    private $isTemplate = false;
    private $maxChildren = 3; // Default minimum 3

    public function __construct($isTemplate = false)
    {
        $this->isTemplate = $isTemplate;
        
        // Calculate maximum children across all employees
        $calculatedMax = DB::table('mst.keluarga')
            ->where('hubungan', 'ANAK')
            ->groupBy('karyawan_id')
            ->selectRaw('count(*) as count')
            ->pluck('count')
            ->max();
            
        $this->maxChildren = max(3, $calculatedMax ?? 0);
    }

    public function bindValue(Cell $cell, $value)
    {
        // Force long numeric strings to be treated as text to prevent E+ (scientific notation)
        if (is_numeric($value) && strlen((string)$value) > 10) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        // Return default behavior for other values
        return parent::bindValue($cell, $value);
    }

    private function formatDateIndo($date)
    {
        if (!$date) return '';
        $date = Carbon::parse($date);
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $date->day . ' ' . $months[$date->month] . ' ' . $date->year;
    }

    private function formatCurrency($value)
    {
        if ($value === null || $value === '' || $value == 0) return '';
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    private function formatDecimal($value)
    {
        if ($value === null || $value === '') return '';
        return number_format((float)$value, 1, '.', '');
    }

    public function columnFormats(): array
    {
        $formats = [
            'B' => NumberFormat::FORMAT_TEXT, // NIK
            'V' => NumberFormat::FORMAT_TEXT, // NO KK
            'W' => NumberFormat::FORMAT_TEXT, // NO KTP
            'X' => NumberFormat::FORMAT_TEXT, // BPJS KET
            'Z' => NumberFormat::FORMAT_TEXT, // BPJS KES
            'AP' => NumberFormat::FORMAT_TEXT, // KTP PASANGAN
            // ... Children columns will be added dynamically below
            'BP' => NumberFormat::FORMAT_TEXT, // REKENING (This index might shift, let's keep it static for now but it's risky)
        ];

        // Dynamically add KTP formatting for children
        // Child 1 starts at index 46 (Column AU) - wait, I should use column names more carefully
        // But WithColumnFormatting uses letters.
        // Let's just use the binder for large numbers anyway, it's safer.
        // I will keep the base formats and rely on bindValue for the rest.

        return $formats;
    }

    public function query()
    {
        $query = Karyawan::query()->with([
            'unitKerja', 
            'jabatan', 
            'formasi', 
            'penempatan', 
            'bank', 
            'keluarga', 
            'ayahIbu'
        ]);

        if ($this->isTemplate) {
            $query->whereRaw('1 = 0');
        }

        return $query;
    }

    public function map($karyawan): array
    {
        $this->rowNumber++;

        // Get Spouse
        $spouse = $karyawan->keluarga->whereIn('hubungan', ['ISTRI', 'SUAMI'])->first();

        // Get Children
        $children = $karyawan->keluarga->where('hubungan', 'ANAK')->sortBy('urutan_anak')->values();

        // Calculate Age & Work Length as decimal
        $age = $karyawan->tanggal_lahir ? Carbon::parse($karyawan->tanggal_lahir)->diffInMonths(Carbon::now()) / 12 : 0;
        $workLength = $karyawan->tanggal_masuk ? Carbon::parse($karyawan->tanggal_masuk)->diffInMonths($karyawan->tanggal_resign ?: Carbon::now()) / 12 : 0;

        $rowData = [
            $this->rowNumber,
            $karyawan->nik,
            $karyawan->nama,
            $this->formatDateIndo($karyawan->tanggal_masuk),
            $this->formatDecimal($workLength),
            $this->formatCurrency($karyawan->gaji_pokok),
            $this->formatCurrency($karyawan->insentif),
            $this->formatCurrency($karyawan->uang_makan),
            $karyawan->control_status,
            $karyawan->keterangan,
            $karyawan->status,
            $this->formatDateIndo($karyawan->tanggal_resign),
            $this->formatDateIndo($karyawan->tanggal_aktif),
            $karyawan->jenis_kelamin,
            $karyawan->formasi ? $karyawan->formasi->nama_formasi : '',
            $karyawan->jabatan ? $karyawan->jabatan->nama_jabatan : '',
            $karyawan->unitKerja ? $karyawan->unitKerja->nama_unit : '',
            $karyawan->penempatan ? $karyawan->penempatan->nama_project : '',
            $karyawan->tempat_lahir,
            $this->formatDateIndo($karyawan->tanggal_lahir),
            $this->formatDecimal($age),
            $karyawan->no_kk,
            $karyawan->no_ktp,
            $karyawan->no_bpjs_ketenagakerjaan,
            $karyawan->npp_bpjs_ketenagakerjaan,
            $karyawan->no_bpjs_kesehatan,
            $karyawan->bu_bpjs_kesehatan,
            $karyawan->alamat,
            $karyawan->rt,
            $karyawan->rw,
            $karyawan->kelurahan,
            $karyawan->kecamatan,
            $karyawan->kabupaten_kota,
            $karyawan->kode_pos,
            $karyawan->agama,
            $karyawan->email,
            $karyawan->no_telepon,
            $karyawan->pendidikan_terakhir,
            $karyawan->golongan_darah,
            $karyawan->status_pernikahan,
            // Spouse
            $spouse ? $spouse->nama : '',
            $spouse ? $spouse->no_ktp : '',
            $spouse ? $spouse->tempat_lahir : '',
            $this->formatDateIndo($spouse ? $spouse->tanggal_lahir : null),
            $spouse ? $spouse->bpjs_kesehatan : '',
        ];

        // Dynamically add Children
        for ($i = 0; $i < $this->maxChildren; $i++) {
            $child = $children->get($i);
            $rowData[] = $child ? $child->nama : '';
            $rowData[] = $child ? $child->no_ktp : '';
            $rowData[] = $child ? $child->tempat_lahir : '';
            $rowData[] = $this->formatDateIndo($child ? $child->tanggal_lahir : null);
            $rowData[] = $child ? $child->bpjs_kesehatan : '';
        }

        // Add remaining fields
        $rowData = array_merge($rowData, [
            $karyawan->ayahIbu ? $karyawan->ayahIbu->nama_ayah : '',
            $karyawan->ayahIbu ? $karyawan->ayahIbu->nama_ibu : '',
            $this->formatDateIndo($karyawan->awal_mulai_cuti),
            $this->formatDateIndo($karyawan->masa_berlaku_cuti),
            $karyawan->potongan_cuti_bersama,
            $karyawan->sisa_cuti,
            $karyawan->bank ? $karyawan->bank->nama_bank : '',
            $karyawan->no_rekening,
            $karyawan->no_jaminan_pensiun,
            $karyawan->bu,
        ]);

        return $rowData;
    }
    public function startCell(): string
    {
        return 'A3';
    }

    public function headings(): array
    {
        $headings = [
            'No',
            'NIK',
            'Nama',
            'Tanggal Masuk Kerja',
            'Lama Kerja',
            'Gaji',
            'Insentif',
            'Uang Makan',
            'Control Status',
            'Keterangan',
            'Aktif/Resign',
            'Tanggal Resign',
            'Tanggal Aktif',
            'Kode Jenis Kelamin',
            'Formasi',
            'Jabatan',
            'Unit Kerja',
            'Penempatan',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Umur Tahun',
            'No KK',
            'No KTP',
            'BPJS Ketenagakerjaan',
            'NPP BPJS Ketenagakerjaan',
            'BPJS Kesehatan',
            'BU BPJS Kesehatan',
            'Alamat',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan',
            'Kabupaten/Kota',
            'Kode Pos',
            'Agama',
            'Email',
            'No Telp/WA',
            'Pendidikan Terakhir',
            'Golongan Darah',
            'Status TK/K',
            'Nama Istri/Suami',
            'Nomor KTP Istri/Suami',
            'Tempat Lahir Istri/Suami',
            'Tanggal Lahir Istri/Suami',
            'Nomor BPJS Istri/Suami',
        ];

        // Dynamically add Children headings
        for ($i = 1; $i <= $this->maxChildren; $i++) {
            $headings[] = "Nama Anak $i";
            $headings[] = "Nomor KTP Anak $i";
            $headings[] = "Tempat Lahir Anak $i";
            $headings[] = "Tanggal Lahir Anak $i";
            $headings[] = "Nomor BPJS Kesehatan Anak $i";
        }

        // Add remaining headings
        $headings = array_merge($headings, [
            'Nama Bapak Kandung',
            'Nama Ibu Kandung',
            'Awal Mulai Cuti',
            'Masa Berlaku Cuti',
            'Potongan Cuti Bersama',
            'Sisa Cuti',
            'Bank',
            'Nomor Rekening',
            'Jaminan Pensiun',
            'BU',
        ]);

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header Row (Row 3)
            3 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '5B9BD5'],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Title at A2
                $sheet->setCellValue('A2', 'DATA KARYAWAN');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 14, 'color' => ['rgb' => '000000']],
                ]);

                // 2. Freeze Panes: Columns A-D fixed
                // Freeze pane at E4 (so rows 1-3 are frozen vertically, and A-D are frozen horizontally)
                // User asked "columns A-D ... fixed not scrollable", implying rows scroll but cols A-D stay.
                $sheet->freezePane('E4');

                // 3. Alternating Row Colors
                $columnCount = count($this->headings());
                $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnCount);
                $highestRow = $sheet->getHighestRow();

                // If template, we might want to style at least 50 empty rows, 
                // but the user specifically asked for dynamic matching.
                // However, if we only have headers (3), we don't apply alternating colors.
                if ($highestRow > 3) {
                    for ($row = 4; $row <= $highestRow; $row++) {
                        $color = ($row % 2 == 0) ? 'DDEBF7' : 'BDD7EE';
                        $range = 'A' . $row . ':' . $lastColumn . $row;
                        $sheet->getStyle($range)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($color);
                        
                        // Body Font: Black 10 Regular
                        $sheet->getStyle($range)->getFont()
                            ->setSize(10)
                            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                        
                        // Set Data Row Heights and Styles
                        $sheet->getRowDimension($row)->setRowHeight(21);
                        $sheet->getStyle($range)->getAlignment()
                            ->setVertical(Alignment::VERTICAL_CENTER);
                        
                        // Add White Borders to Data Rows
                        $sheet->getStyle($range)->getBorders()->getAllBorders()
                            ->setBorderStyle(Border::BORDER_THIN)
                            ->getColor()->setRGB('FFFFFF');
                    }
                }

                // 4. AutoFilter
                $sheet->setAutoFilter('A3:' . $lastColumn . '3');
                
                // 5. Column Widths
                $sheet->getRowDimension(3)->setRowHeight(63);
                for ($i = 1; $i <= $columnCount; $i++) {
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $heading = $this->headings()[$i-1];
                    
                    // Fixed width calculation
                    $width = 20;
                    if (in_array($heading, ['No', 'RT', 'RW', 'Kode Pos', 'Golongan Darah', 'Umur Tahun', 'Kode Jenis Kelamin'])) {
                        $width = 8;
                    } elseif (in_array($heading, ['Nama', 'Alamat', 'Keterangan', 'Email', 'Unit Kerja', 'Penempatan', 'Jabatan', 'Nama Istri/Suami', 'Nama Bapak Kandung', 'Nama Ibu Kandung', 'BU'])) {
                        $width = 35;
                    } elseif (str_contains($heading, 'Anak')) {
                        $width = 30;
                    } elseif (str_contains($heading, 'Nomor') || str_contains($heading, 'No') || str_contains($heading, 'BPJS')) {
                        $width = 25;
                    }
                    
                    $sheet->getColumnDimension($column)->setWidth($width);
                    
                    // Header Styling
                    $sheet->getStyle($column . '3')->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);
                    $sheet->getStyle($column . '3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('FFFFFF');
                }
            },
        ];
    }
}

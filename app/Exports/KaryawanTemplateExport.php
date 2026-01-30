<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class KaryawanTemplateExport implements WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    public function startCell(): string
    {
        return 'A3';
    }

    public function headings(): array
    {
        return [
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
            'Nama Anak 1',
            'Nomor KTP Anak 1',
            'Tempat Lahir Anak 1',
            'Tanggal Lahir Anak 1',
            'Nomor BPJS Kesehatan Anak 1',
            'Nama Anak 2',
            'Nomor KTP Anak 2',
            'Tempat Lahir Anak 2',
            'Tanggal Lahir Anak 2',
            'Nomor BPJS Kesehatan Anak 2',
            'Nama Anak 3',
            'Nomor KTP Anak 3',
            'Tempat Lahir Anak 3',
            'Tanggal Lahir Anak 3',
            'Nomor BPJS Kesehatan Anak 3',
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
        ];
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

                for ($row = 4; $row <= 53; $row++) {
                    $color = ($row % 2 == 0) ? 'DDEBF7' : 'BDD7EE';
                    $range = 'A' . $row . ':' . $lastColumn . $row;
                    $sheet->getStyle($range)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($color);
                    
                    // Body Font: Black 10 Regular
                    $sheet->getStyle($range)->getFont()
                        ->setSize(10)
                        ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                }

                // 4. AutoFilter
                $sheet->setAutoFilter('A3:' . $lastColumn . '3');
                
                // 5. Column Widths & Row Heights
                $columnCount = count($this->headings());
                
                // Set Header Height
                $sheet->getRowDimension(3)->setRowHeight(63);
                
                for ($i = 1; $i <= $columnCount; $i++) {
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $heading = $this->headings()[$i-1];
                    
                    // Varied Column Widths
                    $width = 20; // Default
                    if (in_array($heading, ['No', 'RT', 'RW', 'Kode Pos', 'Golongan Darah', 'Umur Tahun', 'Kode Jenis Kelamin'])) {
                        $width = 8;
                    } elseif (in_array($heading, ['Nama', 'Alamat', 'Keterangan', 'Email', 'Unit Kerja', 'Penempatan', 'Jabatan', 'Nama Istri/Suami', 'Nama Bapak Kandung', 'Nama Ibu Kandung'])) {
                        $width = 35;
                    } elseif (str_contains($heading, 'Anak')) {
                        $width = 30;
                    } elseif (str_contains($heading, 'Nomor') || str_contains($heading, 'No') || str_contains($heading, 'BPJS')) {
                        $width = 25;
                    }
                    
                    $sheet->getColumnDimension($column)->setWidth($width);
                    
                    // Center align headers horizontally and vertically
                    $sheet->getStyle($column . '3')->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);

                    // Add White Borders to Header
                    $sheet->getStyle($column . '3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('FFFFFF');
                }

                // Set Data Row Heights and Styles (Row 4 to 53)
                for ($row = 4; $row <= 53; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(21);
                    $range = 'A' . $row . ':' . $lastColumn . $row;
                    
                    $sheet->getStyle($range)->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER);
                    
                    // Add White Borders to Data Rows
                    $sheet->getStyle($range)->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN)
                        ->getColor()->setRGB('FFFFFF');
                }
            },
        ];
    }
}

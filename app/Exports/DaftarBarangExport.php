<?php

namespace App\Exports;

use App\Models\Barang;
use App\Models\barangs;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DaftarBarangExport implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            '#',
            'Nama Barang',
            'Category',
            'Hitung Stok',
            'Harga Modal',
            'Harga Jual Satuan',
            'Harga Jual Grosir',
            'Harga Jual Reseller',
            'Stok'
        ];
    }
    public function title(): string
    {
        return 'Daftar Barang';
    }
    public function collection()
    {
        $barangs = Barang::with('category')->orderBy('created_at', 'desc')->get();

        $totalHargaModal = 0;
        $totalHargaJualSatuan = 0;
        $totalHargaJualGrosir = 0;
        $totalHargaJualReseller = 0;

        $data = $barangs->map(function ($x, $i) use (&$totalHargaModal, &$totalHargaJualSatuan, &$totalHargaJualGrosir, &$totalHargaJualReseller) {
            $totalHargaModal += $x->harga_modal;
            $totalHargaJualSatuan += $x->harga_jual_satuan;
            $totalHargaJualGrosir += $x->harga_jual_grosir;
            $totalHargaJualReseller += $x->harga_jual_reseller;

            return [
                'number' => $i + 1,
                'name' => $x->name,
                'category' => $x->category->name,
                'hitung_stok' => $x->hitung_stok ? 'Ya' : 'tidak',
                'harga_modal' => format_rupiah($x->harga_modal),
                'harga_jual_satuan' => format_rupiah($x->harga_jual_satuan),
                'harga_jual_grosir' => format_rupiah($x->harga_jual_grosir),
                'harga_jual_reseller' => format_rupiah($x->harga_jual_reseller),
                'stok' => $x->hitung_stok ? strval($x->stok) : '-'
            ];
        });

        $totals = [
            'number' => '',
            'name' => 'Total',
            'category' => '',
            'hitung_stok' => '',
            'harga_modal' => format_rupiah($totalHargaModal),
            'harga_jual_satuan' => format_rupiah($totalHargaJualSatuan),
            'harga_jual_grosir' => format_rupiah($totalHargaJualGrosir),
            'harga_jual_reseller' => format_rupiah($totalHargaJualReseller),
            'stok' => ''
        ];

        // Add the totals as the first row
        $collection = collect([$totals])->merge($data);

        return $collection;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF4CAF50',
                ],
            ],
        ]);

        // Styling the totals row
        $sheet->getStyle('A2:I2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF0000', // Red background for totals
                ],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 30,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
        ];
    }
}

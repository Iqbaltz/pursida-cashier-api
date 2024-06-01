<?php

namespace App\Exports;

use App\Models\BarangTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DaftarTransaksiBarangExport implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    public function headings(): array
    {
        return [
            '#',
            'Tanggal Transaksi',
            'Nama Barang',
            'Nama Supplier',
            'Harga Beli',
            'Jumlah',
            'Total',
        ];
    }
    public function title(): string
    {
        return 'Daftar Transaksi Barang';
    }
    public function collection()
    {
        $query = BarangTransaction::with('supplier', 'barang')->orderBy('transaction_date', 'desc');
        if ($this->startDate) {
            $query->where('transaction_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->where('transaction_date', '<=', $this->endDate);
        }
        $barang_transactions = $query->get();

        $totalHargaBeli = 0;
        $totalJumlah = 0;
        $totalTotal = 0;

        $data = $barang_transactions->map(function ($x, $i) use (&$totalHargaBeli, &$totalJumlah, &$totalTotal) {
            $totalHargaBeli += $x->harga_beli;
            $totalJumlah += $x->jumlah;
            $totalTotal += ($x->harga_beli * $x->jumlah);

            return [
                'number' => $i + 1,
                'transaction_date' => format_indonesia_datetime($x->transaction_date),
                'barang' => $x->barang->name,
                'supplier' => $x->supplier->name,
                'harga_beli' => format_rupiah($x->harga_beli),
                'jumlah' => strval($x->jumlah),
                'total' => format_rupiah($x->harga_beli * $x->jumlah),
            ];
        });

        $totals = [
            'number' => '',
            'transaction_date' => 'Total',
            'name' => '',
            'supplier' => '',
            'harga_beli' => format_rupiah($totalHargaBeli),
            'jumlah' => $totalJumlah,
            'total' => format_rupiah($totalTotal),
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
        ];
    }
}

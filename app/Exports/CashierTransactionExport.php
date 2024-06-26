<?php

namespace App\Exports;

use App\Models\CashierTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashierTransactionExport implements FromCollection, WithTitle, WithHeadings, WithColumnWidths, WithEvents, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Daftar Transaksi Kasir';
    }

    public function headings(): array
    {
        return [
            '#',
            '##',
            'Nomor Transaksi',
            'Tanggal Transaksi',
            'Nama Kasir',
            'Nama Pelanggan',
            'Metode Bayar',
            'Nama Barang',
            'Harga Modal',
            'Type Harga Jual',
            'Harga Jual',
            'QTY',
            'Jumlah',
            'Subtotal',
            'Diskon',
            'Total',
            'Kas Masuk',
            'Kekurangan Pembayaran',
            'Profit',
            'Status'
        ];
    }

    public function collection()
    {
        $query = CashierTransaction::with([
            'cashier', 'customer', 'payment_method', 'transaction_items'
        ]);

        if ($this->startDate) {
            $query->where('transaction_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->where('transaction_date', '<=', $this->endDate);
        }

        $cashier_transactions = $query->get();

        $data = [];
        $totalAllModal = 0;
        $totalAllHargaJual = 0;
        $totalAllQty = 0;
        $totalAllJumlah = 0;
        $totalAllSubtotal = 0;
        $totalAllDiscount = 0;
        $totalAllTotal = 0;
        $totalAllCashIn = 0;
        $totalAllRemainingPayment = 0;
        $totalAllProfit = 0;
        $totalAllStatus = 0;
        $i = 0;
        foreach ($cashier_transactions as $transaction) {
            $i++;
            $j = 0;
            $totalPrice = 0;
            $totalProfit = 0;
            foreach ($transaction->transaction_items as $item) {
                $j++;
                $total_per_barang = $item->qty * $item->price_per_barang;
                $totalPrice += $total_per_barang;
                $profit = ($item->qty * $item->price_per_barang) - ($item->qty * $item->harga_modal);
                $totalProfit += $profit;

                $totalAllModal += $item->harga_modal;
                $totalAllHargaJual += $item->price_per_barang;
                $totalAllQty += $item->qty;
                $totalAllJumlah += $total_per_barang;
                $totalAllSubtotal += $totalPrice;
                $totalAllDiscount += $item->discount;
                $totalAllTotal += $totalPrice - $item->discount;
                $totalAllCashIn += $transaction->payment_amount;
                $totalAllRemainingPayment += $totalPrice - $item->discount - $transaction->payment_amount;
                $totalAllProfit += $profit;
                if ($transaction->status) {
                    $totalAllStatus++;
                }

                $data[] = [
                    'number' => $i,
                    'number2' => $j,
                    'transaction_number' => $transaction->transaction_number,
                    'transaction_date' => $transaction->transaction_date,
                    'cashier_name' => $transaction->cashier_name,
                    'customer_name' => $transaction->customer_name,
                    'payment_method' => $transaction->payment_method->name,
                    'item_name' => $item->barang_name,
                    'cost_price' => $item->harga_modal ?? '0',
                    'price_type' => 'Harga ' .  $item->transaction_type,
                    'selling_price' => strval($item->price_per_barang),
                    'qty' => $item->qty,
                    'jumlah' => $total_per_barang ?? '0',
                    'subtotal' => $totalPrice ?? '0',
                    'discount' => $item->discount ?? '0',
                    'total' => ($totalPrice - $item->discount) ?? '0',
                    'cash_in' => $transaction->payment_amount ?? '0',
                    'remaining_payment' => ($totalPrice - $item->discount - $transaction->payment_amount) ?? '0',
                    'profit' => $totalProfit ?? '0',
                    'status' => $transaction->status ? 'Lunas' : 'Belum Lunas',
                ];
            }
        }
        $totals = [
            'number' => '',
            'number2' => '',
            'transaction_number' => 'Total',
            'transaction_date' => '',
            'cashier_name' => '',
            'customer_name' => '',
            'payment_method' => '',
            'item_name' => '',
            'cost_price' => strval($totalAllModal),
            'price_type' => '',
            'selling_price' => strval($totalAllHargaJual),
            'qty' => strval($totalAllQty),
            'jumlah' => strval($totalAllJumlah),
            'subtotal' => strval($totalAllSubtotal),
            'discount' => strval($totalAllDiscount),
            'total' => strval($totalAllTotal),
            'cash_in' => strval($totalAllCashIn),
            'remaining_payment' => strval($totalAllRemainingPayment),
            'profit' => strval($totalAllProfit),
            'status' => strval($totalAllStatus),
        ];

        $collection = collect([$totals])->merge($data);
        return $collection;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 10,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 30,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 10,
            'K' => 20,
            'L' => 20,
            'M' => 20,
            'N' => 20,
            'O' => 20,
            'P' => 20,
            'Q' => 20,
            'R' => 20,
            'S' => 20,
            'T' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $data = $sheet->toArray();
                $rowCount = count($data);

                for ($i = 1; $i < $rowCount; $i++) {
                    $transactionNumber = $data[$i][2]; // Changed from $data[$i][0] to $data[$i][1]
                    $startRow = $i + 1;

                    while ($i + 1 < $rowCount && $data[$i + 1][2] == $transactionNumber) { // Changed from $data[$i + 1][0] to $data[$i + 1][1]
                        $i++;
                    }

                    $endRow = $i + 1;

                    if ($startRow != $endRow) {
                        $sheet->mergeCells("A{$startRow}:A{$endRow}");
                        $sheet->mergeCells("C{$startRow}:C{$endRow}");
                        $sheet->mergeCells("D{$startRow}:D{$endRow}");
                        $sheet->mergeCells("E{$startRow}:E{$endRow}");
                        $sheet->mergeCells("F{$startRow}:F{$endRow}");
                        $sheet->mergeCells("G{$startRow}:G{$endRow}");
                        $sheet->mergeCells("M{$startRow}:M{$endRow}");
                        $sheet->mergeCells("N{$startRow}:N{$endRow}");
                        $sheet->mergeCells("O{$startRow}:O{$endRow}");
                        $sheet->mergeCells("P{$startRow}:P{$endRow}");
                        $sheet->mergeCells("Q{$startRow}:Q{$endRow}");
                        $sheet->mergeCells("R{$startRow}:R{$endRow}");
                        $sheet->mergeCells("S{$startRow}:S{$endRow}");

                        // Set the values from the top item to the merged cells
                        $sheet->setCellValue("A{$startRow}", $data[$startRow - 1][0]);
                        $sheet->setCellValue("C{$startRow}", $data[$startRow - 1][2]);
                        $sheet->setCellValue("D{$startRow}", $data[$startRow - 1][3]);
                        $sheet->setCellValue("E{$startRow}", $data[$startRow - 1][4]);
                        $sheet->setCellValue("F{$startRow}", $data[$startRow - 1][5]);
                        $sheet->setCellValue("G{$startRow}", $data[$startRow - 1][6]);
                        $sheet->setCellValue("M{$startRow}", $data[$startRow - 1][12]);
                        $sheet->setCellValue("N{$startRow}", $data[$startRow - 1][13]);
                        $sheet->setCellValue("O{$startRow}", $data[$startRow - 1][14]);
                        $sheet->setCellValue("P{$startRow}", $data[$startRow - 1][15]);
                        $sheet->setCellValue("Q{$startRow}", $data[$startRow - 1][16]);
                        $sheet->setCellValue("R{$startRow}", $data[$startRow - 1][17]);
                        $sheet->setCellValue("S{$startRow}", $data[$startRow - 1][18]);
                    }
                }

                // Center align the merged cells vertically and horizontally
                $sheet->getStyle('A1:S' . $rowCount)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1:S' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Added horizontal center alignment
            },
        ];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:T1')->applyFromArray([
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
        $sheet->getStyle('A2:T2')->applyFromArray([
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
}

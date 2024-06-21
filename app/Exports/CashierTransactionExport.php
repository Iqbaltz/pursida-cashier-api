<?php

namespace App\Exports;

use App\Models\CashierTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CashierTransactionExport implements FromCollection, WithTitle, WithHeadings, WithColumnWidths
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
    public function title(): string
    {
        return 'Daftar Transaksi Kasir';
    }
    public function headings(): array
    {
        return [
            '#',
            'Nomor Transaksi',
            'Tanggal Transaksi',
            'Nama Kasir',
            'Nama Pelanggan',
            'Total Barang',
            'Potongan',
            'Total Biaya',
            'Kas Masuk',
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
        $data = $cashier_transactions->map(function ($x, $i) {
            return [
                'number' => $i + 1,
                'transaction_number' => $x->transaction_number,
                'transaction_date' => $x->transaction_date,
                'cashier_name' => $x->cashier_name,
                'customer_name' => $x->customer_name,
                'total_items' => $x->transaction_items->count(),
                'discount' => $x->discount,
                'total_payment' => $x->transaction_items->sum(function ($item) {
                    return $item->qty * $item->price_per_barang;
                }),
                'cash_in' => $x->payment_amount,
                'status' => $x->payment_status == true ? 'Lunas' : 'Belum Lunas'
            ];
        });
        return $data;
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
            'J' => 20,
        ];
    }
}

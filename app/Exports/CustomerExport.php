<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomerExport implements FromCollection, WithTitle, WithHeadings, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            '#',
            'Nama Customer',
            'Alamat',
            'Nomor Telepon',
            'Tanggal Pembuatan'
        ];
    }
    public function title(): string
    {
        return 'Daftar Customer';
    }
    public function collection()
    {
        $customer = Customer::orderBy('created_at', 'desc')->get();
        return $customer->map(function ($x, $i) {
            return [
                'number' => $i + 1,
                'name' => $x->name,
                'address' => $x->address,
                'phone_number' => $x->phone_number,
                'created_at' => $x->created_at
            ];
        });
    }
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 60,
            'D' => 20,
            'E' => 20,
        ];
    }
}

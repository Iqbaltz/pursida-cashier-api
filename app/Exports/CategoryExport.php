<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CategoryExport implements FromCollection, WithTitle, WithHeadings, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            '#',
            'Nama Kategori',
        ];
    }
    public function title(): string
    {
        return 'Daftar Kategori';
    }
    public function collection()
    {
        $customer = Category::all();
        return $customer->map(function ($x, $i) {
            return [
                'number' => $i + 1,
                'name' => $x->name,
            ];
        });
    }
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 60,
        ];
    }
}

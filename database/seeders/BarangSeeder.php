<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangs = $this->readCSV('data_panglong_ud.csv');
        $this->seed_barangs($barangs);
    }
    private function seed_barangs($barangs_from_csv)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('barangs')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        foreach ($barangs_from_csv as $brg) {
            $barang = new Barang();
            $barang->name = $brg['name'];
            $barang->category_id = $this->get_category_id($brg['category']);
            $barang->hitung_stok = $brg['hitung_stok'] == '' ? false : true;
            $barang->harga_modal = $brg['harga_modal'] == '' ? 0 : (int)$brg['harga_modal'];
            $barang->harga_jual_satuan = $brg['harga_satuan'] == '' ? 0 : (int)$brg['harga_satuan'];
            $barang->harga_jual_grosir = $brg['harga_grosir'] == '' ? 0 : (int)$brg['harga_grosir'];
            $barang->harga_jual_reseller = $brg['harga_reseller'] == '' ? 0 : (int)$brg['harga_reseller'];
            $barang->stok = $brg['stok'] == '' ? 0 : (int)$brg['stok'];
            $barang->save();
            $barang->break;
        }
    }
    private function get_category_id($category_name)
    {
        $category = Category::where('name', $category_name)->first();
        if (!$category) {
            $category = new Category();
            $category->name = $category_name;
            $category->save();
        }
        return $category->id;
    }
    private function readCSV($csvFile)
    {
        $data = [];
        $columns = [];
        $index = 0;
        $file_handle = fopen(public_path('/temp/' . $csvFile), 'r');
        // make csv format to array of objects
        while (!feof($file_handle)) {
            $row = fgetcsv($file_handle);
            if (gettype($row) !== 'boolean' && $row[0] !== "") {
                if ($index == 0) {
                    foreach ($row as $obj) {
                        array_push($columns, $obj);
                    }
                } else {
                    $rowData = [];
                    foreach ($row as $key => $obj) {
                        $rowData[$columns[$key]] = $obj;
                    }
                    array_push($data, $rowData);
                }
            }
            $index++;
        }
        fclose($file_handle);
        return $data;
    }
}

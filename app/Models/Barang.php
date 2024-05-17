<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $fillable = [
        "slug",
        "name",
        "category_id",
        "hitung_stok",
        "harga_modal",
        "harga_jual_satuan",
        "harga_jual_grosir",
        "harga_jual_reseller",
        "stok"
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

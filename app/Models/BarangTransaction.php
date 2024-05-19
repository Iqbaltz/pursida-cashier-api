<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date', 'supplier_id', 'barang_id', 'harga_beli', 'jumlah'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}

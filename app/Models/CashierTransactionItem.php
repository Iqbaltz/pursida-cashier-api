<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierTransactionItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'cashier_transaction_id',
        'barang_id',
        'barang_name',
        'price_per_barang',
        'transaction_type',
        'qty',
    ];

    public function cashier_transaction()
    {
        $this->belongsTo(CashierTransaction::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}

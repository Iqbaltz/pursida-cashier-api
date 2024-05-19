<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'cashier_id',
        'customer_id',
        'payment_method_id',
        'discount'
    ];

    public function cashier()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethods::class);
    }

    public function transaction_items()
    {
        return $this->hasMany(CashierTransactionItem::class);
    }
}

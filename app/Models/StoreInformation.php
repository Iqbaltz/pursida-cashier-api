<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreInformation extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'name',
        'address',
        'phone_number',
        'content'
    ];
}

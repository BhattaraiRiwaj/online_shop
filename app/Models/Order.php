<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sub_total',
        'shipping',
        'coupon_code',
        'discount',
        'grand_total',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'country_id',
        'address',
        'appartment',
        'city',
        'state',
        'zip',
        'order_notes'
    ];
}

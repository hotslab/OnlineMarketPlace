<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = "products";

    protected $fillable = [
        "image",
        "name",
        "price",
        "currency",
        "currency_symbol"
    ];

    public function userProduct()
    {
        return $this->hasOne(UserProduct::class, "product_id");
    }
}

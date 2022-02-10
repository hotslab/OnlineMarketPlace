<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoughtProduct extends Model
{
    use HasFactory;

    protected $table = "bought_products";

    protected $fillable = [
        "email",
        "product_id"
    ];

    public function product()
    {
        return $this->hasOne(Product::class, "id");
    }
}

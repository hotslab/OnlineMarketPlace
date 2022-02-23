<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = "purchases";

    protected $fillable = [
        "email",
        "product_id",
        "paid_amount",
        "is_deposit"
    ];

    public function product()
    {
        return $this->hasOne(Product::class, "id", "product_id");
    }
}

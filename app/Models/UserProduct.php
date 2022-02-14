<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProduct extends Model
{
    use HasFactory;
    
    protected $table = "user_products";

    protected $fillable = [
        "product_id",
        "user_id"
    ];

    public function user()
    {
        return $this->hasOne(User::class, "id", "user_id");
    }

    public function product()
    {
        return $this->hasOne(Product::class, "id", "product_id");
    }
}


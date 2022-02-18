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
        "user_id",
        "product_id"
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

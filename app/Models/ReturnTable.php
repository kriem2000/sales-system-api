<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnTable extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "returns";

    public function products() {
        return $this->belongsToMany(Product::class, "returns_products", "return_id", "product_id")
            ->withPivot("quantity");
    }
}

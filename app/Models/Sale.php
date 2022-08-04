<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function products() {
        return $this->belongsToMany(Product::class, "sales_product", "sale_id", "product_id")
            ->withPivot("quantity");
    }

    public function bills() {
        return $this->belongsTo(Bill::class, "bill_id");
    }
}

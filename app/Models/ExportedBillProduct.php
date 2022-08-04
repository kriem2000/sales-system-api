<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportedBillProduct extends Model
{
    use HasFactory;
    protected $table = "exported_bills_products";
    protected $guarded = [];
}

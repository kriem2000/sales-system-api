<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FragmentedBill extends Model
{
    use HasFactory;
    protected $table = "fragmented_bill";
    protected  $guarded= [];
}

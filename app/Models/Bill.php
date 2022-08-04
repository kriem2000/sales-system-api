<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function paymentMethod() {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function billStatus() {
        return $this->belongsTo(BillStatus::class);
    }

    public function sale() {
        return $this->hasOne(Sale::class);
    }

    public function fragmented_bill() {
        return $this->hasMany(FragmentedBill::class, "bill_id");
    }

    public function returns() {
        return $this->hasMany(ReturnProducts::class);
    }
}

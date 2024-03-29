<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded =[];
    public $incrementing = false;
    protected $keyType = 'string';

    public function user() {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}

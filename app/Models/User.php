<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'password',
        'phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles() {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function asignRole($role) {
        if(is_string($role)) {
            $role = Role::whereName($role)->firstOrFail();
        }
        $this->roles()->sync($role,true);
    }

    public function permissions() {
        return $this->roles->map->permissions->flatten()->pluck("name")->unique();
    }

    public function products() {
        return $this->hasMany(Product::class, "created_by_id");
    }

    public function sales() {
        return $this->hasMany(Sale::class, "sold_by_id");
    }

    public function totalSales() {
        $totalSales = $this->sales->map->bills
            ->map(function($item, $key) {
            return $item["original_total"] - ($item["original_total"] * $item["applied_discount"]);
            })->sum();
        return $totalSales;
    }

    public function totalReturned() {
        $totalReturned = $this->sales->map->bills->pluck("total_returned")->sum();
        return $totalReturned;
    }
}

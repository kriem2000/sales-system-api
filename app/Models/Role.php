<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;
use App\Models\User;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        "name"
    ];

    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function permissions() {
        return $this->belongsToMany(Permission::class)->withTimestamps();;
    }

    public function allowTo($permission) {
        if (is_string($permission)) {
            $permission = Permission::whereName($permission)->firstOrFail();
        }
        $this->permissions()->sync($permission,false);
    }

}

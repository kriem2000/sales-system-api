<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponser;

    public function create(Request $request) {
        $data = $this->validateUsersInput($request);

        if ($data->fails()) {
             return $this->error("something went wrong !", $data->validated(), 400);
        } else {
            $data = $data->validated();
            $user = User::create([
                "email" => $data["email"],
                "name" => $data["name"],
                "lastname" => $data["lastname"] ?? "",
                "password" => Hash::make($data["password"]),
                "phone" => $data["phone"] ?? "",
                "created_at" => date("now"),
                "updated_at" => date("now"),
            ]);
            $user->asignRole($data["role"]);
             return $this->success($user ,"user created successfully",200);
        }
    }

    public function indexAll($role) {
            $allUsersWithRoles = DB::table("users")
                ->join("role_user", "users.id","=","role_user.user_id")
                ->join("roles","roles.id","role_user.role_id")
                ->select("users.*","roles.name as roleName")
                ->where("roles.name","like" ,$role)
                ->where("users.deleted_at", "=" , null)
                ->where("users.id", "!=", Auth::user()->id)
                ->get();
            return $this->success($allUsersWithRoles,sizeof($allUsersWithRoles),200);
    }

    public function update(User $user,Request $request) {
        $data = $this->validateUsersInput($request, true);
        if ($data->fails()) {
            return $this->error("something went wrong !", null, 400);
        } else {
            $data = $data->validated();
            $user->name = $data["name"] ?? $user->name;
            $user->lastName = $data["lastname"] ?? $user->lastName;
            $user->password = isset($data["password"]) ? Hash::make($data["password"]) : $user->password;
            $user->phone = $data["phone"] ?? $user->phone;
            $user->save();
            $user->asignRole($data["role"]);
            return $this->success($user ,"user updated successfully",200);
        }
    }

    public function validateUsersInput(Request $request, $update = false) {
       $data = Validator::make($request->all(), [
            "name" => "required|alpha|max:100",
            "lastname" => "alpha|max:100",
            "email" => $update ? "email|unique:users,email" : "email|required|unique:users,email",
            "password" => $update ? "min:3|max:20" : "required|min:3|max:20",
            "phone" => "Numeric|nullable",
            "role" => "exists:roles,name"
        ]);

       return $data;
    }

    public function delete(User $id) {
       $res= $id->delete();
        if($res) {
            return $this->success($res ,"user has been deleted successfully",200);
        }else {
            return $this->error("something went wrong !", null, 400);
        }
    }
}
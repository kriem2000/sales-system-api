<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController
{
    use ApiResponser;
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function signIn(Request $request)
    {
        $credentials  = Validator::make($request->only(["email","password"]),[
            "email" => "required|email",
            "password" => "required|min:3|max:20",
        ])->validated();

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $permissions = $user->permissions()->toArray();
            $success['token'] =  $user->createToken('ss_token',$permissions)->plainTextToken;
            $success['data'] =  $user;

            return $this->success($success, 'User login successfully.',200);
        }
        else{
            return $this->error('Unauthorized.',['error'=>'invalid User'],401);
        }
    }

    public function signOut(Request $request)
    {
        $user= Auth::user();
        // Revoke the token that was used to authenticate the current request...
        $user->currentAccessToken()->delete();
        // Revoke all tokens..
        $user->tokens()->delete();

        return $this->success(['success'=>'sign out successfully'],'sign out successfully.',200);
    }
}

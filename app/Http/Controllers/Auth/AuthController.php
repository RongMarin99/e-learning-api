<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\SendMail;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Contracts\Providers\Auth;

class AuthController extends Controller
{
    public function get(Request $request)
    {
        $user = User::where('user_type',1)
        ->select('name','description','image','social_media_url','contact_url')
        ->first();
        return $user;
    }

    public function register(Request $request)
    {
        if($request['social']==true){
            $Validator = Validator::make($request->all(), [
                'uid' => 'required'
            ]);
        }else{
            $Validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6'
            ]);
        }

        if($Validator->fails()){
            return response()->json([
                'message' => "Register Fail",
                'error' => $Validator->errors(),
            ]);
        }

        if($request['social']==true){
            $uid = User::where('uid',$request['uid'])
                        ->orWhere('email',$request['email'])
                        ->get();
            if(count($uid)>0){
                return response()->json([
                    'message' => false,
                ]);
            }else{
                $register = User::create([
                    'fname' => $request->fname,
                    'lname' => $request->lname,
                    'uid' => $request->uid,
                    'photo' =>$request->photo,
                    'username' => $request->username,
                    'email' => $request->email
                ]);
                $user = [
                    'user_id' => $register->id,
                    'username' => $register->username,
                    'email' => $register->email
                ];
                $this->html_email($user);
            }
        }else{
            $register = User::create([
                'fname' => $request->fname,
                'lname' => $request->lname,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $user = [
                'user_id' => $register->id,
                'username' => $register->username,
                'email' => $register->email
            ];
            $this->html_email($user);
        }
        return response()->json([
            'message' => 'Register Successfully.',
            'data' => $register
        ]);
    }

    public function update(Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'email' => 'required',
            'password'=>'required|min:6'
        ]);
        $user = User::find(auth()->user()->id);
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->password = Hash::make($request['password']);
        $user->save();

        $user = $request->only([
            'email',
            'password'
        ]);

        if( !$token = auth()->attempt($user)){
            return response()->json([
                'message' => 'Unauthorized'
            ]);
        }
        return $this->responseWithToken($token);
    }

    public function founder(Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'email' => 'required',
        ]);
        $user = User::find(auth()->user()->id);
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->social_media_url = $request['social_media_url'];
        $user->contact_url = $request['contact_url'];
        $user->image = $this->uploadImage($request['image']);
        $user->description = $request['description'];

        $user->save();
        return response()->json([
            'message' => 'Personal Information Update Successfully.',
            'user' => $user
        ]);
    }

    private function responseWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
			'user' => auth()->user(),
            'expired_date' => auth()->factory()->getTTL()
        ]);
    }

    public function login(Request $request)
    {
        $user = $request->only([
            'email',
            'password'
        ]);
        if( !$token = auth()->attempt($user)){
            return response()->json([
                'message' => 'Unauthorized'
            ]);
        }
        
        return $this->responseWithToken($token);
    }

    public function getUser()
    {
        return response()->json( auth()->user() );
    }

    public function logout(){
        auth()->logout();
        return response()->json([
            'message' => 'Logout Successfully.'
        ]);
    }

    public function verifyAccount($id)
    {
        $user = User::where('id', $id)->first();
  
        $message = 'Sorry your email cannot be identified.';
  
        if(!is_null($user) ){
            if(is_null($user->email_verified_at)) {
                $user->email_verified_at = Carbon::now();
                $user->save();
                $message = "Your e-mail is verified. You can now login.";
            } else {
                $message = "Your e-mail is already verified. You can now login.";
            }
        }
        return view("emails.confirm",[
            'message' => $message
        ]);
    }
}

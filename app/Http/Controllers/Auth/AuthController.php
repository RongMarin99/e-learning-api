<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Support\Str;
use App\Helper\Notification;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Models\Account_not_verify;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
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
            $user = User::where('email',$request['email'])
                        ->first();
            if(!is_null($user)){
                $token = auth()->login($user);
                return $this->responseWithToken($token);
            }else{
                $register = User::create([
                    'fname' => $request->fname,
                    'lname' => $request->lname,
                    'uid' => $request->uid,
                    'photo' =>$request->photo,
                    'username' => $request->username,
                    'email_verified_at' => Carbon::now(),
                    'email' => $request->email
                ]);
            }
        }else{
            $check = Account_not_verify::where('email',$request['email'])->first();
            if(!is_null($check)){
                return response(
                    [
                        'message' => 'This email has already register, please confirm your email adddres!.'
                    ]
                );
            }else{
                $register = Account_not_verify::create([
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
                $this->verify_email($user);
            }
           
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

    private function responseWithToken($token=null)
    {
        return response()->json([
            'access_token' => $token,
			'user' => auth()->user(),
            'expired_date' => auth()->factory()->getTTL() * 1440
        ]);
    }

    public function login(Request $request)
    {
        if($request['social']==true){
            $user = User::where('uid', $request['uid'])->first();
            if (!is_null($user)) {
              $token = auth()->login($user);
              return $this->responseWithToken($token);
            }else{
                return response()->json([
                    'message' => 'Unauthorized'
                ]); 
            }
        }else{
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
    }

    public function getUser(Request $request)
    {
        return $this->responseWithToken();
    }

    public function getCurrentUser()
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
        $expire_date = Carbon::now()->subDays(2);
        $user = Account_not_verify::where('id', $id)
               ->whereDate('created_at','>=',$expire_date)
               ->first();

        if(!is_null($user) ){
            $register = new User();
            $register->fname = $user->fname;
            $register->lname = $user->lname;
            $register->username = $user->username;
            $register->email = $user->email;
            $register->email_verified_at = Carbon::now();
            $register->password = $user->password;
            $register->save();
            if(!is_null($register->id)){
                Account_not_verify::find($id)->delete();
            }
            $message = "Your email is verified. You can now login.";
            //$message = "Your e-mail is already verified. You can now login.";
        }else {
            $message = 'Sorry this link has been expired.';
        }
        return view("emails.confirm",[
            'message' => $message
        ]);
    }

    public function requestResetPassword(Request $request){
        $email = $request['email'];
        $Validator = Validator::make($request->all(),[
            'email' => 'required'
        ]);
        if($Validator->fails()){
            return response()->json([
                'message' => 'The email field is required.',
                'error' => $Validator->errors(),
                'status' => false
            ]);
        }
        $user = User::where('email',$email)
                      ->whereNull('uid')
                      ->first();
        if(!is_null($user)){
            $token = Str::random(30);
            DB::table('password_resets')
            ->insert(
                [
                    'email' => $email, 
                    'token' => $token,
                    'created_at' => Carbon::now(),
                    'user_id' =>$user['id']
                ]
            );
            $this->reset_password($user,$token);
            return response()->json([
                'status' => true,
                'message' => 'Please check your email address. '
            ]);
        }
    }

    public function resetPassword($token){
        $reset_password_url = env('FRONT_END').'Reset-Password/'.$token;
        //redirect to front (reset new password)
        return Redirect::to($reset_password_url);
    }

    public function confirmResetPassword(Request $request){
        $token = $request['token'];
        $reset = PasswordReset::where('token',$token)->first();
        if(!is_null($reset)){
            $created_at = Carbon::parse($reset['created_at']);
            $current = Carbon::now();
            $expired_date = $current->diffInMinutes($created_at);
            if($expired_date>60){
                return response([
                    'message' => 'This link has been expired!'
                ]);
            }else{
                $data = $request['form'];
                $user = User::find($reset['user_id']);
                $user->password = Hash::make($data['new_password']);
                if($user->save()){
                    PasswordReset::where('user_id',$reset['user_id'])->delete();
                    return response([
                        'message' => 'Password has been changed!'
                    ]);
                }
            }
        }else{
            return response([
                'message' => 'Request url not found, Please check your email again!.'
            ]);
        }
    }

    public function pushNotification(Request $request){
        //Notification::send();
        $TOPIC_ADMIN = 'e-learning';
        $data = [
            'title' => 'Title from laravel',
            'body' => 'body testing from laravel'
        ];
        $fields = array
            (
                'to'  => '/topics/'.$TOPIC_ADMIN,
                'notification'          => $data
            );
        $headers = array
            (
                'Authorization: key=AAAAS5fmpX4:APA91bGWPsdApIRw8Ku6lwA33UhiqEDRXjbJKiWuU5XOSFE0l5wNxR0htMjDlvooNu3S2IKauapB2Co4UrG70oRkj8BtdCBJAA6mwA0EwO219QZ5N8559YcXd5CmTfv5ackrsT5uWcBk' ,
                'Content-Type: application/json'
            );
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        // curl_close( $ch );
        return json_encode($result, true);
    }

    public function updateRefreshToken(Request $request){
        $this->validate($request, [
            'token' => 'required',
        ]);

        $token = $request->input('token');
        $TOPIC_ADMIN = $request->input('topic');
        Notification::subscribeToTopic($TOPIC_ADMIN,$token);
        return response()->json(['success' => 1, 'message' => 'action successfully'], 200);
    }
}

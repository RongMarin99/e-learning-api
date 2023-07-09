<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $username = "Rong Marin";
    public $email = "rongmarin98@gmail.com";

    public function responseWithData($data = [])
    {
        return response()->json([
            'data' => $data,
            'success' => 1,
            'message' => 'Your action has been completed successfully.'
        ], 200);
    }

    public function uploadImages(Request $request){
        $data = $request['images'];
        $images = [];
        foreach($data as $item){
           $images[] = $this->uploadImage($item,'images/gallery');
        }
        return $images;
    }

    public function uploadImage($photo, $path = 'images',$size1=350,$size2=350)
    {
        $photoName = null;
        if (!empty($photo)) {

            $path = public_path( $path);
            if (!is_dir($path)) {
                Storage::makeDirectory($path, 0777, true, true);
            }

            $photoName = uniqid('', true) . '.webp';
            $location = $path . DIRECTORY_SEPARATOR . $photoName;
            try {
                $manager = new ImageManager();
                $manager->make($photo)->save($location);
            } catch (Exception $ex) {
                DB::rollBack();
                response()->json(['success' => 0, 'message' => 'Error while processing image.'], 500);
            }
        }
        return $photoName;
    }
    
    public function getBase64($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public function basic_email() 
    {
        $data = array('name'=>"Rong Marin");
     
        Mail::send(['text'=>'emails.verification'], $data, function($message) {
           $message->to('rongmarin98@gmail.com', 'Tutorials Point')
                    ->subject('Laravel Basic Testing Mail');
           $message->from('rongmarin98@gmail.com','Rong Marin');
        });
    }


    public function verify_email($user) 
    {
        $sender = array(
            'email' => $this->email,
            'username' => $this->username
        );
        $data = array( 
            'username' => $user['username'],
            'email' => $user['email'],
            'user_id' => $user['user_id']
        );
        Mail::send('emails.verification', $data, function($message) use ($user,$sender)  {
            $message->to($user['email'], $user['username'])
                    ->subject('Welcome to RDev! Confirm Your E-mail.');
            $message->from($sender['email'],$sender['username']);
        });
        return 'Please Check Your Email';
    }

    public function reset_password($user,$token) 
    {
        $sender = array(
            'email' => $this->email,
            'username' => $this->username
        );
        $data = array( 
            'username' => $user['username'],
            'email' => $user['email'],
            'user_id' => $user['id'],
            'token' => $token
        );
        Mail::send('emails.reset-password', $data, function($message) use ($user,$sender)  {
            $message->to($user['email'], $user['username'])
                    ->subject('Welcome to RDev! Confirm Your E-mail.');
            $message->from($sender['email'],$sender['username']);
        });

        return 'Please Check Your Email';
    }

    public function attachment_email() {
    $data = array('name'=>"Virat Gandhi");
    Mail::send('mail', $data, function($message) {
        $message->to('abc@gmail.com', 'Tutorials Point')->subject
            ('Laravel Testing Mail with Attachment');
        $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
        $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
        $message->from('xyz@gmail.com','Virat Gandhi');
    });
    echo "Email Sent with attachment. Check your inbox.";
    }
}

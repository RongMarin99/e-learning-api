<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function uploadImage($photo, $path = 'image_etec',$size1=350,$size2=350)
    {
        $photoName = null;
        if (!empty($photo)) {

            $path = public_path(DIRECTORY_SEPARATOR. $path);
            if (!is_dir($path)) {
                Storage::makeDirectory($path, 0777, true, true);
            }

            $photoName = uniqid('', true) . '.webp';
            $location = $path . DIRECTORY_SEPARATOR . $photoName;
            try {
                $manager = new ImageManager();
                $manager->make($photo)->resize($size1, $size2, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($location);
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
}

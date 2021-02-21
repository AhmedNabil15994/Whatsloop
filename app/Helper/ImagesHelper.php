<?php

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class ImagesHelper {

    static function getPhotoSize($url){
        if($url == ""){
            return '';
        }
        $path = str_replace(URL::to('/'), '', $url);
        $path = public_path().$path;
        if(!is_file($path)){
            return '';
        }
        $image = get_headers($url, 1);
        $bytes = $image["Content-Length"];
        $mb = $bytes/(1024 * 1024);
        return number_format($mb,2) . " MB ";
    }

    static function GetImagePath($strAction, $id, $filename,$withDefault=true) {

        if($withDefault){
            $default = asset('images/not-available.jpg');
        }else{
            $default = '';
        }

        if($filename == '') {
            return $default;
        }

        $path = URL::to('/');
        $checkFile = public_path() . '/uploads';

        switch ($strAction) {
            case "users":
                $fullPath = $path.'/uploads' . '/users/' . $id . '/' . $filename;
                $checkFile = $checkFile . '/users/' . $id . '/' . $filename;
                return is_file($checkFile) ? URL::to($fullPath) : $default;
                break;
            case "variables":
                $fullPath = $path.'/uploads' . '/variables/' . $id . '/' . $filename;
                $checkFile = $checkFile . '/variables/' . $id . '/' . $filename;
                return is_file($checkFile) ? URL::to($fullPath) : $default;
                break;
            case "photos":
                $fullPath = $path.'/uploads' . '/photos/' . $filename;
                $checkFile = $checkFile . '/photos/' . $filename;
                return is_file($checkFile) ? URL::to($fullPath) : $default;
                break;
        }

        return $default;
    }

    static function UploadFile($strAction, $fieldInput, $id, $fileType = '') {

        if ($fieldInput == '') {
            return false;
        }

        if (is_object($fieldInput)) {
            $fileObj = $fieldInput;
        } else {
            $fileObj = Storage::url($fieldInput);
        }

        if (Storage::size($fieldInput) >= 2000000) {
            return false;
        }

        $extensionExplode = explode('/' , Storage::mimeType($fieldInput)); // getting image extension
        unset($extensionExplode[0]);
        $extensionExplode = array_values($extensionExplode);
        $extension = $extensionExplode[0];
       
        if($fileType == '' || $fileType == 'photo' || $fileType == 'image'){
            $appliedExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief','jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd','svg+xml'];
        }elseif($fileType == 'file'){
            $appliedExtensions = ['vnd.openxmlformats-officedocument.spreadsheetml.sheet','xlsx','csv','plain','txt','docx','ppt','word','vnd.openxmlformats-officedocument.wordprocessingml.document','zip','rar','pdf',];
        }elseif($fileType == 'video'){
            $appliedExtensions = ['3gp','3g2','avi','uvh','uvm','uvu','uvp','uvs','uaa','fvt','f4v','flv','fli','h261','h263','h264','jpgv','m4v','asf','pyv','wm','wmx','wmv','wvx','mj2','mxu','mpeg','mp4','ogv','webm','qt','movie','viv','wav','avi','mkv'];
        }else{
            $appliedExtensions = $fileType;
        }

        if (!in_array($extension, $appliedExtensions)) {
            return false;
        }
        
        $rand = rand() . date("YmdhisA");
        $fileName = 'whatsloop' . '-' . $rand;
        $directory = '';

        $path = public_path() . '/uploads/';

        if ($strAction == 'users') {
            $directory = $path . 'users/' . $id;
        }

        if ($strAction == 'pages') {
            $directory = $path . 'pages/' . $id;
        }

        if ($strAction == 'companies') {
            $directory = $path . 'companies/' . $id;
        }

        if ($strAction == 'variables') {
            $directory = $path . 'variables/' . $id;
        }

        $fileName_full = $fileName . '.' . $extension;

        if ($directory == '') {
            return false;
        }

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $filePath = str_replace('/storage', '', Storage::url($fieldInput));
        if (File::move(storage_path().'/app'.$filePath, $directory.'/'.$fileName_full)){
            return $fileName_full;
        }

        return false;
    }

    // \ImagesHelper::deleteDirectory(public_path('/').'/uploads/users/15');
    // \ImagesHelper::deleteDirectory(public_path('/').'/uploads/users/15/filename.png');
    static function deleteDirectory($dir) {
        system('rm -r ' . escapeshellarg($dir), $retval);
        return $retval == 0; // UNIX commands return zero on success
    }

}
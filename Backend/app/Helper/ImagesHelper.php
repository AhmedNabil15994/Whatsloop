<?php

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Models\CentralUser;
use App\Models\User;

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

    static function checkFileExtension($filename){
        $filename = explode('.', $filename);
        $extension = array_reverse($filename)[0];
        return self::checkExtensionType(strtolower($extension));
    }

    static function checkExtensionType($extension,$type=null){
        $images = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief','jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd','svg+xml'];
        $files = ['vnd.openxmlformats-officedocument.spreadsheetml.sheet','bin','xlsx','csv','plain','txt','docx','ppt','word','vnd.openxmlformats-officedocument.wordprocessingml.document','zip','rar','pdf','plain'];
        $videos = ['3gp','3g2','avi','uvh','uvm','uvu','uvp','uvs','uaa','fvt','f4v','flv','fli','h261','h263','h264','jpgv','m4v','asf','pyv','wm','wmx','wmv','wvx','mj2','mxu','mpeg','mp4','ogv','webm','qt','movie','viv','avi','mkv'];
        $sounds = ['wav','mp3','m3u','aac','vorbis','flac','alac','aiff','dsd'];

        if(in_array($extension, $images)){
            if($type != null){
                return ['photo',$images];
            }
            return 'photo';
        }

        if(in_array($extension, $files)){
            if($type != null){
                return ['file',$files];
            }
            return 'file';
        }

        if(in_array($extension, $videos)){
            if($type != null){
                return ['video',$videos];
            }
            return 'video';
        }

        if(in_array($extension, $sounds)){
            if($type != null){
                return ['sound',$sounds];
            }
            return 'sound';
        }
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

        $tenant = '';
        if(!\Session::has('central')){
            if(\Session::has('user_id')){
                $tenant = TENANT_ID;
            }
        }else{
            if($default != ''){
                $default = str_replace('tenancy/assets/','',$default);
            }
        }

        if($strAction == 'users'){
            $userObj = CentralUser::getOne($id);
            if(!$userObj){
                $userObj = User::getOne($id);
            }
            $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
            if($tenantObj){
                $tenant = $tenantObj->tenant_id;
            }
        }

        $path = URL::to('/');
        $checkFile = public_path() . '/uploads' . ($tenant != '' ? '/'.$tenant : '');

        switch ($strAction) {
            case "users":
                $fullPath = $path.'/uploads'.($tenant != '' ? '/'.$tenant : '') . '/users/' . $id . '/' . $filename;
                $checkFile = $checkFile . '/users/' . $id . '/' . $filename;
                return is_file($checkFile) ? URL::to($fullPath) : $default;
                break;
            case "bots":
                $fullPath = $path.'/uploads'.($tenant != '' ? '/'.$tenant : '') . '/bots/' . $id . '/' . $filename;
                $checkFile = $checkFile . '/bots/' . $id . '/' . $filename;
                return is_file($checkFile) ? URL::to($fullPath) : $default;
                break;
            case "groupMessages":
                $fullPath = $path.'/uploads'.($tenant != '' ? '/'.$tenant : '') . '/groupMessages/' . $id . '/' . $filename;
                $checkFile = $checkFile . '/groupMessages/' . $id . '/' . $filename;
                return is_file($checkFile) ? URL::to($fullPath) : $default;
                break;
            case "chats":
                $fullPath = $path.'/uploads'.($tenant != '' ? '/'.$tenant : '') . '/chats/'. $filename;
                $checkFile = $checkFile . '/chats/' . $filename;
                return is_file($checkFile) ? URL::to($fullPath) : $default;
                break;

            case "faqs":
                $checkFile = public_path() . '/uploads';
                $fullPath = $path.'/uploads' . '/faqs/' . $id . '/' . $filename;
                $checkFile = $checkFile . '/faqs/' . $id . '/' . $filename;
                return is_file($checkFile) ? URL::to($fullPath) : $default;
                break;
            case "tickets":
                $checkFile = public_path() . '/uploads';
                $fullPath = $path.'/uploads' . '/tickets/' . $id . '/' . $filename;
                $checkFile = $checkFile . '/tickets/' . $id . '/' . $filename;
                return is_file($checkFile) ? URL::to($fullPath) : $default;
                break;
            case "bank_transfers":
                $checkFile = public_path() . '/uploads';
                $fullPath = $path.'/uploads' . '/bank_transfers/' . $id . '/' . $filename;
                $checkFile = $checkFile . '/bank_transfers/' . $id . '/' . $filename;
                return is_file($checkFile) ? URL::to($fullPath) : $default;
                break;
            case "bankAccounts":
                $checkFile = public_path() . '/uploads';
                $fullPath = $path.'/uploads' . '/bankAccounts/' . $id . '/' . $filename;
                $checkFile = $checkFile . '/bankAccounts/' . $id . '/' . $filename;
                return is_file($checkFile) ? URL::to($fullPath) : $default;
                break;
            case "changeLogs":
                $checkFile = public_path() . '/uploads';
                $fullPath = $path.'/uploads' . '/changeLogs/' . $id . '/' . $filename;
                $checkFile = $checkFile . '/changeLogs/' . $id . '/' . $filename;
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
        $oldExtension = explode('.', explode('/', $fieldInput)[1])[1];
        $extensionExplode = explode('/' , Storage::mimeType($fieldInput)); // getting image extension
        unset($extensionExplode[0]);
        $extensionExplode = array_values($extensionExplode);
        $extension = $extensionExplode[0];
        
        $fileData = self::checkExtensionType($extension,'getData');
        $fileType = $fileData[0];
        $appliedExtensions = $fileData[1];

        if (!in_array($extension, $appliedExtensions)) {
            return false;
        }

        
        $rand = rand() . date("YmdhisA");
        $fileName = 'whatsloop' . '-' . $rand;
        $directory = '';

        $tenant = '';
        if(!\Session::has('central')){
            $tenant = TENANT_ID;
        }

        if($strAction == 'users'){
            $userObj = CentralUser::getOne($id);
            $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
            if($userObj->group_id == 0){
                $tenant = $tenantObj->tenant_id;
            }
        }

        $path = public_path() . '/uploads/'.$tenant.'/';

        if ($strAction == 'users') {
            $directory = $path . 'users/' . $id;
        }

        if ($strAction == 'bots') {
            $directory = $path . 'bots/' . $id;
        }

        if ($strAction == 'chats') {
            $directory = $path . 'chats/';
        }

        if ($strAction == 'groupMessages') {
            $directory = $path . 'groupMessages/' . $id;
        }



        if ($strAction == 'faqs') {
            $directory = $path . 'faqs/' . $id;
        }

        if ($strAction == 'tickets') {
            $path = public_path() . '/uploads/';
            $directory = $path . 'tickets/' . $id;
        }

        if ($strAction == 'bank_transfers') {
            $path = public_path() . '/uploads/';
            $directory = $path . 'bank_transfers/' . $id;
        }

        if ($strAction == 'bankAccounts') {
            $path = public_path() . '/uploads/';
            $directory = $path . 'bankAccounts/' . $id;
        }

        if ($strAction == 'changeLogs') {
            $directory = $path . 'changeLogs/' . $id;
        }

        if ($strAction == 'central_users') {
            $directory = $path . 'central_users/' . $id;
        }

        $fileName_full = $fileName . '.' . $oldExtension;

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

    static function UploadFiles($strAction, $fieldInput, $id, $fileType = '') {

        if ($fieldInput == '') {
            return false;
        }

        if (is_object($fieldInput)) {
            $fileObj = $fieldInput;
        } else {
            $fileObj = Storage::url($fieldInput);
        }

        if (Storage::size($fieldInput) >= 10000000) {
            return false;
        }
        $oldExtension = explode('.', explode('/', $fieldInput)[1])[1];
        // dd($oldExtension);
        $extensionExplode = explode('/' , Storage::mimeType($fieldInput)); // getting image extension
        unset($extensionExplode[0]);
        $extensionExplode = array_values($extensionExplode);
        $extension = $extensionExplode[0];
        $appliedExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief','jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd','svg+xml','3gp','3g2','avi','uvh','uvm','uvu','uvp','uvs','uaa','fvt','f4v','flv','fli','h261','h263','h264','jpgv','m4v','asf','pyv','wm','wmx','wmv','wvx','mj2','mxu','mpeg','mp4','ogv','webm','qt','movie','viv','wav','avi','mkv','x-m4v','svg'];

        if (!in_array($extension, $appliedExtensions)) {
            return false;
        }


        $rand = rand() . date("YmdhisA");
        $fileName = 'whatsloop' . '-' . $rand;
        $directory = '';

        $tenant = '';
        if(!\Session::has('central')){
            $tenant = TENANT_ID;
        }

        if($strAction == 'users'){
            $userObj = CentralUser::getOne($id);
            $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
            if($userObj->group_id == 0){
                $tenant = $tenantObj->tenant_id;
            }
        }

        $path = public_path() . '/uploads/'.$tenant.'/';

        if ($strAction == 'users') {
            $directory = $path . 'users/' . $id;
        }

        if ($strAction == 'bots') {
            $directory = $path . 'bots/' . $id;
        }

        if ($strAction == 'chats') {
            $directory = $path . 'chats/';
        }

        if ($strAction == 'groupMessages') {
            $directory = $path . 'groupMessages/' . $id;
        }



        if ($strAction == 'faqs') {
            $directory = $path . 'faqs/' . $id;
        }

        if ($strAction == 'tickets') {
            $path = public_path() . '/uploads/';
            $directory = $path . 'tickets/' . $id;
        }

        if ($strAction == 'bank_transfers') {
            $path = public_path() . '/uploads/';
            $directory = $path . 'bank_transfers/' . $id;
        }

        if ($strAction == 'bankAccounts') {
            $path = public_path() . '/uploads/';
            $directory = $path . 'bankAccounts/' . $id;
        }

        if ($strAction == 'changeLogs') {
            $directory = $path . 'changeLogs/' . $id;
        }

        if ($strAction == 'central_users') {
            $directory = $path . 'central_users/' . $id;
        }

        $fileName_full = $fileName . '.' . $oldExtension;

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

    static function uploadFileFromRequest($strAction, $fieldInput,$id='', $fileType = '') {

        if ($fieldInput == '') {
            return false;
        }

        if (is_object($fieldInput)) {
            $fileObj = $fieldInput;
        } else {
            if (!Request::hasFile($fieldInput)) {
                return false;
            }

            $fileObj = Request::file($fieldInput);
        }

        if ($fileObj->getSize() >= 10000000) {
            return false;
        }

        $extensionExplode = explode('/' , $fileObj->getMimeType()); // getting image extension
        unset($extensionExplode[0]);
        $extensionExplode = array_values($extensionExplode);
        $extension = $extensionExplode[0];
        $fileData = self::checkExtensionType($extension,'getData');
        $fileType = $fileData[0];
        $appliedExtensions = $fileData[1];

        if (!in_array($extension, $appliedExtensions)) {
            return false;
        }
        
        $rand = rand() . date("YmdhisA");
        $fileName = 'whatsloop' . '-' . $rand;
        $directory = '';

        $path = public_path() . '/uploads/';
        if(!\Session::has('central')){
            $tenant = TENANT_ID;
            $path = public_path() . '/uploads/'.$tenant.'/';
        }

        if ($strAction == 'chats') {
            $directory = $path . 'chats/';
        }

        if ($strAction == 'bank_transfers') {
            $path = public_path() . '/uploads/';
            $directory = $path . 'bank_transfers/' . $id;
        }

        if ($strAction == 'tickets') {
            $path = public_path() . '/uploads/';
            $directory = $path . 'tickets/' . $id;
        }

        if ($strAction == 'bankAccounts') {
            $path = public_path() . '/uploads/';
            $directory = $path . 'bankAccounts/' . $id;
        }

        $fileName_full = $fileName . '.' . $extension;

        if ($directory == '') {
            return false;
        }

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        if ($fileObj->move($directory, $fileName_full)){
            return $fileName_full;
        }

        return false;
    }

    // \ImagesHelper::deleteDirectory(public_path('/').'/uploads/users/15');
    // \ImagesHelper::deleteDirectory(public_path('/').'/uploads/users/15/filename.png');
    static function deleteDirectory($dir) {
        system('rm -r ' . escapeshellarg($dir), $retval);
        // \File::deleteDirectory($dir);
        return $retval == 0; // UNIX commands return zero on success
    }

}
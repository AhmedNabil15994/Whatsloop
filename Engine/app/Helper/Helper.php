<?php

class Helper
{

    static function formatDate($date, $format = "Y-m-d H:i:s", $custom = false){
        if($date == null || $date == "0000-00-00 00:00:00" || $date == "0000-00-00" || $date == ""){
            return '';
        }

        $date = str_replace("," , '' , $date);

        $FinalDate = date($format, strtotime($date));

        if ($format == '24') {
            $FinalDate = date('Y-m-d', strtotime($date)) . ' 24:00:00';
        }

        if ($custom != false) {
            $FinalDate = date($format, strtotime($custom, strtotime($date)));
        }

        return $FinalDate != '1970-01-01 12:00:00' ? $FinalDate : null;
    }

    static function formatDateForDisplay($date, $withTime = false){
        if($date == null || $date == "0000-00-00 00:00:00" || $date == "0000-00-00" || $date == ""){
            return '';
        }

        return $withTime != false ? date("d F, Y H:i:s", strtotime($date)) : date("d F, Y", strtotime($date));
    }

    static function formatDateCustom($date, $format = "Y-m-d H:i:s", $custom = false){
        if($date == null || $date == "0000-00-00 00:00:00" || $date == "0000-00-00" || $date == ""){
            return '';
        }

        $date = str_replace("," , '' , $date);

        $FinalDate = date($format, strtotime($date));

        if ($format == '24') {
            $FinalDate = date('Y-m-d', strtotime($date)) . ' 24:00:00';
        }

        if ($custom != false) {
            $FinalDate = date($format, strtotime($custom, strtotime($date)));
        }

        return $FinalDate != '1970-01-01 12:00:00' ? $FinalDate : null;
    }

    static function fixPaginate($url, $key) {
        if(strpos($key , $url) == false){
            $url = preg_replace('/(.*)(?)' . $key . '=[^&]+?(?)[0-9]{0,4}(.*)|[^&]+&(&)(.*)/i', '$1$2$3$4$5$6$7$8$9$10$11$12$13$14$15$16$17$18$19$20', $url . '&');
            $url = substr($url, 0, -1);
            return $url ;
        }else{
            return $url;
        }
    }

    static function getCountryID(){
        $country_id = 1;
        $ip_address = \Input::get('ip_address');

        if($ip_address != null){
            $position = \Location::get(\Input::get('ip_address'));

            if($position != false){
                $countryObj = App\Models\Country::getOneByCode($position->countryCode);
                if($countryObj != null){
                    $country_id = $countryObj->id;
                }
            }
        }
        return $country_id;
    }

    Static function GeneratePagination($source){
        $uri = \Input::getUri();
        $count = count($source);
        $total = $source->total();
        $lastPage = $source->lastPage();
        $currentPage = $source->currentPage();

        $data = new \stdClass();
        $data->count = $count;
        $data->total_count = $total;
        $data->current_page = $currentPage;
        $data->last_page = $lastPage;
        $next = $currentPage + 1;
        $prev = $currentPage - 1;

        $newUrl = self::fixPaginate($uri, "page");

        if(preg_match('/(&)/' , $newUrl) != 0 || strpos($newUrl , '?') != false ){
            $separator = '&';
        }else{
            $separator = '?';
        }

        if($currentPage !=  $lastPage ){
            $link = str_replace('&&' , '&' , $newUrl . $separator. "page=". $next);
            $link = str_replace('?&' , '?' , $link);
            $data->next = $link;
            if($currentPage == 1){
                $data->prev = "";
            }else{
                $link = str_replace('&&' , '&' , $newUrl . $separator. "page=". $prev);
                $link = str_replace('?&' , '?' , $link);
                $data->prev = $link ;
            }
        }else{
            $data->next = "";
            if($currentPage == 1){
                $data->prev = "";
            }else{
                $link = str_replace('&&' , '&' , $newUrl . $separator. "page=". $prev);
                $link = str_replace('?&' , '?' , $link);
                $data->prev = $link ;
            }
        }

        return $data;
    }

    static function currencyFormat($price, $str = false) {
        if ($price == 9999999999) {
            $price = "POA";
        } else {
            $price = number_format($price, 2, ".", ",");
            $price = str_replace(',', ',', $price);
            $price = str_replace('.', '.', $price);
            if ($str && $price == 0) {
                $price = $str;
            }
        }

        return $price;
    }

    static function ExportToText($content){

        $date = date('dmYHis');
        $filename = 'BuildingToRephrase-'.$date.'.txt';

        set_time_limit(0);
        ini_set('memory_limit','2500M');

        header('Content-Description: File Transfer');
        header('Content-Type: text/plain');
        header('Content-disposition: attachment; filename='.$filename);
        header('Content-Length: '.strlen($content));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Pragma: public');
        echo $content;
        exit;

    }

    static function generateSlug($string) {
        $string = str_replace(" ", "-", $string);
        $special = array('!', '’', '–', '@', '#', '$', '%', '^', '&', '*', '(', ')', '_', '+', '{', '}', '|', '[', ']', ':', ';', '<', '>', '?', ',', '.', '/', '`', '~', '/', '!', '&', '*');
        $string = str_replace(' ', '-', str_replace($special, '', $string));
        $string = str_replace("_", "-", $string);
        $string = str_replace("'", "", $string);
        $string = str_replace('"', '', $string);
        $string = str_replace("--", "-", $string);
        $string = strip_tags($string);
        $string = strtolower($string);
        return $string;
    }

    static function SendMail($emailData){

        \Mail::send($emailData['template'], $emailData, function ($message) use ($emailData) {

            $fromEmailAddress = 'engine@aliensera.com';
            $fromDisplayName = 'Aliensera';

            if(isset($emailData['fromEmailAddress'])){
                $fromEmailAddress = $emailData['fromEmailAddress'];
            }

            if(isset($emailData['fromDisplayName'])) {
                $fromDisplayName = $emailData['fromDisplayName'];
            }

            $message->from($fromEmailAddress, $fromDisplayName);

            $message->to($emailData['to'])->subject($emailData['subject']);

        });
    }

}

<?php
namespace App\Traits;
use DB;
use Auth;
use App\Models\User;
use App\Models\Notification;

trait FireBaseNotification {

    /**
     * Localize a date to users timezone
     *
     * @param null $dateField
     * @return Carbon
     */
    public function sendNotification($deviceToken,$title,$body,$url)
    {
        $payload = array(
            "to"=>$deviceToken,
            "data"=>array("body" => $body, "title" => $title,"click_action"=>$url),
        );
          
        $headers = array(
          'Authorization:key='.env('FireBase'),
          'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $payload ) );
        $result = curl_exec($ch );
        curl_close( $ch );
    }
}

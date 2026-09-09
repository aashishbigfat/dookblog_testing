<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageServiceProvider;
use DB;
use Storage;
use Image;
use Auth;
use App\Models\User; 
use App\Models\Notification;

class NotificationController extends Controller
{
    //Token Add
    public function updateFCMToken(Request $request){
        try{
            $user_id = auth()->user()->id;
            $fcmToken = User::find($user_id);
            $fcmToken->fcm_token = $request->token;
            $fcmToken->save();
            $this->getRecentNotification();
            return response()->json([
                'success'=>true
            ]);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'success'=>false
            ],500);
        }
    }

    function getRecentNotification(){     
       
        $allNotifications = Notification::where(['status_notify'=>0])->get();
        foreach($allNotifications as $notifData){
            if($notifData->assigned_to == auth()->user()->id){
                $deviceToken = User::where('id',$notifData->assigned_to)->whereNotNull('fcm_token')->value('fcm_token');
            }else{
                $deviceToken = User::where('id',$notifData->user_id)
                ->whereNotNull('fcm_token')->value('fcm_token');
            }
            
            $this->sendNotification($deviceToken,$notifData);
            $statusUpdate = Notification::find($notifData->id);
            $statusUpdate->status_notify = 1;
            $statusUpdate->save();
            sleep(7);
        }

    }

    public function sendNotification($deviceToken,$data){
        $payload = array(
            "to"=>$deviceToken,
            "data"=>array("body" => $data->body, "title" => $data->title,"click_action"=>$data->url),
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

    public function changeStatusNotification(Request $request, $id){
        
        $viewStatusChange = Notification::find($id);
        $viewStatusChange->status_view = 1;
        $viewStatusChange->save();
        $status = [
            'url'=> url('/notifications'),
        ];
        return response()->json($status); 
    }
    public function getNotification(Request $request){
        $permission = User::getPermissions();
        $user_id = auth()->user()->id;
        $notifications = Notification::where('user_id', $user_id)
            ->orWhere('assigned_to', $user_id)
            ->orderBy('created_at','DESC')
            ->paginate(25);
        foreach ($notifications as $key => $value) {
            $value->writer = User::where('id', $value->assigned_to)->value('name');
        }
        $admin_id = Notification::where('user_id',auth()->user()->id)->value('user_id');
        $total = DB::table('notifications')->where('user_id',$user_id)
            ->where('assigned_to', $user_id)->count();
        
        return view('notification.notification',compact('notifications','total','permission','admin_id'));
    }

    public function storeNotification(Request $request){
        $notification = new Notification;
        $notification->title = $request->notification_title;
        $notification->body = $request->notification_message;
        $notification->user_id = $request->user_id;
        $notification->assigned_to = $request->assign_to;
        $notification->dest_topic_id = $request->dest_topic_id;
        $notification->type = "Topic";
        $notification->url = url('/topics');
        $notification->save();

        $last_title = $notification->title;
        $last_body = $notification->body;
        $last_url = $notification->url;
        $last_id = $notification->id;

        //++++++++Notification send Code++++++++++++//
        if($notification->assigned_to == auth()->user()->id){
            $firebaseToken = User::where('id',$notification->user_id)
            ->whereNotNull('fcm_token')->value('fcm_token');
        }else{
            $firebaseToken = User::where('id',$notification->assigned_to)
            ->whereNotNull('fcm_token')->value('fcm_token');
        }

        $payload = array(
            "to"=>$firebaseToken,
            "data"=>array("body" => $last_body, "title" => $last_title,"click_action"=>$last_url),
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

        if($notification->assigned_to == auth()->user()->id){
            $statusUpdate = Notification::find($last_id);
            $statusUpdate->status_notify = 1;
            $statusUpdate->save();
        }else{
            $statusUpdate = Notification::find($last_id);
            $statusUpdate->status_notify = 1;
            $statusUpdate->save();
        }
        
        return redirect()->back();
    }
}

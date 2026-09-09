<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use DB;
use Auth;
use App\Models\User;
use App\Models\Destination;
use App\Models\Role;
use App\Models\Suggetion;
use App\Models\Notification;
use App\Traits\FireBaseNotification;

class DestinationController extends Controller
{
    use FireBaseNotification;
    public function getDestintionFromDook(){
        $post = array(
            'dest' => "abcd"
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://adm.dookinternational.com/api/blog_destinations');
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($curl);
        $vv = json_decode($response);
        $destinations = $vv->destinations;
        foreach ($destinations as $key => $value) {
            $uniqueDest = Destination::where('reference_id', $value->id)->first();
            if($uniqueDest){

            }else{
                $destination = new Destination;
                $destination->reference_id = $value->id;
                $destination->dest_name = $value->dest_name;
                $destination->country_name = $value->country_name;
                $destination->save();
            }
        }
        return response()->json(['msg'=>'success']);
    }

    public function destintions(Request $request)
    {
        $status = $request->status_filter;
        $keyword = $request->keyword;
        $permission = User::getPermissions();
        if (Gate::allows('destination_list',$permission)) 
        {
            $user = User::where('id',auth()->user()->id)->value('user_type');
            if($user == 1){
                $destinations = Destination::where('user_id', auth()->user()->id)
                    ->orWhere('assigned_to',null)
                    ->where(function ($query) use($request){
                        if($request->keyword != ""){
                            $query->where('dest_name', 'LIKE','%'.$request->keyword.'%')
                                ->orWhere('country_name', 'LIKE','%'.$request->keyword.'%');
                        }
                        if($request->status_filter == "in"){
                            $query->where('status', 0);
                        }
                        if($request->status_filter == 1){
                            $query->where('status', 1);
                        }
                        if($request->status_filter == 2){
                            $query->where('status', 2);
                        }
                    })
                    ->paginate(25);
            }else{
                $destinations = Destination::where('assigned_to',auth()->user()->id)
                    ->where(function ($query) use($request){
                        if($request->keyword != ""){
                            $query->where('dest_name', 'LIKE','%'.$request->keyword.'%')
                                ->orWhere('country_name', 'LIKE','%'.$request->keyword.'%');
                        }
                        if($request->status_filter == "in"){
                            $query->where('status', 0);
                        }
                        if($request->status_filter == 1){
                            $query->where('status', 1);
                        }
                        if($request->status_filter == 2){
                            $query->where('status', 2);
                        }
                    })
                ->paginate(25);
            }
            $totalDest = Destination::count();
            $admin_id = Destination::where('user_id',auth()->user()->id)->value('user_id');
            $users = User::where('role_id','!=',0)->get();
            $status = ($status == null)?'no':$status;
            return view('destinations.index',compact('destinations','permission','users','admin_id','user','keyword','status','totalDest','status'));
        }else{
            return abort(401);
        }
    }

    public function destintionAssign(Request $request){
        $destination = Destination::find($request->id);
        $destination->assigned_to = $request->writer_id;
        $destination->other = $request->writer_id;
        $destination->user_id = auth()->user()->id;
        $destination->save();

        $notification = new Notification;
        $notification->title = "Destination Assigned!";
        $notification->body = 'Destination Assigned Successfully!';
        $notification->user_id = auth()->user()->id;
        $notification->assigned_to = $destination->assigned_to;
        $notification->type = "Destination";
        $notification->url = url('/destinations');
        $notification->save();

        $last_title = $notification->title;
        $last_body = $notification->body;
        $last_url = $notification->url;

        //++++++++Notification send Code++++++++++++//

        $firebaseToken = User::where('id',$notification->assigned_to)
            ->whereNotNull('fcm_token')->value('fcm_token');

        $sendNotification = $this->sendNotification($firebaseToken, $last_title, $last_body, $last_url);

        return response()->json($destination->assigned_to);
    }

    public function destinationEdit(Request $request, $id){
        $permission = User::getPermissions();
        if (Gate::allows('destination_edit',$permission)) 
        {
            $destination = Destination::where('id',$id)->first();
            $wc1 = strlen(strip_tags($destination->description));
            $wc2 = strlen(strip_tags($destination->title));
            $wc3 = strlen(strip_tags($destination->sub_title));
            $wc4 = strlen(strip_tags($destination->header_title));
            $wc5 = strlen(strip_tags($destination->header_sub_title));
            $wc6 = strlen(strip_tags($destination->tour_sub_title));
            $wc7 = strlen(strip_tags($destination->experience_sub_title));
            $wc8 = strlen(strip_tags($destination->attraction_sub_title));
            $wc9 = strlen(strip_tags($destination->event_sub_title));
            $wc10 = strlen(strip_tags($destination->restaurant_sub_title));
            $wc11= strlen(strip_tags($destination->trip_sub_title));
            $wc12= strlen(strip_tags($destination->trip_description));
            
            $user = User::where('id',$destination->assigned_to)->first();
            return view('destinations.edit',compact('destination','permission','user','wc1','wc2','wc3','wc4','wc5','wc6','wc7','wc8','wc9','wc10','wc11','wc12'));
        }else{
            return abort(401);
        }
    }

    public function destinationUpdate(Request $request, $id)
    {
        $data = $request->all();
        $edit_trip_description = preg_replace('/<!--\[if gte mso 9\]>.*<!\[endif\]-->/s', '', $request->edit_trip_description);
        $edit_trip_description = str_replace("\r\n",'', $edit_trip_description);
        $edit_trip_description = str_replace('<!--StartFragment--><span lang="VI">','', $edit_trip_description);
        $edit_trip_description = str_replace('<o:p></o:p>','', $edit_trip_description);

        $destination = Destination::find($id);
        $destination->title = $request->edit_title;
        $destination->sub_title = $request->edit_sub_title;
        $destination->header_title = $request->edit_header_title;
        $destination->header_sub_title = $request->edit_header_sub_title;
        $destination->description = $request->edit_description;
        $destination->tour_sub_title = $request->edit_tour_sub_title;
        $destination->experience_sub_title = $request->edit_experience_sub_title;
        $destination->attraction_sub_title = $request->edit_attraction_sub_title;
        $destination->event_sub_title = $request->edit_event_sub_title;
        $destination->restaurant_sub_title = $request->edit_restaurant_sub_title;
        $destination->trip_sub_title = $request->edit_trip_sub_title;
        $destination->trip_description = $edit_trip_description;
        $destination->created_date = date("Y-m-d");
      
        $destination->save();
        $last_id = $destination->id;

        
        $status = [
            'url'=> url('/destinations'),
        ];
        return response()->json($status);
    }

    public function destinationStatusChanged(Request $request)
    {
        $data = $request->all();
        $destination = Destination::find($request->edit_id);
        $destination->status = $request->dest_status;
        $destination->save();

        return response()->json('success');
    }

    public function destinationView(Request $request, $id){
        $permission = User::getPermissions();
        $destination = Destination::where('id',$id)->first();
        $wc1 = str_word_count(strip_tags($destination->description));
        $wc2 = str_word_count(strip_tags($destination->title));
        $wc3 = str_word_count(strip_tags($destination->sub_title));
        $wc4 = str_word_count(strip_tags($destination->header_title));
        $wc5 = str_word_count(strip_tags($destination->header_sub_title));
        $wc6 = str_word_count(strip_tags($destination->tour_sub_title));
        $wc7 = str_word_count(strip_tags($destination->experience_sub_title));
        $wc8 = str_word_count(strip_tags($destination->attraction_sub_title));
        $wc9 = str_word_count(strip_tags($destination->event_sub_title));
        $wc10 = str_word_count(strip_tags($destination->restaurant_sub_title));
        $wc11= str_word_count(strip_tags($destination->trip_sub_title));
        $wc12= str_word_count(strip_tags($destination->trip_description));

        $destination->word_count = $wc1+$wc2+$wc3+$wc4+$wc5+$wc6+$wc7+$wc8+$wc9+$wc10+$wc11+$wc12;
        $adminUser = User::where('id',auth()->user()->id)->value('user_type');
        $user = User::where('id',$destination->assigned_to)->first();
        return view('destinations.view',compact('destination','permission','user','wc1','wc2','wc3','wc4','wc5','wc6','wc7','wc8','wc9','wc10','wc11','wc12','adminUser'));
        
    }

    public function destinationSuggetions(Request $request)
    {
        $permission = User::getPermissions();
        $suggetions = Suggetion::where('user_id',auth()->user()->id)
                ->where('type','Destination')
                ->orWhere('suggetion_to',auth()->user()->id)
                ->orderBy('created_date','DESC')
                ->paginate(25);
        foreach ($suggetions as $key => $value) {
            $value->suggetionTo = User::where('id',$value->suggetion_to)
                ->value('name');
        }
        $admin_id = Suggetion::where('user_id',auth()->user()->id)->value('user_id');
        return view('suggetion.index',compact('suggetions','admin_id','permission'));
    }
    public function destinationSuggetionStore(Request $request)
    {
        $data = $request->all();
        $suggetion = new Suggetion;
        $suggetion->heading = $request->suggetion_title;
        $suggetion->description = $request->suggetions;
        $suggetion->type = $request->type;
        $suggetion->user_id = auth()->user()->id;
        $suggetion->suggetion_to = $request->suggetion_to;
        $suggetion->destination_topic_id = $request->dest_topic_id;
        $suggetion->created_date = date("Y-m-d");
        $suggetion->save();
        $last_id = $suggetion->id;

        
        $status = [
            'url'=> url('/destiation-suggetions'),
        ];
        return response()->json($status);
    }
}
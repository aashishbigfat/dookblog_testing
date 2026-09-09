<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use DB;
use Storage;
use Image;
use Auth;
use App\Models\User;
use App\Models\Topic;
use App\Models\Role;
use App\Models\Suggetion;
use App\Models\Notification;
use App\Traits\FireBaseNotification;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\CategoryPost;
use App\Models\PostTag;
use File;

class TopicController extends Controller
{
    use FireBaseNotification;

    public function topics(Request $request)
    {
        $admin_id = "";
        $permission = User::getPermissions();
        $topics = Topic::where('user_id',auth()->user()->id)->orWhere('assigned_to',auth()->user()->id)->orderBy('created_date','DESC')->paginate(25);
        foreach ($topics as $key => $value) {
            $assigned_to = User::where('id',$value->assigned_to)->value('name');
            $value->assignedTo = $assigned_to;
        }
        $admin_id = Topic::where('user_id',auth()->user()->id)->value('user_id');
        return view('topics.index',compact('topics','permission','admin_id'));
    }

    public function topicAssign(Request $request)
    {
        $permission = User::getPermissions();
        if (Gate::allows('post_view',$permission)) 
        {
            $users = User::where('role_id',1)->get();
            return view('topics.create',compact('users','permission','users'));
        }else{
            return abort(401);
        }
    }

    public function topicStore(Request $request)
    {
        $topics = new Topic;
        $topics->title = $request->title;
        $topics->description = $request->description;
        $topics->user_id = auth()->user()->id;
        $topics->assigned_to = $request->writer;
        $topics->created_date = date("Y-m-d");
        $topics->save();


        $notification = new Notification;
        $notification->title = "Topic Created";
        $notification->body = 'Following Topic request has been created';
        $notification->user_id = auth()->user()->id;
        $notification->assigned_to = $topics->assigned_to;
        $notification->type = "Topic";
        $notification->url = url('/topics');
        $notification->save();

        $last_title = $notification->title;
        $last_body = $notification->body;
        $last_url = $notification->url;

        //++++++++Notification send Code++++++++++++//

        $firebaseToken = User::where('id',$notification->assigned_to)
            ->whereNotNull('fcm_token')->value('fcm_token');

        $sendNotification = $this->sendNotification($firebaseToken, $last_title, $last_body, $last_url);

        $status = [
            'url'=> url('/topics'),
        ];
        return response()->json($status);
    }
    public function topicView(Request $request, $id)
    {
        $permission = User::getPermissions();
        $rid = $request->route('id'); 
        $routeId = (int)$rid;
        $user_id = Topic::where('user_id', '=', Auth::User()->id)
            ->orWhere('assigned_to', '=', Auth::User()->id)
            ->pluck('id')
            ->toArray();
        if(in_array($routeId, $user_id)){
            $topic = Topic::where('id',$id)
                ->where(function ($query) use($request){
                    $query->where('user_id',auth()->user()->id)
                          ->orWhere('assigned_to',auth()->user()->id);
            })
                ->first();
            $topic->assignedTo = User::where('id',$topic->assigned_to)->value('name');
            return view('topics.view',compact('topic','permission'));
        }else{
            return abort(401);
        }
    }

    public function topicSuggetions(Request $request)
    {
        $permission = User::getPermissions();
        $suggetions = Suggetion::where('user_id',auth()->user()->id)
                ->where('type','Topic')
                ->orWhere('suggetion_to',auth()->user()->id)
                ->orderBy('created_date','DESC')
                ->paginate(25);
        foreach ($suggetions as $key => $value) {
            $value->suggetionTo = User::where('id',$value->suggetion_to)
                ->value('name');
        }
        $admin_id = Suggetion::where('user_id',auth()->user()->id)->value('user_id');
        return view('suggetion.topic_index',compact('suggetions','admin_id','permission'));
    }

    public function getBlogWP(Request $request)
    {
        $post_url = "https://blog.dookinternational.com/wp-json/custom/v1/all-post-save";
        $cURLs = curl_init();
        curl_setopt($cURLs, CURLOPT_URL, $post_url);
        curl_setopt($cURLs, CURLOPT_HTTPGET, true);
        curl_setopt($cURLs, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLs, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));

        $posts = curl_exec($cURLs);
        $posts = json_decode($posts);
        $recentPost = $posts->posts;


        $admin_id = User::where('tenant_id',auth()->user()->tenant_id)
                ->where('role_id',0)->value('id');
        foreach ($recentPost as $key => $value) {
            $unique = Post::where('reference_id',$value->id)->first();
            if($unique){

            }else
            {
                $post = new Post;
                $post->title = $value->title;
                $post->reference_id = $value->id;
                $post->slug = $value->post_url;
                $post->short_description = $value->short_description;
                $post->description = $value->description;
                $post->published_date = $value->post_date;
                $post->modified_date = $value->post_date;
                $post->auther = "Dook";
                
                $post->created_by = auth()->user()->id;
                $post->user_id = $admin_id;
                $post->tenant_id = auth()->user()->tenant_id;

                if(isset($value->metas->_amt_title[0])){
                    $meta_title = $value->metas->_amt_title[0];
                }
                if(isset($value->metas->_amt_keywords[0])){
                    $meta_keywords = $value->metas->_amt_keywords[0];
                }
                if(isset($value->metas->_amt_description[0])){
                    $meta_description = $value->metas->_amt_description[0];
                }

                $post->meta_title = $meta_title;
                $post->meta_keywords = $meta_keywords;
                $post->meta_description = $meta_description;

                $image = basename($value->image_url);
                $post->image = $image;
                $post->save();
                $last_id = $post->id;

                // Categories 
                if($value->related_categories){
                    foreach ($value->related_categories as $cat) {
                        $category = Category::where('name',$cat->name)->first();
                        
                        if($category){
                            if($category->name == $cat->name){
                                $categories = Category::find($category->id);
                                $categories->name = $cat->name;
                                $categories->slug = $cat->slug;
                            }else{
                            $categories = new Category;
                            $categories->name = $cat->name;
                            $categories->slug = $cat->slug;
                            }
                        }else{
                            $categories = new Category;
                            $categories->name = $cat->name;
                            $categories->slug = $cat->slug;
                        }
                        $categories->save();

                        $last_catid = $categories->id;
                        $catTag  = new CategoryPost;
                        $catTag->post_id=$last_id;
                        $catTag->category_id=$last_catid;
                        $catTag->save();
                    }
                }
                // Tags 
                if($value->related_tags){
                    foreach ($value->related_tags as $tag) {
                        $tags = Tag::where('name',$tag->name)->first();
                        
                        if($tags){
                            if($tags->name == $tag->name){
                                $tags = Tag::find($tags->id);
                                $tags->name = $tag->name;
                                $tags->slug = $tag->slug;
                            }else{
                            $tags = new Tag;
                            $tags->name = $tag->name;
                            $tags->slug = $tag->slug;
                            }
                        }else{
                            $tags = new Tag;
                            $tags->name = $tag->name;
                            $tags->slug = $tag->slug;
                        }
                        $tags->save();

                        $last_tagid = $tags->id;
                        $postTag  = new PostTag;
                        $postTag->post_id=$last_id;
                        $postTag->tag_id=$last_tagid;
                        $postTag->save();
                    }
                }
            }  
        }
        return response()->json('Successfully.');
    }

    //Move Copy Image from 1 folder to another
    public function moveimageOneTwoAnother(Request $request){
        // $posts = Post::select('image')->get();
        // //$i=0;
        // foreach($posts as $post){
        //     $relPath = 'imageAll/'.$post->image;
        //     $relPath1 = 'imageNew/'.$post->image;
        //     if (file_exists(public_path($relPath))) {
        //         File::copy(public_path($relPath), public_path($relPath1));
        //         //Storage::move($post->image, 'imageNew/'.basename($post->image));
        //         //$i++;
        //     }
        // }
        dd('true');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\ImageServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use DB;
use Storage;
use Image;
use Auth;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\CategoryPost;
use App\Models\PostTag;
use App\Models\User;
use App\Models\Topic;

class PostController extends Controller
{
    public function postImageCrop(Request $request){
        $data = $request->image;
        $image_array_1 = explode(";", $data);
        $image_array_2 = explode(",", $image_array_1[1]);
        $data = base64_decode($image_array_2[1]);

        $imageName = time() . '.png';
        $relPath = 'images/cropedimages/';
            if (!file_exists(public_path($relPath))) {
                mkdir(public_path($relPath), 777, true);
            }
        Image::make($data)->save( public_path($relPath . $imageName ) );
        //file_put_contents($imageName, $data);
        $urls = public_path("images/cropedimages/".$imageName);
        $url = url("images/cropedimages/".$imageName);
        //echo '<img src="'.$url.'" class="img-thumbnail" />';
        return response()->json(['img' => $url, 'url' => $urls]);
    }

    public function summernoteImage(Request $request){
        $image = $request->file('file');
        $extension = $image->getClientOriginalExtension();
        $imageName =  Str::random(5).time() .'.'. $extension;
        $relPath = 'images/post-media/';
            if (!file_exists(public_path($relPath))) {
                mkdir(public_path($relPath), 777, true);
            }
        $img = Image::make($image)->save( public_path($relPath . $imageName) );
        $url = url('images/post-media').'/'.$imageName;
        //dd($url);
        return response()->json($url);
    }
    public function searchCountry(Request $request){
        $startDest = [];
        if($request->has('q')){
         $search = $request->q;
            $startDest = DB::table('all_countries')->select("id","country_name")
                        ->where('country_name','LIKE',"$search%")
                        ->get(15);

        }else{
            $startDest = DB::table('all_countries')->select("id","country_name")
                        ->limit(15)
                        ->get();
        }
        return response()->json($startDest);
    }

    public function createPost(Request $request)
    {
        $topicId = $request->topicId; // for blade auther readonly keyword
        // for by default title pic from topic
        if($request->topicId){
            $topic = DB::table('topics')->where('id',$request->topicId)
                ->select('id','title')->first();
        }else{
            $topic = "";
        }
        $auther = User::where('tenant_id',auth()->user()->tenant_id)
                ->where('role_id',0)->value('name');
        $permission = User::getPermissions();
        if (Gate::allows('post_create',$permission)) 
        {
            return view('posts.create',compact('permission','topic','auther','topicId'));
        }else{
            return abort(401);
        }
    }

    public function createIndex(Request $request)
    {

        $cat = $request->cat;
        $date = $request->date;
        $keyword = $request->keyword;
        $permission = User::getPermissions();
        if (Gate::allows('post_view',$permission)) 
        {
            if($request->post_status == "" && $request->keyword != ""){
                $posts = Post::join('category_posts','category_posts.post_id','=','posts.id')
                    ->join('categories','categories.id','=','category_posts.category_id')  
                    ->where('posts.tenant_id',auth()->user()->tenant_id)
                    //->where('status',1)
                    ->where(function ($query) use($request){
                        if($request->keyword != ""){
                            $query->where('posts.title', 'LIKE','%'.$request->keyword.'%');
                                //->orWhere('posts.auther', 'LIKE','%'.$request->keyword.'%')
                                //->orWhere('post.created_by', 'LIKE','%'.$request->keyword.'%');
                        }
                        if($request->cat != ""){
                            $query->where('category_posts.category_id', $request->cat);
                        }
                        if($request->date != ""){
                            $query->where('posts.published_date', 'LIKE','%'.$request->date.'%');
                        }
                        if($request->post_status != ""){
                            if($request->post_status == "publish"){
                                $query->where('posts.status', 1);
                            }
                            if($request->post_status == "draft"){
                                $query->where('posts.status', 0);
                            }
                        }
                    })
                ->select('posts.id','posts.title','posts.slug','posts.image','posts.published_date','posts.modified_date','posts.auther','posts.created_by','posts.user_id','posts.tenant_id','posts.status','posts.created_at','posts.description','posts.short_description')
                ->distinct()
                ->orderBy('posts.published_date','DESC')
                ->paginate(30);
                 //dd(count($posts));
            }elseif($request->post_status == ""){
                $posts = Post::where('tenant_id',auth()->user()->tenant_id)
                        ->orderBy('published_date','DESC')
                        ->paginate(30);
            }
            else{
                if($request->post_status == "publish"){
                    $posts = Post::where('tenant_id',auth()->user()->tenant_id)
                    ->where('status',1)
                    ->orderBy('published_date','DESC')
                    ->paginate(30);
                }elseif($request->post_status == "draft") {
                    $posts = Post::where('tenant_id',auth()->user()->tenant_id)
                        ->where('status',0)
                        ->orderBy('published_date','DESC')
                        ->paginate(30);
                }else{
                    $posts = Post::where('tenant_id',auth()->user()->tenant_id)
                        ->orderBy('published_date','DESC')
                        ->paginate(30);
                }
            }

            foreach ($posts as $key => $value) {

                $category_ids = CategoryPost::where('post_id',$value->id)
                    ->pluck('category_id')
                    ->toArray();
                $categories = Category::whereIn('id',$category_ids)->select('name')->get();
                $value->categories = $categories;
                $removeHtmlDes = str_word_count(strip_tags($value->description));
                $value->word_count = $removeHtmlDes;
                $value->created_by = User::where('id',$value->created_by)
                        ->value('name');
            }
            // Out of filters

            $all_post = Post::where('tenant_id',auth()->user()->tenant_id)
                ->count();
            $publish_post = Post::where('tenant_id',auth()->user()->tenant_id)
                ->where('status',1)
                ->count();
            $draft_post = $all_post-$publish_post;
            if(isset($request->post_status) == ""){
                $post_status = "All";
            }else{
                $post_status = $request->post_status;
            }
            $allDate = DB::table('posts')
                ->where('tenant_id',auth()->user()->tenant_id)
                ->select(DB::raw('YEAR(published_date) year, MONTH(published_date) months, MONTHNAME(published_date) month'))
                ->distinct()
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
            foreach ($allDate as $key => $value) {
                $value->yearManth = $value->year.'-'.$value->month;
                $value->yearManths = $value->year.'-'.$value->months;
            }

            $categories = Category::get();
            return view('posts.index',compact('posts','all_post','publish_post','draft_post','post_status','cat','date','allDate','categories','keyword','permission'));
        }else{
            return abort(401);
        }
    }
    public function storePost(Request $request)
    {
        $descriptions = preg_replace('/<!--\[if gte mso 9\]>.*<!\[endif\]-->/s', '', $request->description);
        $descriptions = str_replace("\r\n",'', $descriptions);
        $descriptions = str_replace('<!--StartFragment--><span lang="VI">','', $descriptions);
        $descriptions = str_replace('<o:p></o:p>','', $descriptions);

        $admin_id = User::where('tenant_id',auth()->user()->tenant_id)
                ->where('role_id',0)->value('id');
        
        $post = new Post;
        $post->title = $request->title;
        $post->slug = $request->slug;
        $post->short_description = $request->short_description;
        $post->description = $descriptions;
        $post->published_date = $request->published_date;
        $post->modified_date = $request->modified_date;
        $post->auther = $request->auther;
        $post->meta_title = $request->meta_title;
        $post->meta_keywords = $request->meta_keywords;
        $post->meta_description = $request->meta_description;
        $post->created_by = auth()->user()->id;
        $post->user_id = $admin_id;
        $post->tenant_id = auth()->user()->tenant_id;
        $post->topic_id = $request->topic_id;

        // Handle FAQs
        $faqs = [];
        if ($request->faq_questions && $request->faq_answers) {
            $questions = $request->faq_questions;
            $answers = $request->faq_answers;
            
            for ($i = 0; $i < min(count($questions), count($answers), 5); $i++) {
                if (!empty(trim($questions[$i])) && !empty(trim($answers[$i]))) {
                    $faqs[] = [
                        'question' => trim($questions[$i]),
                        'answer' => trim($answers[$i])
                    ];
                }
            }
        }
        $post->faqs = !empty($faqs) ? json_encode($faqs) : null;

        if($request->post_image){
            $url = $request->post_image;
            $basename = basename($request->post_image);
            $basename_a = $basename;
            $type = pathinfo($url, PATHINFO_EXTENSION);
            $data = file_get_contents($url);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$base64));
            $imageName = Str::random(7).time() . '.jpg';
            $relPath = 'images/posts/';
            if (!file_exists(public_path($relPath))) {
                mkdir(public_path($relPath), 777, true);
            }
            Image::make($image)->save( public_path($relPath . $imageName ) ); 
            $post->image = $imageName;
        }
        $post->save();
        $last_id = $post->id;
        
        if($request->post_image){
            $image_croppath = public_path('/images/cropedimages/'.$basename_a);
            if(file_exists($image_croppath)){
                unlink($image_croppath);
            } 
        }

        // Categories 
        if($request->categories){
            foreach ($request->categories as $key => $value) {
                $category = Category::where('name',$value)->first();
                $data = strtolower($value);
                $arr = explode(' ',$data);
                $str_slug = implode('-', $arr);
                if($category){
                    if($category->name == $value){
                        $categories = Category::find($category->id);
                        $categories->name = $value;
                        $categories->slug = $str_slug;
                    }else{
                    $categories = new Category;
                    $categories->name = $value;
                    $categories->slug = $str_slug;
                    }
                }else{
                    $categories = new Category;
                    $categories->name = $value;
                    $categories->slug = $str_slug;
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
        if($request->tags){
            foreach ($request->tags as $key => $value) {
                $tag = Tag::where('name',$value)->first();
                $data = strtolower($value);
                $arr = explode(' ',$data);
                $str_slug = implode('-', $arr);
                if($tag){
                    if($tag->name == $value){
                        $tags = Tag::find($tag->id);
                        $tags->name = $value;
                        $tags->slug = $str_slug;
                    }else{
                    $tags = new Tag;
                    $tags->name = $value;
                    $tags->slug = $str_slug;
                    }
                }else{
                    $tags = new Tag;
                    $tags->name = $value;
                    $tags->slug = $str_slug;
                }
                $tags->save();
                $last_tagid = $tags->id;
                $postTag  = new PostTag;
                $postTag->post_id=$last_id;
                $postTag->tag_id=$last_tagid;
                $postTag->save();
            }
        }

        $status = [
            'url'=> url('/posts'),
        ];
        return response()->json($status);
    }

   public function editPost(Request $request, $id)
{
    $rid = $request->route('id'); 
    $routeId = (int)$rid;
    $permission = User::getPermissions();
    $tenantId = Post::where('tenant_id',auth()->user()->tenant_id)
            ->pluck('id')->toArray();
    if(in_array($routeId, $tenantId)){
        $post = Post::where('id',$id)->first();
        $category_ids = CategoryPost::where('post_id',$post->id)
                ->pluck('category_id')
                ->toArray();
        $categories = Category::whereIn('id',$category_ids)->get();

        $tag_ids = PostTag::where('post_id',$post->id)
                ->pluck('tag_id')
                ->toArray();
        $tags = Tag::whereIn('id',$tag_ids)->get();

        return view('posts.edit',compact('post','tags','categories','permission'));
    }else{
        return abort(401);
    }
}

public function updatePost(Request $request, $id)
{
    $descriptions = preg_replace('/<!--\[if gte mso 9\]>.*<!\[endif\]-->/s', '', $request->description);
    $descriptions = str_replace("\r\n",'', $descriptions);
    $descriptions = str_replace('<!--StartFragment--><span lang="VI">','', $descriptions);
    $descriptions = str_replace('<o:p></o:p>','', $descriptions);
    
    $post = Post::find($id);
    $post->title = $request->title;
    $post->slug = $request->slug;
    $post->short_description = $request->short_description;
    $post->description = $descriptions;
    $post->published_date = $request->published_date;
    $post->modified_date = $request->modified_date;
    $post->auther = $request->auther;
    $post->meta_title = $request->meta_title;
    $post->meta_keywords = $request->meta_keywords;
    $post->meta_description = $request->meta_description;
    $post->created_by = auth()->user()->id;
    $post->user_id = auth()->user()->id;
    $post->tenant_id = auth()->user()->tenant_id;

    // Handle FAQs Update
    $faqs = [];
    if ($request->faq_questions && $request->faq_answers) {
        $questions = $request->faq_questions;
        $answers = $request->faq_answers;
        
        for ($i = 0; $i < min(count($questions), count($answers), 5); $i++) {
            if (!empty(trim($questions[$i])) && !empty(trim($answers[$i]))) {
                $faqs[] = [
                    'question' => trim($questions[$i]),
                    'answer' => trim($answers[$i])
                ];
            }
        }
    }
    $post->faqs = !empty($faqs) ? json_encode($faqs) : null;

    if($request->post_image){
        $url = $request->post_image;
        $basename = basename($request->post_image);
        $basename_a = $basename;
        $type = pathinfo($url, PATHINFO_EXTENSION);
        $data = file_get_contents($url);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$base64));
        $imageName = Str::random(7).time() . '.jpg';
        $relPath = 'images/posts/';
        if (!file_exists(public_path($relPath))) {
            mkdir(public_path($relPath), 777, true);
        }
        Image::make($image)->save( public_path($relPath . $imageName ) ); 
        $post->image = $imageName;
    }
    $post->save();
    $last_id = $post->id;
    if($request->post_image){
        $image_croppath = public_path('/images/cropedimages/'.$basename_a);
        if(file_exists($image_croppath)){
            unlink($image_croppath);
        } 
    }

    // Categories 
    if($request->categories){
        CategoryPost::where('post_id',$id)->delete();
        foreach ($request->categories as $key => $value) {
            $data = strtolower($value);
            $arr = explode(' ',$data);
            $str_slug = implode('-', $arr);
            $category = Category::where('name',$value)->first();
            
            if($category){
                if($category->name == $value){
                    $categories = Category::find($category->id);
                    $categories->name = $value;
                    $categories->slug = $str_slug;
                }else{
                $categories = new Category;
                $categories->name = $value;
                $categories->slug = $str_slug;
                }
            }else{
                $categories = new Category;
                $categories->name = $value;
                $categories->slug = $str_slug;
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
    if($request->tags){
        PostTag::where('post_id',$id)->delete();
        foreach ($request->tags as $key => $value) {
            $data = strtolower($value);
            $arr = explode(' ',$data);
            $str_slug = implode('-', $arr);
            $tag = Tag::where('name',$value)->first();
            
            if($tag){
                if($tag->name == $value){
                    $tags = Tag::find($tag->id);
                    $tags->name = $value;
                    $tags->slug = $str_slug;
                }else{
                $tags = new Tag;
                $tags->name = $value;
                $tags->slug = $str_slug;
                }
            }else{
                $tags = new Tag;
                $tags->name = $value;
                $tags->slug = $str_slug;
            }
            $tags->save();
            $last_tagid = $tags->id;
            $postTag  = new PostTag;
            $postTag->post_id=$last_id;
            $postTag->tag_id=$last_tagid;
            $postTag->save();
        }
    }
    $status = [
        'url'=> url('/posts'),
    ];
    return response()->json($status);
}

    public function myPost(Request $request)
    {
        $cat = $request->cat;
        $date = $request->date;
        $keyword = $request->keyword;
        $permission = User::getPermissions();
        if($request->post_status == ""){
            $posts = Post::where('created_by',auth()->user()->id)
                //->where('status',1)
                ->where(function ($query) use($request){
                    if($request->keyword != ""){
                        $query->where('posts.title', 'LIKE','%'.$request->keyword.'%');
                    }
                    // if($request->cat != ""){
                    //     $query->where('category_posts.category_id', 'LIKE','%'.$request->cat.'%');
                    // }
                    if($request->date != ""){
                        $query->where('posts.published_date', 'LIKE','%'.$request->date.'%');
                    }
                    // if($request->post_status != ""){
                    //     if($request->post_status == "publish"){
                    //         $query->where('posts.status', 1);
                    //     }
                    //     if($request->post_status == "draft"){
                    //         $query->where('posts.status', 0);
                    //     }
                    // }
                })
            ->select('id','title','slug','image','published_date','modified_date','auther','created_by','user_id','tenant_id','status','created_at','description','short_description')
            ->distinct()
            ->orderBy('posts.published_date','DESC')
            ->paginate(25);
        }else{
            if($request->post_status == "publish"){
                $posts = Post::where('created_by',auth()->user()->id)
                ->where('status',1)
                ->orderBy('published_date','DESC')
                ->paginate(25);
            }elseif($request->post_status == "draft") {
                $posts = Post::where('created_by',auth()->user()->id)
                    ->where('status',0)
                    ->orderBy('published_date','DESC')
                    ->paginate(25);
            }else{
                $posts = Post::where('created_by',auth()->user()->id)
                    ->orderBy('published_date','DESC')
                    ->paginate(25);
            }
        }

        foreach ($posts as $key => $value) {
            $removeHtmlDes = str_word_count(strip_tags($value->description));
            $value->word_count = $removeHtmlDes;
            $value->created_by = User::where('id',$value->created_by)
                    ->value('name');
        }
        // foreach ($posts as $key => $value) {
        //     $category_ids = CategoryPost::where('post_id',$value->id)
        //         ->pluck('category_id')
        //         ->toArray();
        //     $categories = Category::whereIn('id',$category_ids)->select('name')->get();
        //     $value->categories = $categories;
        // }
        
        // Out of filters

        $all_post = Post::where('created_by',auth()->user()->id)
            ->count();
        $publish_post = Post::where('created_by',auth()->user()->id)
            ->where('status',1)
            ->count();
        $draft_post = $all_post-$publish_post;
        if(isset($request->post_status) == ""){
            $post_status = "All";
        }else{
            $post_status = $request->post_status;
        }
        $allDate = DB::table('posts')
            ->where('created_by',auth()->user()->id)
            ->select(DB::raw('YEAR(published_date) year, MONTH(published_date) months, MONTHNAME(published_date) month'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        foreach ($allDate as $key => $value) {
            $value->yearManth = $value->year.'-'.$value->month;
            $value->yearManths = $value->year.'-'.$value->months;
        }
        //$categories = Category::get();
        return view('posts.my_post',compact('posts','all_post','publish_post','draft_post','post_status','cat','date','allDate','keyword','permission'));
    }
    public function postStatusChange(Request $request, $id){
        $postStatus = Post::where('id', $id)->select('topic_id','status')
            ->first();
        $post = Post::find($id);
        if($postStatus->status == 1){
            $post->status = 0;
        }else{
            $post->status = 1;
        }
        $post->save();

        if($postStatus->topic_id != ""){
            $topic = Topic::find($postStatus->topic_id);
            $topic->status = 1;
            $topic->save();
        }
        return redirect()->back();
    }
    // Category Ajax
    public function categoryListAjax(Request $request)
    {
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            $data = Category::select("id","name")
                        ->where('name','LIKE',"%$search%")
                        ->get();
            }
        else{
            $data = Category::select("id","name")
                        ->get();
        }
        return response()->json($data);
    }
    // Tags
    public function tagListAjax(Request $request)
    {
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            $data = Tag::select("id","name")
                        ->where('name','LIKE',"%$search%")
                        ->get(15);
            }
        else{
            $data = Tag::select("id","name")
                        ->limit(15)
                        ->get();
        }
        return response()->json($data);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\CategoryPost;
use App\Models\PostTag;

class ApiBlogController extends Controller
{
    public function allPosts(Request $request)
    {
        $item_per_page = 21;
        $item_first_page = 21;
        if (is_null($request->page) || $request->page == 1) {
            $current_page = 1;
            $item_per_page = 21;
            $offset = ($current_page-1)* $item_per_page;
        }elseif ($request->page == 2){ 
            $current_page = $request->page;
            $offset = ($current_page-1)* 21;
            $item_per_page = 21;
        }else {
            $item_per_page = 21;
            $current_page = $request->page;
            $offset = ($current_page-1)*$item_per_page+($item_first_page-$item_per_page);
        }

        $post = Post::where('status',1)
            ->select('id','title','slug','image','auther','status','short_description','meta_title','meta_keywords','meta_description','published_date')
            ->distinct()
            ->orderBy('published_date','DESC')
            ->get(); 
        $posts = $post->forPage($current_page,$item_per_page);
        $postCount = count($posts);
        if(count($posts)>0){
            foreach ($posts as $key => $value) {
                if($value->image != "" || $value->image != null){
                    $value->image = url('').'/images/posts/'.$value->image;
                }
                $value->published_date = date('M d, Y', strtotime($value->published_date));

                $category_ids = CategoryPost::where('post_id',$value->id)
                    ->pluck('category_id')
                    ->toArray();
                if(count($category_ids)>0){
                    $categories = Category::whereIn('id',$category_ids)->select('name as cat_name','slug')->get();
                    $value->related_categories = $categories;
                }else{
                    $value->related_categories = [];
                }
            }
        }

        $postsT = Post::select('id')->where('status',1)->distinct()->get(); 
        $total = count($postsT);
        $recentPost = Post::where('status',1)
            ->select('id','title','slug','image','auther','status','short_description','meta_title','meta_keywords','meta_description','published_date')
            ->distinct()
            ->orderBy('published_date','DESC')
            ->limit(29)
            ->get(); 
        if(count($recentPost)>0){
            foreach ($recentPost as $key => $values) {
                if($values->image != "" || $values->image != null){
                    $values->image = url('').'/images/posts/'.$values->image;
                }
                $values->published_date = date('M d, Y', strtotime($values->published_date));
            }
        }
        $categories = Category::select('id','name','slug','meta_title','meta_keywords','meta_description')->get();
        $status = array(
            'error' => false,
            'total' => $total,
            'posts' => $posts,
            'postCount' => $postCount,
            'recentPost' => $recentPost,
            'categories' => $categories
        );
        return response()->json($status, 200);
    }

    public function postDetails(Request $request)
    {
        $slug = $request->slug;
        $post = Post::where('status',1)
            ->where('slug',$slug)
            ->select('id','title','slug','image','auther','status','short_description','meta_title','meta_keywords','meta_description','published_date','description','modified_date','faqs')
            ->first(); 
        if($post){
            if($post->image != "" || $post->image != null){
                $post->image = url('').'/images/posts/'.$post->image;
            }
            $post->published_date = date('M d, Y', strtotime($post->published_date));
             $post->modified_date = date('M d, Y', strtotime($post->modified_date));
            
            // Add loading="lazy" to img tags in the description
            if (strpos($post->description, '<img') !== false) {
                $post->description = preg_replace('/<img([^>]+)>/', '<img$1 loading="lazy">', $post->description);
            }

            // Process FAQs
            if ($post->faqs) {
                if (is_string($post->faqs)) {
                    $decodedFaqs = json_decode($post->faqs, true);
                    $post->faqs = $decodedFaqs && is_array($decodedFaqs) ? $decodedFaqs : [];
                } elseif (!is_array($post->faqs)) {
                    $post->faqs = [];
                }
            } else {
                $post->faqs = [];
            }

            $category_ids = CategoryPost::where('post_id',$post->id)
                ->pluck('category_id')
                ->toArray();
            if(count($category_ids)>0){
                $categories = Category::whereIn('id',$category_ids)->select('name as cat_name','slug')->get();
                $post->related_categories = $categories;
            }else{
                $post->related_categories = [];
            }
            $tag_ids = PostTag::where('post_id',$post->id)
                ->pluck('tag_id')
                ->toArray();
            if(count($tag_ids)>0){
                $tags = Tag::whereIn('id',$tag_ids)->select('name','slug')->get();
                $post->related_tags = $tags;
            }else{
                $post->related_tags = [];
            }
        }else{
            $post->related_categories = [];
            $post->related_tags = [];
        }
        $recentPost = Post::where('status',1)
            ->select('id','title','slug','image','auther','status','short_description','meta_title','meta_keywords','meta_description','published_date','modified_date')
            ->distinct()
            ->orderBy('published_date','DESC')
            ->limit(29)
            ->get(); 
        if(count($recentPost)>0){
            foreach ($recentPost as $key => $values) {
                if($values->image != "" || $values->image != null){
                    $values->image = url('').'/images/posts/'.$values->image;
                }
                $values->published_date = date('M d, Y', strtotime($values->published_date));
                $values->modified_date = date('M d, Y', strtotime($values->modified_date));
            }
        }
        $categories = Category::select('id','name','slug','meta_title','meta_keywords','meta_description')->get();
        
        $status = array(
            'error' => false,
            'blog_details' => $post,
            'recentPost' => $recentPost,
            'categories' => $categories,
            'hh'=>$tag_ids
        );
        return response()->json($status, 200);
    }

    public function categoryWisePost(Request $request)
    {
        $categoryId = Category::where('slug',$request->slug)->select('id')->first();
        $postId = CategoryPost::where('category_id',$categoryId->id)->pluck('post_id');
        $posts = Post::whereIn('id',$postId)
            ->where('status',1)
            ->select('id','title','slug','image','auther','status','short_description','meta_title','meta_keywords','meta_description','published_date')
            ->distinct()
            ->orderBy('published_date','DESC')
            ->get(); 
        if(count($posts)>0){
            foreach ($posts as $key => $value) {
                if($value->image != "" || $value->image != null){
                    $value->image = url('').'/images/posts/'.$value->image;
                }
                $value->published_date = date('M d, Y', strtotime($value->published_date));

                $category_ids = CategoryPost::where('post_id',$value->id)
                    ->pluck('category_id')
                    ->toArray();
                if(count($category_ids)>0){
                    $categories = Category::whereIn('id',$category_ids)->select('name as cat_name','slug')->get();
                    $value->related_categories = $categories;
                }else{
                    $value->related_categories = [];
                }
            }
        }

        $recentPost = Post::where('status',1)
            ->select('id','title','slug','image','auther','status','short_description','meta_title','meta_keywords','meta_description','published_date')
            ->distinct()
            ->orderBy('published_date','DESC')
            ->limit(18)
            ->get(); 
        if(count($recentPost)>0){
            foreach ($recentPost as $key => $values) {
                if($values->image != "" || $values->image != null){
                    $values->image = url('').'/images/posts/'.$values->image;
                }
                $values->published_date = date('M d, Y', strtotime($values->published_date));
            }
        }
        $categories = Category::select('id','name','heading','slug','meta_title','meta_keywords','meta_description')->get();
        $status = array(
            'error' => false,
            'total' => 0,
            'postCount' => 0,
            'posts' => $posts,
            'recentPost' => $recentPost,
            'categories' => $categories
        );
        return response()->json($status, 200);
    }

    public function postSearchResult(Request $request)
    {
        $keyword = $request->keyword;

        $posts = Post::join('category_posts','category_posts.post_id','=','posts.id')
            ->join('categories','categories.id','=','category_posts.category_id')
            ->where(function ($query) use($request){
                if($request->keyword != ""){
                    $query->where('posts.title', 'LIKE','%'.$request->keyword.'%')
                        ->orWhere('categories.name', 'LIKE','%'.$request->keyword.'%');
                }
            })
            ->select('posts.id','posts.title','posts.slug','posts.image','posts.published_date','posts.auther','posts.status','posts.short_description')
            ->distinct()
            ->orderBy('posts.published_date','DESC')
            ->get();
        if(count($posts)>0){
            foreach ($posts as $key => $value) {
                if($value->image != "" || $value->image != null){
                    $value->image = url('').'/images/posts/'.$value->image;
                }
                $value->published_date = date('M d, Y', strtotime($value->published_date));

                $category_ids = CategoryPost::where('post_id',$value->id)
                    ->pluck('category_id')
                    ->toArray();
                if(count($category_ids)>0){
                    $categories = Category::whereIn('id',$category_ids)->select('name as cat_name','slug')->get();
                    $value->related_categories = $categories;
                }else{
                    $value->related_categories = [];
                }
            }
        }else{
            $post->related_categories = [];
        }
        
        $recentPost = Post::where('status',1)
            ->select('id','title','slug','image','auther','status','short_description','meta_title','meta_keywords','meta_description','published_date')
            ->distinct()
            ->orderBy('published_date','DESC')
            ->limit(18)
            ->get(); 
        if(count($recentPost)>0){
            foreach ($recentPost as $key => $values) {
                if($values->image != "" || $values->image != null){
                    $values->image = url('').'/images/posts/'.$values->image;
                }
                $values->published_date = date('M d, Y', strtotime($values->published_date));
            }
        }
        $categories = Category::select('id','name','heading','slug','meta_title','meta_keywords','meta_description')->get();
        $status = array(
            'error' => false,
            'total' => 0,
            'postCount' => 0,
            'posts' => $posts,
            'recentPost' => $recentPost,
            'categories' => $categories
        );
        return response()->json($status, 200);
    }

    public function recentFourPost(Request $request)
    {

        $posts = Post::where('status',1)
            ->select('id','title','slug','image','status','short_description','published_date')
            ->distinct()
            ->orderBy('published_date','DESC')
            ->limit(4)
            ->get(); 
        if($posts){
            foreach ($posts as $key => $post) {
                if($post->image != "" || $post->image != null){
                    $post->image = url('').'/images/posts/'.$post->image;
                }
                $post->published_date = date('M d, Y', strtotime($post->published_date));
            }
        }
        $status = array(
            'error' => false,
            'posts' => $posts
        );
        return response()->json($status, 200);
    }
}

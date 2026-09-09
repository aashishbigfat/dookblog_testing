<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Auth;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Models\PostTag;
use App\Models\CategoryPost;

class CategoryTagController extends Controller
{
    public function categoriesIndex(Request $request)
    {
        $permission = User::getPermissions();
        $categories = Category::paginate(25);
        foreach ($categories as $key => $value) {
            $post_count = DB::table('category_posts')->where('category_id',$value->id)
                ->count();
            $value->count = $post_count;
        }
        return view('categories.index',compact('categories','permission'));
    }

    public function categoryStore(Request $request)
    {
        $store = new Category;
        $store->heading = $request->heading;
        $store->name = $request->name;
        $store->slug = $request->slug;
        $store->meta_title = $request->meta_title;
        $store->meta_keywords = $request->meta_keywords;
        $store->meta_description = $request->meta_description;
        $store->save();
    }

    public function categoryUpdate(Request $request)
    {
        $id = $request->edit_id;
        $update = Category::find($id);
        $update->heading = $request->edit_heading;
        $update->name = $request->edit_name;
        $update->slug = $request->edit_slug;
        $update->meta_title = $request->edit_meta_title;
        $update->meta_keywords = $request->edit_meta_keywords;
        $update->meta_description = $request->edit_meta_description;
        $update->save();
    }

    public function categoryDelete(Request $request, $id){
        Category::where('id', $id)->delete();
        $category = CategoryPost::where('category_id', $id)->pluck('id')->toArray();
        CategoryPost::where('id', $category)->delete();
        return redirect()->back();
    }

    // Tag

    public function tagsIndex(Request $request)
    {
        $permission = User::getPermissions();
        $tags = Tag::paginate(25);
        foreach ($tags as $key => $value) {
            $post_count = DB::table('post_tags')->where('tag_id',$value->id)
                ->count();
            $value->count = $post_count;
        }
        return view('tags.index',compact('tags','permission'));
    }

    public function tagStore(Request $request)
    {
        $store = new Tag;
        $store->name = $request->name;
        $store->slug = $request->slug;
        $store->save();
    }

    public function tagUpdate(Request $request)
    {
        $id = $request->edit_id;
        $update = Tag::find($id);
        $update->name = $request->edit_name;
        $update->slug = $request->edit_slug;
        $update->save();
    }

     public function tagDelete(Request $request, $id){
        Tag::where('id', $id)->delete();
        $tag = PostTag::where('tag_id', $id)->pluck('id')->toArray();
        PostTag::where('id', $tag)->delete();
        return redirect()->back();
    }
}

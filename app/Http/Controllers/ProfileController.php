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
use App\Models\User;

class ProfileController extends Controller
{
    public function companyProfile(Request $request){
        $permission = User::getPermissions();
        $user = User::where('id',auth()->user()->id)
            ->where('role_id',0)
            ->where('user_type',1)
            ->first();
        return view('profile.profile',compact('permission','user'));
    }

    public function companyProfileStore(Request $request){
        //dd($request->all());
        $profile = User::find($request->user_id);
        $profile->product = $request->product;
        $profile->name = $request->product;
        $profile->email = $request->email;
        $profile->mobile = $request->mobile;
        $profile->company_name = $request->company_name;
        $profile->country = $request->country;
        $profile->address = $request->address;
        $profile->about_company = $request->about_company;
        if($request->company_logo){
            $image = $request->file('company_logo');
            $extension = $image->getClientOriginalExtension();
            $imageName = Str::random(4).time() .'.'. $extension;
            $relPath = 'images/profile/';
            if (!file_exists(public_path($relPath))) {
                mkdir(public_path($relPath), 777, true);
            }
            Image::make($image)->save( public_path($relPath . $imageName ) ); 
            $profile->logo = $imageName;
        }
        $profile->save();

        return redirect()->back();
    }
}

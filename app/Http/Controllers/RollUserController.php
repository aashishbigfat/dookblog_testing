<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\User;
use Auth;
use DB;
use App\Models\PermissionRole;

class RollUserController extends Controller
{
    public function roles()
    {
        $permission = User::getPermissions();
        if (Gate::allows('role_view',$permission)) 
        {
            $role = Role::where('user_id',Auth::user()->id)->orderBy('id','desc')->get();
            $permissions = DB::table('permissions')
                ->select('module')
                ->distinct()
                ->get();
                foreach($permissions as $row){
                    $permission_allow = DB::table('permissions')
                        ->where('module',$row->module)
                        ->get();        
                        
                    $row->sub_module=$permission_allow;
                }
            return view('roleuser.create_role',compact('role','permission_allow','permission','permissions'));
        }
        else{
            return abort(401);
        }
    }
    public function roleStore(Request $request){
        $save = new Role;
        $save->name = $request->role;
        $save->tenant_id = Auth::user()->tenant_id;
        $save->user_id =Auth::user()->id;
        $save->status =0;
        if($save->save()){
            return redirect()->back()->with('msg','Role Created');
        }
    }
    public function roleUpdate(Request $request){
       $edit = Role::find($request->id);
       $edit->name = $request->role;
       if($edit->save()){
        return redirect()->back()->with('msg','Role updated');
       }
    }

    public function users(Request $request)
    {
        //dd(Auth::user()->tenant_id);
        //$keyword = $request->keyword;
        $permission = User::getPermissions();
       // dd(Gate::allows('user_view',$permission));
        if (Gate::allows('user_view',$permission)) {
            $role = Role::where('user_id',Auth::user()->id)->get();
            $users = User::where('tenant_id',Auth::user()->tenant_id)
                ->where('role_id','!=',"")
            ->orderBy('created_at','DESC')
            ->paginate(25);

            foreach($users as $row){
                $user_role = Role::where('id',$row->role_id)
                    ->get();
                $row->sub_name=$user_role;
            }
            //dd($users);
            return view('roleuser.user_create',compact('role','permission','users'));
        }
        else{
            return abort(401);
        }
    }

    public function userStore(Request $request){
         
        $validate = User::where('email',$request->email)->first();
        if(isset($validate->email) == $request->email){
            return redirect()->back()->with('msg','Email id already exist plz use another E-mail!');
        }else{
        $password = Str::random(8);
        $email = $request->email;
        $name= $request->name;
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->phone;
        $user->country = $request->country;
        $user->tenant_id = Auth::user()->tenant_id;
        $user->role_id = $request->role;
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->company_name = Auth::user()->company_name;
        //$user->logo = Auth::user()->logo;
        $user->address = Auth::user()->address;
        $user->password_text = $password;
        $user->password = Hash::make($password);
        $user->remember_token = bin2hex(random_bytes(32));;
        
        // Mail::send('mail.tenant_user_create', ['email' => $email, 'password'=>$password, 'name'=>$name], function ($m) use ($user) {
        //     $m->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        //     $m->to($user->email);
        //     $m->subject('Departure Cloud - User Created Successfully');
        // });
        if($user->save()){
           
            return redirect()->back()->with('msg','User Created Successfully');
        }
      }
    }
    public function UserUpdate(Request $request){
        $update = User::find($request->id);
        $update->name = $request->name;
        $update->mobile = $request->phone;
        $update->role_id = $request->role;
        $update->country = $request->country;
        $update->password = Hash::make($request->new_password);
        $update->password_text = $request->new_password;
        if($update->save()){
            //$user->sendEmailVerificationNotification();
             return redirect()->back()->with('msg','User Updated Successfully');
         }
    }

    public function userDisableEnable(Request $request, $id)
    {
        $user  = User::find($id);
        if($user->verified == 0){
            $user->verified = 1;
            $user->save();
        }
        else{
            $user->verified = 0;
            $user->save();
        }
        return response()->json(['success'=>'User status changed successfully!']);
    }

    public function userDelete(Request $request, $id)
    {
        $user  = User::where('id', $id)->delete();
        return response()->json(['success'=>'User status changed successfully!']);
    }

    public function allowPermission(Request $request){
        $permission = $request->permission_id;
        $role_id = $request->role_id;
        $delete = DB::table('permission_roles')->where('role_id',$role_id)->delete();
        if($permission > 0){
            for ($i=0; $i<sizeof($permission); $i++)
            {
                //dd($i);
               $data = array();
               $data['permission_id']= $permission[$i];
               $data['role_id']   = $role_id;
               $query_insert = DB::table('permission_roles')->insert($data);
            }
        
        if($query_insert){
            return redirect()->back()->with('msg', 'Permission provided successfully');
        }
     }else{
        return redirect()->back()->with('msg', 'Permission removed successfully');
     }
    }
}

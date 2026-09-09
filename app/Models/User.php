<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name','email','password','password_text','mobile','company_id','company_name','tenant_id','address','country','main_user_type','remember_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getPermissions()
    {
      $role_id = Auth::user()->role_id;
      $permissions = array();
      if($role_id != '0'){
          $permission_id = PermissionRole::where('role_id', $role_id)->pluck('permission_id');
          foreach($permission_id as $key => $id){
                $name = Permission::where('id', $id)->value('name');
                array_push($permissions,$name);
          }
      }
      else{
          $per_names= Permission::pluck('name');
          foreach($per_names as $key => $value){
              array_push($permissions,$value);
          }
      }
      return $permissions;
     }
}

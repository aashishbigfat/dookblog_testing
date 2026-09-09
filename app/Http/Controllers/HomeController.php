<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Auth;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $permission = User::getPermissions();
        $total_post = DB::table('posts')->where('tenant_id',auth()->user()->tenant_id)->count();
        $total_active_post = DB::table('posts')->where('status',1)->where('tenant_id',auth()->user()->tenant_id)->count();
        $total_inactive_post = $total_post-$total_active_post;
        $this_month_post = DB::table('posts')->whereMonth('created_at', Carbon::now()->month)
            ->where('tenant_id',auth()->user()->tenant_id)
            ->count();

        $total_topic = DB::table('topics')->where('user_id',auth()->user()->id)->count();
        $total_destination = DB::table('destinations')->count();
        $destAsigned = DB::table('destinations')->where('user_id',auth()->user()->id)->count();
        return view('dashboard',compact('total_post','total_active_post','total_inactive_post','this_month_post','permission','total_topic','total_destination','destAsigned'));
    }
}

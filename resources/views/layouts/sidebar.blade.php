<?php 
    $role = DB::table('users')->where('id',auth()->user()->id)->value('role_id');
    $users = DB::table('users')->where('tenant_id',auth()->user()->tenant_id)
        ->whereNotNull('logo')->first();
    if(isset($users->logo) != null || isset($users->logo) != ""){
        $user = url('images/profile').'/'.$users->logo;
    }else{
        $user = url('assets/images/logo.png');
    }
?>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar Toggler (Sidebar) -->
    
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('dashboard')}}">
        <div class="sidebar-brand-icon">
            <img src="{{$user}}">
        </div>
        <div class="sidebar-brand-text mx-3">Blog Master</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
    @can('post_view', $permission)
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Posts</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('post_index')}}">All Posts</a>
                @can('post_create', $permission)
                    <a class="collapse-item" href="{{route('post_create')}}">Add New</a>
                @endcan
            </div>
        </div>
    </li>
    @endcan

    <!-- Divider -->
    <hr class="sidebar-divider">
    @can('category_create', $permission)
    <li class="nav-item">
        <a class="nav-link" href="{{route('category_index')}}"> 
            <i class='fas fa-chess-board'></i>
            <span>Categories</span>
        </a>
    </li>
    @endcan
    @can('tag_create', $permission)
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link" href="{{route('tag_index')}}"> 
            <i class="fa fa-tag" aria-hidden="true"></i>
            <span>Tags</span>
        </a>
    </li>
    @endcan
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTopic"
            aria-expanded="true" aria-controls="collapseTopic">
            <i class="fa fa-tasks" aria-hidden="true"></i>
            <span>Topics</span>
        </a>
        <div id="collapseTopic" class="collapse" aria-labelledby="collapseTopic" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('topics')}}">All Topics</a>
                @can('topic_assign', $permission)
                    <a class="collapse-item" href="{{route('topic_assign')}}">Add New</a>
                @endcan
            </div>
        </div>
    </li>
    @if($role != 0)
        <hr class="sidebar-divider">
        <li class="nav-item">
            <a class="nav-link" href="{{route('my_post')}}"> 
                <i class="fa fa-tag" aria-hidden="true"></i>
                <span>My Post</span>
            </a>
        </li>
    @endif

    @can('destination_list', $permission)
        <hr class="sidebar-divider">
        <li class="nav-item">
            <a class="nav-link" href="{{route('dook_destinations')}}"> 
                <i class="fa fa-map-marker" aria-hidden="true"></i>
                <span>Destinations</span>
            </a>
        </li>
    @endif
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSuggertion"
            aria-expanded="true" aria-controls="collapseSuggertion">
            <i class="fa fa-tasks" aria-hidden="true"></i>
            <span>Suggetions</span>
        </a>
        <div id="collapseSuggertion" class="collapse" aria-labelledby="collapseSuggertion" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('destination_suggetion')}}">Destination Suggetions</a>
                <a class="collapse-item" href="{{route('topic_suggetion')}}">Topic Suggetions</a>
            </div>
        </div>
    </li>
</ul>
<?php
    $notifications = DB::table('notifications')->where('user_id',auth()->user()->id)
        ->orWhere('assigned_to',auth()->user()->id)
        ->orderBy('status_view', 'ASC')
        ->orderBy('created_at', 'DESC')
        ->take(15)
        ->get();
    $total_notification = DB::table('notifications')
        ->where('status_view',0)
        ->where(function ($query) {
            $query->where('user_id',auth()->user()->id)
                ->orWhere('assigned_to',auth()->user()->id);
        })
        ->count();

?>
<div class="text-right d-none d-md-inline mt-1">
    <button class="border-1" id="sidebarToggle"><i class="fa fa-bars" aria-hidden="true"></i></button>
</div>
<!-- Topbar Navbar -->
<ul class="navbar-nav ml-auto">

    <!-- Nav Item - Search Dropdown (Visible Only XS) -->
    <li class="nav-item dropdown no-arrow d-sm-none">
        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-search fa-fw"></i>
        </a>
        <!-- Dropdown - Messages -->
        <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
            aria-labelledby="searchDropdown">
            <form class="form-inline mr-auto w-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small"
                        placeholder="Search for..." aria-label="Search"
                        aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </li>
    <li class="nav-item dropdown no-arrow mx-1">
        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-bell fa-fw"></i>
            <span class="badge badge-danger badge-counter">{{$total_notification}}</span>
        </a>
        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
            aria-labelledby="alertsDropdown">
            <h6 class="dropdown-header">
                Alerts Center 
            </h6>
            @foreach($notifications as $notification)
                <a class="dropdown-item d-flex align-items-center" onclick="showNotification({{$notification->id}})" style="cursor: pointer;">
                    <div class="mr-3">
                        <div class="icon-circle bg-primary">
                            <i class="far fa-bell text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">{{date('d M, Y', strtotime($notification->created_at))}}</div>
                        @if($notification->status_view == 0)
                            <span class="font-weight-bold">{!! $notification->body !!}</span>
                        @else
                            <span class="font-weight-normal">{!! $notification->body !!}</span>
                        @endif
                    </div>
                </a>
            @endforeach
            <a class="dropdown-item text-center small text-gray-700" href="{{route('get_notification')}}">Show All Alerts</a>
        </div>
    </li>

  
   <!--  <li class="nav-item dropdown no-arrow mx-1">
        <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-envelope fa-fw"></i>
            <span class="badge badge-danger badge-counter">7</span>
        </a>
        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
            aria-labelledby="messagesDropdown">
            <h6 class="dropdown-header">
                Message Center
            </h6>
            <a class="dropdown-item d-flex align-items-center" href="#">
                <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="img/undraw_profile_1.svg"
                        alt="...">
                    <div class="status-indicator bg-success"></div>
                </div>
                <div class="font-weight-bold">
                    <div class="text-truncate">Hi there! I am wondering if you can help me with a
                        problem I've been having.</div>
                    <div class="small text-gray-500">Emily Fowler · 58m</div>
                </div>
            </a>
            <a class="dropdown-item d-flex align-items-center" href="#">
                <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="img/undraw_profile_2.svg"
                        alt="...">
                    <div class="status-indicator"></div>
                </div>
                <div>
                    <div class="text-truncate">I have the photos that you ordered last month, how
                        would you like them sent to you?</div>
                    <div class="small text-gray-500">Jae Chun · 1d</div>
                </div>
            </a>
            <a class="dropdown-item d-flex align-items-center" href="#">
                <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="img/undraw_profile_3.svg"
                        alt="...">
                    <div class="status-indicator bg-warning"></div>
                </div>
                <div>
                    <div class="text-truncate">Last month's report looks great, I am very happy with
                        the progress so far, keep up the good work!</div>
                    <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                </div>
            </a>
            <a class="dropdown-item d-flex align-items-center" href="#">
                <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                        alt="...">
                    <div class="status-indicator bg-success"></div>
                </div>
                <div>
                    <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                        told me that people say this to all dogs, even if they aren't good...</div>
                    <div class="small text-gray-500">Chicken the Dog · 2w</div>
                </div>
            </a>
            <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
        </div>
    </li> -->

    <div class="topbar-divider d-none d-sm-block"></div>

    <!-- Nav Item - User Information -->
    <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{auth()->user()->name}}</span>
            <img class="img-profile rounded-circle"
                src="{{asset('/images/placeholde_person.png')}}">
        </a>
        <!-- Dropdown - User Information -->
        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
            aria-labelledby="userDropdown">
            <p class="dropdown-item">
                <?php   
                    $roles = DB::table('roles')->where('id',auth()->user()->role_id)
                            ->select('name')->first();
                    if($roles){
                        $role = $roles->name;
                    }else{
                        $role = "Admin";
                    }

                ?>
               <span class="font-weight-bold">Welcome !</span> ({{$role}})
            </p>
            @if(auth()->user()->role_id == 0)
            <a href="{{route('company_profile')}}" class="dropdown-item notify-item">
                <i class="fas fa-user-circle fa-sm fa-fw mr-2 text-gray-600"></i>
                <span>Company Profile</span>
            </a>
            @endif
            @can('post_create', $permission)
            <a href="{{route('roles')}}" class="dropdown-item notify-item">
                <i class="fas fa-user-edit fa-sm fa-fw mr-2 text-gray-600"></i>
                <span>Manage Role</span>
            </a>
            <a href="{{route('users')}}" class="dropdown-item notify-item">
                <i class="fa fa-cog fa-sm fa-fw mr-2 text-gray-600" aria-hidden="true"></i>
                <span>Manage Users</span>
            </a>
            @endcan
            <div class="dropdown-divider"></div>
            @guest
                @if (Route::has('login'))
                    <a class="dropdown-item" href="{{ route('login') }}">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-600"></i>
                    Login</a>
                @endif
             @else
                 <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                   <i class='fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-600'></i> {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            @endguest
        </div>
    </li>

</ul>
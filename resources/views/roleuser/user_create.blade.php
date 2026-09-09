@extends('layouts.app')

@section('content')

<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Users</li>
  </ol>
</nav>
<span class="h3 text-gray-800">Manage Users</span> 
<div class="row mt-4"> </div>

<div class="card shadow mb-4 mt-4">
<div class="col-md-12 d-flex align-items-center justify-content-between mt-2">
    <h5 class="text-dark">Users List</h5>
    @can('user_create', $permission)
        <a href="" class="btn btn-info btn-sm pull-right" data-toggle="modal" data-target="#modal">Create New User</a>
    @endcan
</div>
@if(\Session::has('msg'))
    <div class="alert text-danger alert-dismissible fade show " id="myElem" role="alert" style="">
        <div id="success_msg" style="">
            {{\Session::get('msg')}}
        </div>
        
    </div>
@endif
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Country</th>
                        <th>Role</th>
                        @can('user_activate_inactivate', $permission)
                            <th>Status</th>
                        @endcan
                        @can('user_edit', $permission)
                            <th>Action</th>
                        @endcan
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($users as  $key => $row)
                    <tr>
                        <td>{{ ($users->currentpage()-1) * $users->perpage() + $key + 1 }}</td>
                        <td>{{$row->name}}</td>
                        <td>{{$row->email}}</td>
                        <td>{{$row->mobile}}</td>
                        <td>{{$row->country}}</td>
                        <td>
                            @foreach($row->sub_name as $value)
                                <span class="badge badge-success text-light p-1"> {{ucfirst($value->name)}}</span>
                            @endforeach
                        </td>
                        @can('user_activate_inactivate', $permission)
                            <td>@if($row->verified == 0)
                                    <a class="userdiasable badge badge-danger text-light" data-id="{{ $row->id }}" data-status="{{ $row->verified }}" style="cursor: pointer; color: #2f8263;">Inactive
                                    </a>
                                @else
                                    <a class="userdiasable badge badge-success text-light" data-id="{{ $row->id }}" data-status="{{ $row->verified }}" style="cursor: pointer; color: #F9423C;">
                                        Active
                                    </a>
                                @endif
                            </td>
                        @endcan
                        @can('user_edit', $permission)
                            <td>
                                <a href="" class="btn btn-sm" data-toggle="modal" data-target="#modal{{$row->id}}" style="cursor: pointer; color: black;"><i class="fa fa-edit"></i></a> ||
                                <a href="" class="btn btn-sm delete" data-id="{{ $row->id }}" data-status="{{ $row->verified }}" style="cursor: pointer; color: #F9423C;">
                                    <i class="fa fa-trash"></i></a>
                                <!-- <a href="" class="dropdown-item"   data-toggle="modal" data-target="">Email Send Again</a>          -->
                                <div class="modal fade bd-example-modal-sm" role="dialog" id="modal{{$row->id}}" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document" style="width:30%;">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-dark" id="mySmallModalLabel">Update User</h5>
                                                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{route('user_update')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$row->id}}">
                                                <div class="row p-1">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">User Name </label>
                                                            <input type="text" class="form-control" id="name" name="name" value="{{$row->name}}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Email </label>
                                                            <input type="email" class="form-control" id="email" name="email" value="{{$row->email}}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Phone </label>
                                                            <input type="text" class="form-control" id="phone" name="phone" value="{{$row->mobile}}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Select Country </label>
                                                            <select class="form-control country" name="country" id="countrys">
                                                                <option value="{{$row->country}}" selected>{{$row->country}}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Role</label>
                                                            <select class="form-control" name="role">
                                                                @foreach($role as $data)
                                                                    <option value="{{$data->id}}" @if($row->role_id == $data->id) selected @endif>{{ucfirst($data->name)}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group dc_disable_password">
                                                            <label for="exampleFormControlInput1">Old Password </label>
                                                            <br>
                                                            <input type="password" class="form-control" id="old_password" value="{{$row->password_text}}" disabled>
                                                            <i toggle="#old_password" class="fa fa-fw fa-eye field-icon toggle-password"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">New Password </label>
                                                            <input type="text" class="form-control" id="new_password" name="new_password" required>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                            </div>
                                         </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        @endcan
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade bd-example-modal-sm" role="dialog" id="modal" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="width:30%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mySmallModalLabel">Create User</h5>
                <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('user_store')}}" method="post">
                    @csrf
                    <div class="row p-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">User Name </label>
                                <input type="text" class="form-control" id="uname" name="name" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Email </label>
                                <input type="email" class="form-control" id="uemail" name="email" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Phone </label>
                                <input type="text" class="form-control" id="uphone" name="phone" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Select Country </label>
                                <select class="form-control country" name="country" id="country">
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Role</label>
                                <select class="form-control" name="role">
                                    @foreach($role as $row)
                                        <option value="{{$row->id}}">{{$row->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard</button>
                <button type="submit" class="btn btn-primary btn-sm">Save</button>

            </div>
        </form>
        </div>
    </div>
</div>
<style type="text/css">
    .pagination{float: right;}
    span.select2-selection.select2-selection--single {
        width: 335px;
    }
</style>
@endsection
@section('footer')
    <script type="text/javascript">
        $('.country').select2({
           placeholder: 'Search Country',
           ajax: {
               url: "{{route('country_search')}}",
               dataType: 'json',
               delay: 250,
               processResults: function (data) {
                   return {
                       results: jQuery.map(data, function (item) {
                           return {
                               text: item.country_name,
                               id: item.country_name
                           }
                       })
                   };
               },
               cache: true
           }
       });
    </script>
    <script type="text/javascript">
        $(".toggle-password").click(function() {

            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    </script>
    <script type="text/javascript">
        $(".userdiasable").click(function () {
            //alert('gg');
            var status = $(this).data("status");
            var flag = (status == 0) ? 'active' : 'inactive';
            if (confirm("Are you sure you want to " + flag + " this User?"))
            var id = $(this).data("id");
            var token = "{{ csrf_token() }}";
            if (id) {
                $.ajax({
                    url: '/user/disable-enable/' + id,
                    type: 'POST',
                    data: {
                        "id": id,
                        "_token": token,
                    },
                    success: function (data) {
                        window.location.reload();
                    }
                });
            }
        });
    </script>
    <script type="text/javascript">
        $(".delete").click(function () {
            if (confirm("Are you sure you want to delete this user?"))
            var id = $(this).data("id");
            var status = $(this).data("status");

            var token = "{{ csrf_token() }}";
            if (id) {
                $.ajax({
                    url: '/user-delete/' + id,
                    type: 'POST',
                    data: {
                        "id": id,
                        "_token": token,
                    },
                    success: function (data) {
                        window.location.reload();
                    }
                });
            }
        });
    </script>
    <script>
        $("#imgInp").change(function(){
        readURL(this);
        });
        $("#myElem").show().delay(4000).queue(function(n) {
          $(this).hide(); n();
        });
    </script>
    <style type="text/css">
        .dc_disable_password .fa.fa-fw.fa-eye.field-icon, .dc_disable_password .fa.fa-fw.field-icon {
            position: absolute;
            right: 17px;
            top: 44px;
        }
    </style>
@endsection
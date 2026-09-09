@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Destinations</li>
  </ol>
</nav>
<span class="h3 text-gray-800">Destinations <sup class="text-danger">({{$totalDest}})</sup></span>
 @if($user == 1)
    <span class="texDecoration">
        <a class="page-title-action" id="pullDestinations">Pull Destinations</a>
    </span><span id="message"></span>
 @endif
 <div class="row mt-4 postSearchSelect">
    <form action="{{route('dook_destinations')}}" method="get" style="display: inline-flex; margin-left: -12px;">
        <div class="col-md-3 mlRight">
            
            <select class="form-select status_filter" id="status_filter" name="status_filter">
                <option value="" <?php if($status == "no") { echo "selected"; } ?>>Status</option>
                <option value="2" @if($status == 2 && $status != 'no') selected="" @endif>Complete</option>
                <option value="1" @if($status == 1 && $status != 'no') selected="" @endif>Under Review</option>
                <option value="in" @if($status == 0 && $status != 'no') selected="" @endif>In Process</option>
            </select>
        </div>
        <div class="col-md-4 mlRight">
            <input type="text" class="form-control" name="keyword" id="keyword" placeholder="Search by destination, country.." value="{{$keyword}}" style="height: calc(1.1em + 0.75rem + 2px) !important;padding: 0.375rem 0.5rem; !important">
            
        </div>
        <div class="col-md-2 mt-1 mlRight">
            <button type="submit" class="page-title-action">Search</button>
        </div>
    </form>
</div>
<div class="card shadow mb-4" style="margin-top: 30px">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>S.No {{$status}}</th>
                        <th>Destination</th>
                        <th>Country</th>
                        @if($admin_id == auth()->user()->id)
                            <th width="20%">Assign To</th>
                        @endif
                        <th style="width:13%;">Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($destinations as $key => $row)
                    <tr>
                        <td>{{ ($destinations->currentpage()-1) * $destinations->perpage() + $key + 1 }}</td>
                        <td>{{$row->dest_name}}</td>
                        <td>{{$row->country_name}}</td>
                        @if($admin_id == auth()->user()->id)
                            <td style="width:10%"><span id="messageAssign{{$row->id}}"></span>
                                <select class="form-select users" onchange="getval(this);" id="users{{$key}}" name="users">
                                    <option value="">Select</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}" data-id={{$row->id}} @if($row->assigned_to == $user->id) selected @endif>{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                        @endif
                        @if($row->status == 1)
                            <td style="color:green;">Under Review</td>
                        @elseif($row->status == 2)
                            <td style="color:green;">Complete</td>
                        @else
                            <td style="color:#d74b4b;">In Process</td>
                        @endif
                        <td>
                            <a class="viewDestination" href="{{route('view_destination',$row->id)}}" title="View"><i class="fa fa-eye"></i>
                            </a> | 
                            @can('destination_edit', $permission)
                            <a class="editDestination" href="{{route('edit_destination',$row->id)}}" title="Edit">
                                <i class="fa fa-edit"></i>
                            </a>  @endcan
                            @can('destination_status', $permission)|
                           
                            <a class="changeStatus" data-toggle="modal" data-id="{{ $row->id }}" data-status="{{ $row->status }}" title="Change Status" style="cursor: pointer;"><i class='fas fa-exchange-alt'></i></a>
                            @endcan
                        </td> 
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $destinations->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
<div class="modal fade modal-scroll modalRight" id="modal_destination_status" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Change Status</h5>
          <button type="button" class="close btns" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="img-container">
            <form method="post" enctype="multipart/form-data" id="changeDestinationStatusForm">
              @csrf
              <div class="row">
                  <div class="col-md-12 mb-3">
                    <input type="hidden" class="form-control" name="edit_id" id="edit_id">
                    <label for="edit_name" class="form-label">Select Status <sup class="compulsayField">*</sup></label>
                    <select class="form-select dest_status" name="dest_status">
                    <option value="0">In Process</option>
                    <option value="1">Under Review</option>
                    <option value="2">Complete</option>
                    </select>
              </div>
            </form>
          </div>
        </div>
        <div class="modal-footer">
          <span class="text-success d-block" id="messageU" style="margin-right: 10px"></span>
          <button type="button" id="changeStatusDestination" class="btn btn-primary">Update
          </button>
          <button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close">
              Close
          </button>
        </div>
    </div>
  </div>
</div>
<style type="text/css">
    td p {
        margin-bottom: 0px;
    }
</style>

@endsection

@section('footer')
<script type="text/javascript" src="{{asset('assets/js/ajax_js.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function () {
    $('#pullDestinations').click(function (e) {
      e.preventDefault();
      $('#pullDestinations').prop('disabled', true);
      $('#pullDestinations').html('Please wait...')
      $.ajax({
        method: 'GET',
        url: "{{ route('dook_destinations_pull') }}",
        data: "",
        contentType: false,
        processData: false,
        success: function (data) {
          $('#message').html("<span class='sussecmsg'>Destinations pulled successfully!</span>");
          $('#pullDestinations').prop('disabled', false);
          $('#pullDestinations').html('Update')
          window.location.reload();
        },
        errors: function () {
            $('#message').html("<span class='sussecmsg'>Somthing went wrong!</span>");
        }
      });
    });
    
  });
</script>
<script type="text/javascript">
    $('.changeStatus').click(function() {
        $('#modal_destination_status').modal('show');
        var id = $(this).data('id');
        var status = $(this).data("status");

        $("#edit_id").val(id);
        $("#modal_destination_status").find("select[name='dest_status'] option[value='"+status+"']").attr('selected', 'selected');
    });
    $('#changeStatusDestination').click(function (e) {
      e.preventDefault();
      $('#changeStatusDestination').prop('disabled', true);
      $('#changeStatusDestination').html('Please wait...')
      var formDatas = new FormData(document.getElementById('changeDestinationStatusForm'));
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'POST',
        url: "{{ route('destination_status_change') }}",
        data: formDatas,
        contentType: false,
        processData: false,
        success: function (data) {
          $('#messageU').html("<span class='sussecmsg'>Status changed successfully!</span>");
          $('#changeStatusDestination').prop('disabled', false);
          $('#changeStatusDestination').html('Update')
          window.location.reload();
        },
        errors: function () {
            $('#messageU').html("<span class='sussecmsg'>Somthing went wrong!</span>");
        }
      });
    });
</script>
<script type="text/javascript">
    function getval(sel) {
        var opt = sel.options[sel.selectedIndex];
        var id = opt.dataset.id;
        var writer_id = opt.value;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: 'POST',
            contentType: "application/json; charset=utf-8",
            url: '/destination_assign/'+id+'/'+writer_id,
            data: { "id": id, "writer_id": writer_id },
            success: function (data) {
                $('#messageAssign'+id).html("<span class='sussecmsg'>Assigned!</span>");
                setTimeout(function () {
                    $('#messageAssign'+id).html("");
                },5000);
                
            }
        });
    }
</script>
<script type="text/javascript">
  var editor =  // 1st change: will need this variable later
  $('#description').summernote({
    spellCheck: true,
    height: 100,   
    focus: false, 
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'underline', 'italic']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['table', ['table']],
      ['view', ['fullscreen', 'codeview']],
    ]
  });

</script>
@endsection

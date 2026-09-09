@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Topics</li>
  </ol>
</nav>
<h1 class="h3 text-gray-800 mb-5">Topics</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Topic</th>
                        @if($admin_id == auth()->user()->id)
                            <th>Writer</th>
                        @endif
                        <th>Created Date</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($topics as $key => $row)
                    <tr>
                        <td>{{ ($topics->currentpage()-1) * $topics->perpage() + $key + 1 }}</td>
                        <td>{{$row->title}}</td>
                        @if($admin_id == auth()->user()->id)
                            <td>{{$row->assignedTo}}</td>
                        @endif
                        <td>{{date('d M, Y', strtotime($row->created_date))}}</td>
                        <td>{!! Str::limit($row->description, 25)!!}</td>
                        @if($row->status == 1)
                            <td style="color:green;">Complete</td>
                        @else
                            <td style="color:#d74b4b;">In Process</td>
                        @endif
                        <td>
                            <a class="viewTopic" href="{{route('topic_view',$row->id)}}"><i class="fa fa-eye"></i>
                            </a> | <a class="suggetions" data-toggle="modal" data-id="{{ $row->id }}" data-suggetion="{{ $row->assigned_to }}" title="Suggestion" style="cursor: pointer;"><i class='far fa-lightbulb'></i></a> | <a class="Notifications" data-toggle="modal" data-id="{{ $row->id }}" data-assigned="{{ $row->assigned_to }}"  data-users="{{ $row->user_id }}" title="Notification Send" style="cursor: pointer;"><i class="fas fa-bell fa-fw"></i></a>
                        </td>  
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $topics->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
<div class="modal fade modal-scroll modalRight" id="modal_topic_suggetion" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Give Suggestion</h5>
          <button type="button" class="close btns" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="img-container">
            <form method="post" enctype="multipart/form-data" id="DestinationsuggetionForm">
              @csrf
              <div class="row">
                <div class="col-md-12 mb-3">
                    <input type="hidden" class="form-control" name="dest_topic_id" id="topic_id">
                    <input type="hidden" class="form-control" name="suggetion_to" id="suggetion_to">
                     <input type="hidden" class="form-control" name="type" id="ttype" value="Topic">
                    <label>Heading</label>
                    <input type="text" class="form-control" name="suggetion_title" id="suggetion_title">
                </div>
                <div class="col-md-12 mb-3">
                    <label for="suggetions" class="form-label">Suggetion</label>
                    <textarea class="form-control" name="suggetions" id="suggetions" rows="6"></textarea>
                </div>
            </div>
            </form>
          </div>
        </div>
        <div class="modal-footer">
          <span class="text-success d-block" id="messageS" style="margin-right: 10px"></span>
          <button type="button" id="suggetionTopic" class="btn btn-primary">Send
          </button>
          <button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close">
              Close
          </button>
        </div>
    </div>
  </div>
</div>

<div class="modal fade modal-scroll modalRight" id="modal_topic_notification" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Send Notification</h5>
          <button type="button" class="close btns" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="img-container">
            <form method="post" enctype="multipart/form-data" id="NotificationForm">
              @csrf
              <div class="row">
                <div class="col-md-12 mb-3">
                    <input type="hidden" class="form-control" name="dest_topic_id" id="topic_id_noti">
                    <input type="hidden" class="form-control" name="assign_to" id="assign_to">
                     <input type="hidden" class="form-control" name="type" id="ttype" value="Topic">
                      <input type="hidden" class="form-control" name="user_id" id="user_id">
                    <label>Heading</label>
                    <input type="text" class="form-control" name="notification_title" id="notification_title">
                </div>
                <div class="col-md-12 mb-3">
                    <label for="notifications" class="form-label">Message</label>
                    <textarea class="form-control" name="notification_message" id="notifications" rows="6"></textarea>
                </div>
            </div>
            </form>
          </div>
        </div>
        <div class="modal-footer">
          <span class="text-success d-block" id="messageN" style="margin-right: 10px"></span>
          <button type="button" id="notificationTopicSend" class="btn btn-primary">Send
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
    $('#storeTopic').click(function (e) {
      e.preventDefault();
      var title = $('#title').val();
      if (title == "") {
          $(".title_error").html('This field is required!');
          $("input#title").focus();
          return false;
      }
      $('#storeTopic').prop('disabled', true);
      $('#storeTopic').html('Please wait...')
      var formDatas = new FormData(document.getElementById('topicForm'));
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'POST',
        url: "{{ route('topic_store') }}",
        data: formDatas,
        contentType: false,
        processData: false,
        success: function (data) {
          $('#message').html("<span class='sussecmsg'>Topic created successfully!</span>");
          $('#storeTopic').prop('disabled', false);
          $('#storeTopic').html('Update')
          window.location.reload();
        },
        errors: function () {
            $('#message').html("<span class='sussecmsg'>Somthing went wrong!</span>");
        }
      });
    });
    // Dropdown
    $('#writer').select2({
        //placeholder: 'Select Writer'
    });
  });
</script>
<script type="text/javascript">
    $('.suggetions').click(function() {
        $('#modal_topic_suggetion').modal('show');
        var id = $(this).data('id');
        var suggetion_to = $(this).data('suggetion');
        
        $("#topic_id").val(id);
        $("#suggetion_to").val(suggetion_to);
    });

    $('#suggetionTopic').click(function (e) {
      e.preventDefault();
      var id = $('#topic_id').val();
      $('#suggetionTopic').prop('disabled', true);
      $('#suggetionTopic').html('Please wait...')
      var formDatas = new FormData(document.getElementById('DestinationsuggetionForm'));
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'POST',
        url: "{{route('destination_suggetion_store')}}",
        data: formDatas,
        contentType: false,
        processData: false,
        success: function (data) {
          $('#messageS').html("<span class='sussecmsg'>Status changed successfully!</span>");
          $('#suggetionTopic').prop('disabled', false);
          $('#suggetionTopic').html('Update')
          window.location.reload();
        },
        errors: function () {
            $('#messageS').html("<span class='sussecmsg'>Somthing went wrong!</span>");
        }
      });
    });
</script>
<script type="text/javascript">
    $('.Notifications').click(function() {
        $('#modal_topic_notification').modal('show');
        var id = $(this).data('id');
        var assignedTo = $(this).data('assigned');
        var userId = $(this).data('users');
        
        $("#topic_id_noti").val(id);
        $("#assign_to").val(assignedTo);
        $("#user_id").val(userId);

        $('#notificationTopicSend').click(function (e) {
          e.preventDefault();
          $('#notificationTopicSend').prop('disabled', true);
          $('#notificationTopicSend').html('Please wait...')
          var formDatas = new FormData(document.getElementById('NotificationForm'));
          $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: 'POST',
            url: "{{route('store_notification')}}",
            data: formDatas,
            contentType: false,
            processData: false,
            success: function (data) {
              $('#messageN').html("<span class='sussecmsg'>Notification send!</span>");
              $('#notificationTopicSend').prop('disabled', false);
              $('#notificationTopicSend').html('Update')
              window.location.reload();
            },
            errors: function () {
                $('#messageN').html("<span class='sussecmsg'>Somthing went wrong!</span>");
            }
          });
        });
    });
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

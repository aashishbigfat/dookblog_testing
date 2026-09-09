@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Destination</li>
  </ol>
</nav>
<span class="h3 text-gray-800">View Destination </span>
 @if($adminUser == 1)
    <span class="texDecoration">
          <a class="suggetions page-title-action" data-toggle="modal" data-id="{{ $destination->id }}" title="Suggestion" style="cursor: pointer;"><i class='far fa-lightbulb'></i> Give Suggetion to {{$user->name}}</a>
    </span><span id="message"></span>
 @endif

<div class="card shadow mb-4" style="margin-top:50px;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered " id="dataTable" width="100%" cellspacing="0">
                <tbody>
                    <tr><th class="tdtrtd">Destination</th><td>{{$destination->dest_name}}</td></tr>
                    <tr><th class="tdtrtd">Country</th><td>{{$destination->country_name}}</td></tr>
                    <tr><th class="tdtrtd">Created By </th> <td>{{$user->name}} /  Total Word Count:<span class="text-danger"> {{$destination->word_count}}</span></td></tr>
                    <tr><th class="tdtrtd">Created Date</th><td>{{date('d M, Y', strtotime($destination->created_date))}}</td></tr>
                    <tr><th class="tdtrtd">Banner Title(1)</th><td>{!! $destination->header_title !!} | <span class="text-danger">{{$wc4}}</span></td></tr>
                    <tr><th class="tdtrtd">Banner Sub Title(2)</th><td>{!! $destination->header_sub_title !!} | <span class="text-danger">{{$wc5}}</span></td></tr>
                    <tr><th class="tdtrtd">Title(3)</th><td>{!! $destination->title !!} | <span class="text-danger">{{$wc2}}</span></td></tr>
                    <tr><th class="tdtrtd">Sub Title(4)</th><td>{!! $destination->sub_title !!} | <span class="text-danger">{{$wc3}}</span></td></tr>
                    <tr><th class="tdtrtd">Description(5)</th><td>{!! $destination->description !!} | <span class="text-danger">{{$wc1}}</span></td></tr>
                    <tr><th class="tdtrtd">Tours Sub Title(6)</th><td>{!! $destination->tour_sub_title !!} | <span class="text-danger">{{$wc6}}</span></td></tr>
                    <tr><th class="tdtrtd">Experience Sub Title(7)</th><td>{!! $destination->experience_sub_title !!} | <span class="text-danger">{{$wc7}}</span></td></tr>
                    <tr><th class="tdtrtd">Attraction Sub Title(8)</th><td>{!! $destination->attraction_sub_title !!} | <span class="text-danger">{{$wc8}}</span></td></tr>
                    <tr><th class="tdtrtd">Event Sub Title(9)</th><td>{!! $destination->event_sub_title !!} | <span class="text-danger">{{$wc9}}</span></td></tr>

                    <tr><th class="tdtrtd">Restaurant Sub Title(10)</th><td>{!! $destination->restaurant_sub_title !!} | <span class="text-danger">{{$wc10}}</span></td></tr>
                    <tr><th class="tdtrtd">Plan A Trip Sub Title(11)</th><td>{!! $destination->trip_sub_title !!} | <span class="text-danger">{{$wc10}}</span></td></tr>
                    <tr>
                    <tr><th class="tdtrtd">Plan A Trip Description(12)</th><td>{!! $destination->trip_description !!} | <span class="text-danger">{{$wc12}}</span></td></tr>
                    <tr>
                        <th class="tdtrtd">Status</th> 
                        @if($destination->status == 1)
                            <td style="color:green;">Under Review</td>
                        @elseif($destination->status == 2)
                            <td style="color:green;">Complete</td>
                        @else
                            <td style="color:#d74b4b;">In Process</td>
                        @endif
                    </tr>
                    @can('destination_edit', $permission)
                    <tr><th class="tdtrtd">Edit Destination</th>
                        <td>
                            <a class="editDestination" href="{{route('edit_destination',$destination->id)}}" title="Edit">
                                <i class="fa fa-edit"></i> Click Here
                            </a> to Edit
                        </td>
                    </tr>
                    @endcan
                     
                  </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade modal-scroll modalRight" id="modal_destination_suggetion" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Give Suggestion ({{$user->name}})</h5>
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
                    <input type="hidden" class="form-control" name="dest_topic_id" id="dest_id">
                    <input type="hidden" class="form-control" name="suggetion_to" id="suggetion_to" value="{{$user->id}}">
                    <input type="hidden" class="form-control" name="type" id="ttype" value="Destination">
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
          <button type="button" id="suggetionDestination" class="btn btn-primary">Send
          </button>
          <button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close">
              Close
          </button>
        </div>
    </div>
  </div>
</div>
<style type="text/css">
    th.tdtrtd {
        width: 30%;
    }
</style>
@endsection

@section('footer')

    <script type="text/javascript">
        $('.suggetions').click(function() {
            $('#modal_destination_suggetion').modal('show');
            var id = $(this).data('id');

            $("#dest_id").val(id);
        });
        $('#suggetionDestination').click(function (e) {
          e.preventDefault();
          var id = $('#dest_id').val();
          $('#suggetionDestination').prop('disabled', true);
          $('#suggetionDestination').html('Please wait...')
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
              $('#suggetionDestination').prop('disabled', false);
              $('#suggetionDestination').html('Update')
              window.location.reload();
            },
            errors: function () {
                $('#messageS').html("<span class='sussecmsg'>Somthing went wrong!</span>");
            }
          });
        });
    </script>
<script type="text/javascript">
  var editor =  // 1st change: will need this variable later
  $('#suggetions').summernote({
    spellCheck: true,
    height: 200,   
    focus: false, 
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'underline', 'italic']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['fontsize', ['fontsize']],
      ['view', ['fullscreen', 'codeview']],
    ],
    callbacks: {
      onPaste: function (e) {
        var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('text/html');
        var bufferText1 = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
        e.preventDefault();
        var div = $('<div />');
        div.append(bufferText);
        div.find('*').removeAttr('style');
        setTimeout(function () {
        if(bufferText){
          document.execCommand('insertHtml', false, div.html());
        }else{
          document.execCommand('insertText', false, bufferText1);
        }
        }, 10);
      }
    },
  });
</script>
@endsection
@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Topic</li>
  </ol>
</nav>
<h1 class="h3 text-gray-800 mb-5">Create Topic</h1>
<form id="topicForm" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-8 mb-3">
          <div class="row">
              <div class="col-md-12 mt-3">
                <label for="title" class="form-label">Topic Name: <sup class="compulsayField">*</sup> <span class="title_ch_error spanColor"></span><span class="spanColor title_error"></span></label>
                <input type="text" class="form-control title" name="title" id="title" placeholder="Enter topic" autocomplete="off" autofocus maxlength="60">
              </div>
              
            <div class="col-md-12 mt-3">
                <label for="comment">Topic Description:</label>
              <textarea class="form-control" rows="10" id="description" name="description" placeholder="write here..."></textarea>
              <p style="float:right;"><span class="totalCharEditor"></span></p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="row">
            <div class="col-md-12 mt-3">
              <label for="sel1" class="form-label">Assigned To:</label>
                <select class="form-select writer" id="writer" name="writer">
                  @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->name}}</option>
                  @endforeach
                </select>
            </div> 
          </div>
        </div>
        <div class="col-md-12 mt-1 mb-4 text-left">
            <span class="text-success d-block" id="message" style="margin-right: 10px"></span>
            <button type="button" id="storeTopic" class="btn btn-primary">Create</button>
        </div>
    </div>  
</form>

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
          window.location = data.url;
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

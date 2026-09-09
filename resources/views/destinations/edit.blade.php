@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{route('dook_destinations')}}">Destinations</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Destination</li>
  </ol>
</nav>
<h1 class="h3 text-gray-800 mb-5">Edit Destination</h1>

<form id="destinationForm" enctype="multipart/form-data" style="margin-top: 4rem!important;">
  <div class="row">
    <div class="col-md-4 col-lg-4 col-xl-4">
        <label>Destination Name</label>
        <input type="text" class="form-control" name="edit_destination" id="edit_destination" readonly ="" value="{{$destination->dest_name}}">
    </div>
    <div class="col-md-4 col-lg-4 col-xl-4">
       
          <label>Country Name</label>
          <input type="text" class="form-control" name="edit_country" id="edit_country" value="{{$destination->country_name}}" readonly="">
        
      </div>
    <div class="col-md-4 col-lg-4 col-xl-4">
     
        <label>Destination Slug URL</label>
        <input type="text" class="form-control" name="edit_slug_url" id="edit_slug_url" value="{{$destination->slug_url}}">
      
    </div>
    <div class="col-md-6 col-lg-6 col-xl-6">
     
        <label>Banner Title(1)</label>
        <input type="text" class="form-control" name="edit_header_title" id="edit_header_title" value="{{$destination->header_title}}">
      <p style="float:right;color:red;"><span>Total Char: </span><span class="totalChar1">{{$wc4}}</span></p>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-6">
     
        <label>Banner Sub Title(2)</label>
        <textarea class="form-control" name="edit_header_sub_title" id="edit_header_sub_title">{{$destination->header_sub_title}}</textarea>
      <p style="float:right;color:red;"><span>Total Char: </span><span class="totalChar2">{{$wc5}}</span></p>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-6">
     
        <label>Title(3)</label>
        <input type="text" class="form-control" name="edit_title" id="edit_title" value="{{$destination->title}}">
      <p style="float:right;color:red;"><span>Total Char: </span><span class="totalChar3">{{$wc2}}</span></p>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-6">
     
        <label>Sub Title(4)</label>
        <textarea class="form-control" name="edit_sub_title" id="edit_sub_title" rows="6">{{$destination->sub_title}}</textarea>
     <p style="float:right;color:red;"><span>Total Char: </span><span class="totalChar4">{{$wc3}}</span></p>
    </div>
    
    <div class="col-md-12 col-lg-12 col-xl-12">
     
        <label>Description(5)</label>
        <textarea class="form-control" name="edit_description" id="edit_description" style="height: 160px">{{$destination->description}}</textarea>
     <p style="float:right;color:red;"><span>Total Char: </span><span class="totalChar5">{{$wc1}}</span></p>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-6">
     
        <label>Tours Sub Title(6)</label>
        <textarea class="form-control" name="edit_tour_sub_title" id="edit_tour_sub_title" rows="6">{{$destination->tour_sub_title}}</textarea>
    <p style="float:right;color:red;"><span>Total Char: </span><span class="totalChar6">{{$wc6}}</span></p>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-6">
     
        <label>Experience Sub Title(7)</label>
        <textarea class="form-control" name="edit_experience_sub_title" id="edit_experience_sub_title" rows="6">{{$destination->experience_sub_title}}</textarea><p style="float:right;color:red;"><span>Total Char: </span><span class="totalChar7">{{$wc7}}</span></p>
      
    </div>
    <div class="col-md-6 col-lg-6 col-xl-6">
     
        <label>Attraction Sub Title(8)</label>
        <textarea class="form-control" name="edit_attraction_sub_title" id="edit_attraction_sub_title" rows="6">{{$destination->attraction_sub_title}}</textarea><p style="float:right;color:red;"><span>Total Char: </span><span class="totalChar8">{{$wc8}}</span></p>
    
    </div>
    {{--<div class="col-md-6 col-lg-6 col-xl-6">
     
        <label>Event Sub Title(9)</label>
        <textarea class="form-control" name="edit_event_sub_title" id="edit_event_sub_title" rows="6">{{$destination->event_sub_title}}</textarea>
        <p style="float:right;color:red;"><span>Total Char: </span><span class="totalChar9">{{$wc9}}</span></p>
      </div>
      <div class="col-md-6 col-lg-6 col-xl-6">
     
        <label>Restaurant Sub Title(10)</label>
        <textarea class="form-control" name="edit_restaurant_sub_title" id="edit_restaurant_sub_title" rows="6">{{$destination->restaurant_sub_title}}</textarea><p style="float:right;color:red;"><span>Total Char: </span><span class="totalChar10">{{$wc10}}</span></p>
      </div>
    --}}
    <div class="col-md-6 col-lg-6 col-xl-6">
     
        <label>Plan A Trip Sub Title(11)</label>
        <textarea class="form-control" name="edit_trip_sub_title" id="edit_trip_sub_title" rows="6">{{$destination->trip_sub_title}}</textarea>
     <p style="float:right;color:red;"><span>Total Char: </span><span class="totalChar11">{{$wc11}}</span></p>
    </div>
    <div class="col-md-12 col-lg-12 col-xl-12">
     
        <label>Plan A Trip Description(12)</label>
        <textarea class="form-control" name="edit_trip_description" id="edit_trip_description">{{$destination->trip_description}}
        </textarea>
      <p style="float:right;color:red;"><span>Total Char: </span><span class="totalChar12">{{$wc12}}</span></p>
    </div>
    <div class="col-md-12 mt-3 text-right">
      <span class="text-success d-block" id="message" style="margin-right: 10px"></span>
      <button type="button" id="updateDestnation" class="btn btn-primary">Update</button>
    </div>
  </div>
      
</form>
<style type="text/css">
  #destinationForm label {
    margin-top: 15px;
}
</style>

@endsection

@section('footer')
<script type="text/javascript" src="{{asset('assets/js/ajax_js.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function () {
    $('#updateDestnation').click(function (e) {
      e.preventDefault();
      $('#updateDestnation').prop('disabled', true);
      $('#updateDestnation').html('Please wait...')
      var formDatas = new FormData(document.getElementById('destinationForm'));
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'POST',
        url: "{{ route('update_destination', request()->route('id')) }}",
        data: formDatas,
        contentType: false,
        processData: false,
        success: function (data) {
          $('#message').html("<span class='sussecmsg'>Destination updated successfully!</span>");
          $('#updateDestnation').prop('disabled', false);
          $('#updateDestnation').html('Update')
          window.location = data.url;
        },
        errors: function () {
            $('#message').html("<span class='sussecmsg'>Somthing went wrong!</span>");
        }
      });
    });
  });
</script>
<script type="text/javascript">
  var editor =  // 1st change: will need this variable later
  $('#edit_trip_description').summernote({
    spellCheck: true,
    height: 500,   
    focus: false, 
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'underline', 'italic']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['fontsize', ['fontsize']],
      ['insert', ['link']],
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
        let characters = $('#edit_trip_description').summernote('code').replace(/(<([^>]+)>)/ig, "");
        let totalCharacters = characters.length;
        $(".totalCharEditor").text(totalCharacters);
        var t = e.currentTarget.innerText;
      }
    },
    callbacks: {
      onKeydown: function(e) {
        let characters = $('#edit_trip_description').summernote('code').replace(/(<([^>]+)>)/ig, "");
        let totalCharacters = characters.length;
        $(".totalChar12").text(totalCharacters);
      },
      onKeyup: function(e) {
        var t = e.currentTarget.innerText;
        $('#edit_trip_description').text(t.trim().length);
      }
    },
  });
</script>
<script type="text/javascript">
  var editor =  // 1st change: will need this variable later
  $('#edit_description').summernote({
    spellCheck: true,
    height: 250,   
    focus: false, 
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'underline', 'italic']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['fontsize', ['fontsize']],
      ['insert', ['link']],
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
        let characters = $('#edit_description').summernote('code').replace(/(<([^>]+)>)/ig, "");
        let totalCharacters = characters.length;
        $(".totalCharEditor").text(totalCharacters);
        var t = e.currentTarget.innerText;
      }
    },
    callbacks: {
      onKeydown: function(e) {
        let characters = $('#edit_description').summernote('code').replace(/(<([^>]+)>)/ig, "");
        let totalCharacters = characters.length;
        $(".totalChar5").text(totalCharacters);
      },
      onKeyup: function(e) {
        var t = e.currentTarget.innerText;
        $('#edit_description').text(t.trim().length);
      }
    },
  });
</script>
<script type="text/javascript">
  $("#edit_header_title").keyup(function(){
    el = $(this);
        $(".totalChar1").text(el.val().length);
  });
  $("#edit_header_sub_title").keyup(function(){
      el = $(this);
          $(".totalChar2").text(el.val().length);
  });
  $("#edit_title").keyup(function(){
      el = $(this);
          $(".totalChar3").text(el.val().length);
  });
  $("#edit_sub_title").keyup(function(){
      el = $(this);
          $(".totalChar4").text(el.val().length);
  });
  $("#edit_description").keyup(function(){
      el = $(this);
          $(".totalChar5").text(el.val().length);
  });
  $("#edit_tour_sub_title").keyup(function(){
      el = $(this);
          $(".totalChar6").text(el.val().length);
  });
  $("#edit_experience_sub_title").keyup(function(){
      el = $(this);
          $(".totalChar7").text(el.val().length);
  });
  $("#edit_attraction_sub_title").keyup(function(){
      el = $(this);
          $(".totalChar8").text(el.val().length);
  });
  $("#edit_event_sub_title").keyup(function(){
      el = $(this);
          $(".totalChar9").text(el.val().length);
  });
  $("#edit_restaurant_sub_title").keyup(function(){
      el = $(this);
          $(".totalChar10").text(el.val().length);
  });
  $("#edit_trip_sub_title").keyup(function(){
      el = $(this);
          $(".totalChar11").text(el.val().length);
  });
</script>
@endsection

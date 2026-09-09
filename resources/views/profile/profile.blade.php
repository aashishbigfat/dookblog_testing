@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Company Profile</a></li>
  </ol>
</nav>
<h1 class="h3 text-gray-800 mb-5">Edit Profile</h1>

<form id="profileForm" enctype="multipart/form-data">
  <div class="row">
    <div class="col-md-8 mb-3">
      <div class="row">
        <div class="col-md-6 mt-3">
          <label for="product" class="form-label">Product Name </label>
          <input type="hidden" name="user_id" value="{{$user->id}}">
          <input type="text" class="form-control" name="product" id="product" placeholder="Enter product" value="{{$user->product}}" autocomplete="off">
        </div>
        <div class="col-md-6 mt-3">
          <label for="slug" class="form-label">Conatct Name </label>
          <input type="text" class="form-control" name="conatact_name" id="conatact_name" placeholder="Enter Name" value="{{$user->name}}" autocomplete="off">
        </div>
        <div class="col-md-6 mt-3">
          <label for="Modified date" class="form-label">Company Name</label>
          <input type="text" class="form-control" name="company_name" id="company_name" value="{{$user->company_name}}">
        </div>
        <div class="col-md-6 mt-3">
          <label for="Modified date" class="form-label">Contact Number</label>
          <input type="text" class="form-control" name="mobile" id="mobile" value="{{$user->mobile}}">
        </div>
        <div class="col-md-12 mt-3">
          <label for="comment">About Company: <span class="spanColor short_description_error"></span></label>
          <textarea class="form-control" rows="10" id="about_company" name="about_company" placeholder="write about company...">{{$user->about_company}} </textarea>
        </div>  
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="row">
        <div class="col-md-12 mt-3">
          <label for="Published date" class="form-label">Email <sup class="compulsayField">*</sup></label>
          <input type="text" class="form-control" name="email" id="email" value="{{$user->email}}" readonly>
        </div>
        <div class="col-md-12 mt-3">
          <label for="Modified date" class="form-label">Country</label>
          <input type="text" class="form-control" name="country" id="country" value="{{$user->country}}">
        </div>
        <div class="col-md-12 mt-3">
          <label for="comment">Address</label>
          <textarea class="form-control" rows="4" id="address" name="address" placeholder="write address...">{{$user->address}} </textarea>
        </div>
        <hr class="mt-4">
        <div class="col-md-12 form-group mt-3 mb-2">
          <div class="d-flex align-items-center">
              <div class="from-group choosebannerselect position-relative" style="margin-right:1rem">
                  <label for="img_file" translate="">Choose Logo</label>
                  <input type="file" name="company_logo" id="company_logo" class="banner-img" onchange="readURLImage(this);">
              </div>
              <div class="choosebannerShow" id="post_uploaded_image">
                @if($user->logo)
                  <img src="{{asset('/images/profile')}}/{{$user->logo}}" id="filePath" alt="your image" class="img_url" onclick="triggerImage()">
                @else
                  <img src="{{asset('/images/no-img.jpg')}}" id="filePath" alt="your image" class="img_url" onclick="triggerImage()">
                @endif
              </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12 mt-3 text-left">
      <span class="text-success d-block" id="message" style="margin-right: 10px"></span>
      <button type="button" id="storeProfile" class="btn btn-primary">Update</button>
    </div>
  </div>
      
</form>

@endsection

@section('footer')
<script type="text/javascript" src="{{asset('assets/js/ajax_js.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function () {
    $('#storeProfile').click(function (e) {
      e.preventDefault();
      var title = $('#title').val();
      if (title == "") {
          $(".title_error").html('This field is required!');
          $("input#title").focus();
          return false;
      }
      $('#storeProfile').prop('disabled', true);
      $('#storeProfile').html('Please wait...')
      var formDatas = new FormData(document.getElementById('profileForm'));
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'POST',
        url: "{{ route('company_profile_store') }}",
        data: formDatas,
        contentType: false,
        processData: false,
        success: function (data) {
          $('#message').html("<span class='sussecmsg'>Post published successfully!</span>");
          $('#storeProfile').prop('disabled', false);
          $('#storeProfile').html('Update')
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
  function readURLImage(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('#filePath')
          .attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
    function triggerImage(){
      $('#company_logo').trigger('click');
    }
</script>
@endsection

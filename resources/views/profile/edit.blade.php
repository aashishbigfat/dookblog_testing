@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('post_index')}}">Profile</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
  </ol>
</nav>
<h1 class="h3 text-gray-800 mb-5">Edit Profile</h1>



<form id="postForm" enctype="multipart/form-data">
  <div class="row">
    <div class="col-md-8 mb-3">
      <div class="row">
          <div class="col-md-4 mt-3">
            <label for="name" class="form-label">Name </label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Enter name" value="{{auth()->user()->name}}" autocomplete="off">
          </div>
          <div class="col-md-4 mt-3">
            <label for="slug" class="form-label">Email </label>
            <input type="text" class="form-control" name="slug"  value="{{auth()->user()->email}}" autocomplete="off" readonly>
          </div>
          <div class="col-md-4 mt-3">
            <label for="name" class="form-label">Phone </label>
            <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter phone" value="{{auth()->user()->phone}}" autocomplete="off">
          </div>
          
        </div>
    </div>
    <div class="col-md-4 mb-3 mt-3">
      <div class="row">
        <div class="col-md-6 mt-3">
            <label for="Published date" class="form-label">Published date <sup class="compulsayField">*</sup></label>
            <input type="text" class="form-control" name="published_date" id="published_date" value="{{$post->published_date}}">
            <span>Like this: 2022-01-01</span>
          </div>
          <div class="col-md-6 mt-3">
            <label for="Modified date" class="form-label">Modified date <sup class="compulsayField">*</sup></label>
            <input type="text" class="form-control" name="modified_date" id="modified_date" value="{{$post->modified_date}}">
          </div>
          <div class="col-md-12 mt-3">
            <label for="Auther" class="form-label">Auther<sup class="compulsayField">*</sup></label>
            <input type="text" class="form-control" name="auther" id="auther" value="{{$post->auther}}">
          </div>
        <div class="col-md-12 mt-3">
          <label for="sel1" class="form-label">Categories:</label>
            <select class="form-select categories" id="categories" name="categories[]" multiple>
              @foreach($categories as $category)
              <option value="{{$category->name}}" selected>{{$category->name}}</option>
              @endforeach
            </select>
        </div>
        <div class="col-md-12 mt-3">
          <label for="sel1" class="form-label">Tags:</label>
            <select class="form-select tags" id="tags" name="tags[]" multiple>
              @foreach($tags as $tag)
              <option value="{{$tag->name}}" selected>{{$tag->name}}</option>
              @endforeach
            </select>
        </div>

        <div class="col-md-12 form-group mt-2 mb-2">
          <hr>
              <div class="d-flex align-items-center">
                  <div class="from-group choosebannerselect position-relative" style="margin-right:1rem">
                      <label for="img_file" translate="">Choose image</label>
                      <input type="file" id="img_file" class="banner-img">
                      <span class="validationError" id="banner_error"></span>
                      <input type="hidden" name="post_image" id="post_image" class="post_image">
                  </div>
                  <div class="choosebannerShow" id="post_uploaded_image">
                    @if($post->image)
                      <img src="{{asset('/images/posts')}}/{{$post->image}}" id="filePath" alt="your image" class="img_url">
                    @else
                      <img src="{{asset('/images/no-img.jpg')}}" id="filePath" alt="your image" class="img_url">
                    @endif
                  </div>
              </div>
          </div>
      </div>
    </div>
    <div class="col-md-6 mt-3">
        <label for="comment">Meta Tag:</label>
      <textarea class="form-control" id="meta_title" name="meta_title">{{$post->meta_title}}</textarea>
      </div>
      <div class="col-md-6 mt-3">
        <label for="comment">Meta Keywords:</label>
      <textarea class="form-control" id="meta_keywords" name="meta_keywords">{{$post->meta_keywords}}</textarea>
      </div>
      <div class="col-md-12 mt-3">
        <label for="comment">Meta Description:</label>
      <textarea class="form-control" id="meta_description" name="meta_description">{{$post->meta_description}}</textarea>
      </div>
      <div class="col-md-12 mt-3 text-right">
        <span class="text-success d-block" id="message" style="margin-right: 10px"></span>
        <button type="button" id="storePost" class="btn btn-primary">Update</button>
      </div>
  </div>
      
</form>

@endsection

@section('footer')
<script type="text/javascript" src="{{asset('assets/js/ajax_js.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function () {
    $('#storePost').click(function (e) {
      e.preventDefault();
      $('#storePost').prop('disabled', true);
      $('#storePost').html('Please wait...')
      var formDatas = new FormData(document.getElementById('postForm'));
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'POST',
        url: "{{ route('update_post', request()->route('id')) }}",
        data: formDatas,
        contentType: false,
        processData: false,
        success: function (data) {
          $('#message').html("<span class='sussecmsg'>Post published successfully!</span>");
          $('#storePost').prop('disabled', false);
          $('#storePost').html('Update')
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
  // Image crop

    $(document).ready(function(){
      var $modal = $('#modal_banner');
      var image = document.getElementById('banner_sample_image');
      var cropper;
      $('#img_file').on('change',function(event){
        var files = event.target.files;
        var done = function(url){
          image.src = url;
          $modal.modal('show');
        };
        if(files && files.length > 0)
        {
          reader = new FileReader();
          reader.onload = function(event)
          {
            done(reader.result);
          };
          reader.readAsDataURL(files[0]);
        }
      });
      $modal.on('shown.bs.modal', function() {
        cropper = new Cropper(image, {
          aspectRatio: 1.9,
          viewMode: 3,
          preview:'.imgs_preview'
        });
      }).on('hidden.bs.modal', function(){
        cropper.destroy();
          cropper = null;
      });
      $('#banner_crop').click(function(){
        $(".crop_wait_banner").show();
        $(".crop_text_banner").hide();
        canvas = cropper.getCroppedCanvas({
          width:1920,
          height:768
        });
        canvas.toBlob(function(blob){
          url = URL.createObjectURL(blob);
          var reader = new FileReader();
          reader.readAsDataURL(blob);
          reader.onloadend = function(){
          var base64data = reader.result;
          $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },                 
            url:"/post-image-crop",
            type: "POST",
            data:{"image": base64data},
            success:function(data)
            {
              $modal.modal('hide');
              $('#post_image').val(data.url)
              $('#post_uploaded_image').html('<img src="'+data.img+'" id="filePath" alt="your image" class="img_url">');
            }
          });
          };
        });
      });
    });

    $('#certificates').select2({
        placeholder: '-- Click to Select --'
    });

    $(document).ready(function() {
      $('#about_company').summernote({
        
         toolbar: [
            ['style', ['style']],
            ['style', ['bold', 'italic', 'underline']],
            //['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['view', ['codeview']]
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
            styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
            height:320
        });
    });

</script>
<script type="text/javascript">
  var editor =  // 1st change: will need this variable later
  $('#description').summernote({
  toolbar: [
    ['style', ['style']],
    ['font', ['bold', 'underline', 'italic']],
    ['para', ['ul', 'ol', 'paragraph']],
    ['table', ['table']],
    ['insert', ['link', 'picture']],
    ['view', ['fullscreen', 'codeview']],
  ],
    height: 600,   
    focus: false,   
    callbacks: { // 2nd change - onImageUpload inside of "callbacks"
      onImageUpload: function (files) { // 3rd change - dont need other params
        var formData = new FormData();
        formData.append("file", files[0]);

        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{route('summernote_image_upload')}}",
          data: formData,
          type: 'POST',
          cache: false,
          contentType: false,
          processData: false,
          success: function (imageUrl) {
            console.log(imageUrl);
              if (!imageUrl) {
                  // handle error
                  return;
              }
              // 4th change - create img element and add to document
              var imgNode = document.createElement('img');
              imgNode.src = imageUrl;
              editor.summernote('insertNode', imgNode);
          },
          error: function () {
              // handle error
          }
        });
      } 
    }
  });
</script>
@endsection

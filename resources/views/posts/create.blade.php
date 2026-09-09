@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('post_index')}}">Posts</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add New Post</li>
  </ol>
</nav>
<h1 class="h3 text-gray-800 mb-5">Create Post</h1>
<form id="postForm" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col-md-8 mb-3">
      <div class="row">
          <div class="col-md-12 mt-3">
            <input type="hidden" class="form-control title" name="topic_id" @if($topic != "") value="{{$topic->id}}" @endif>

            <label for="title" class="form-label">Title: <sup class="compulsayField">*</sup> <span class="title_ch_error spanColor"></span><span class="spanColor title_error"></span></label>
            <input type="text" class="form-control title" name="title" id="title" placeholder="Enter title" autocomplete="off" autofocus @if($topic != "") value="{{$topic->title}}" @endif>
          </div>
          <div class="col-md-12 mt-3">
            <label for="slug" class="form-label">Slug: <sup class="compulsayField">*</sup></label>
            <input type="text" class="form-control" name="slug" id="slug" placeholder="Enter slug" autocomplete="off">
          </div>
          <div class="col-md-12 mt-3">
            <label for="title" class="form-label">Page Url: <span id="pageUrl"></span></label>
            
          </div>
          <div class="col-md-12 mt-3">
            <label for="comment">Short Description:</label>
          <textarea class="form-control" rows="5" id="short_description" name="short_description" placeholder="write here..."></textarea>
          <p style="float:right;"><span>Char left: </span><span class="totalChar"></span></p>
          </div>
        <div class="col-md-12 mt-3">
            <label for="comment">Description:</label>
          <textarea class="form-control" rows="10" id="description" name="description" placeholder="write here..."></textarea>
          <p style="float:right;"><span class="totalCharEditor"></span></p>
        </div>
         <!-- FAQ Section -->
        <div class="col-md-12 mt-4">
          <hr>
          <h5>FAQ Section</h5>
          <div id="faqContainer">
            <div class="faq-item mb-3" data-index="0">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h6 class="mb-0">FAQ #1</h6>
                  <button type="button" class="btn btn-sm btn-danger remove-faq" onclick="removeFaq(0)" style="display: none;">Remove</button>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label for="faq_question_0" class="form-label">Question:</label>
                    <input type="text" class="form-control" name="faq_questions[]" id="faq_question_0" placeholder="Enter FAQ question">
                  </div>
                  <div class="mb-3">
                    <label for="faq_answer_0" class="form-label">Answer:</label>
                    <textarea class="form-control" name="faq_answers[]" id="faq_answer_0" rows="3" placeholder="Enter FAQ answer"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <button type="button" id="addFaq" class="btn btn-sm btn-success">+ Add FAQ</button>
          <p class="text-muted small mt-2">You can add up to 5 FAQ questions</p>
        </div>
        <!-- End FAQ Section -->
      </div>
    </div>
    <div class="col-md-4 mb-3 mt-3">
      <div class="row">
        <div class="col-md-6 mt-3">
          <label for="Published date" class="form-label">Published date <sup class="compulsayField">*</sup></label>
          <input type="text" class="form-control" name="published_date" id="published_date" value="{{date('Y-m-d')}}">
          <span>Like this: 2022-01-01</span>
        </div>
        <div class="col-md-6 mt-3">
          <label for="Modified date" class="form-label">Modified date <sup class="compulsayField">*</sup></label>
          <input type="text" class="form-control" name="modified_date" id="modified_date" value="{{date('Y-m-d')}}">
        </div>
        <div class="col-md-12 mt-3">
          <label for="Auther" class="form-label">Auther<sup class="compulsayField">*</sup></label>
          <input type="text" class="form-control" name="auther" id="auther" value="{{$auther}}" @if($topicId != "") readonly @endif>
        </div>
        <div class="col-md-12 mt-3">
          <label for="sel1" class="form-label">Categories:</label>
            <select class="form-select categories" id="categories" name="categories[]" multiple>
            </select>
        </div>
        <div class="col-md-12 mt-3">
          <label for="sel1" class="form-label">Tags:</label>
            <select class="form-select tags" id="tags" name="tags[]" multiple>
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
                      <img src="{{asset('/images/no-img.jpg')}}" id="filePath" alt="your image" class="img_url">
                  </div>
              </div>
          </div>
          <hr class="mt-3">
          <div class="col-md-12 mt-3">
            <label for="comment">Meta Title:</label>
            <textarea class="form-control" id="meta_title" name="meta_title" rows="4"></textarea>
          </div>
          <div class="col-md-12 mt-3">
            <label for="comment">Meta Description:</label>
            <textarea class="form-control" id="meta_description" name="meta_description" rows="5"></textarea>
          </div>
          <div class="col-md-12 mt-3">
              <label for="comment">Meta Keywords:</label>
            <textarea class="form-control" id="meta_keywords" name="meta_keywords" rows="4"></textarea>
          </div>  
      </div>
    </div>
    <div class="col-md-12 mt-3 text-right">
        <span class="text-success d-block" id="message" style="margin-right: 10px"></span>
        <button type="button" id="storePost" class="btn btn-primary">Publish</button>
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
      var title = $('#title').val();
      if (title == "") {
          $(".title_error").html('This field is required!');
          $("input#title").focus();
          return false;
      }
      $('#storePost').prop('disabled', true);
      $('#storePost').html('Please wait...')
      var formDatas = new FormData(document.getElementById('postForm'));
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'POST',
        url: "{{ route('store_post') }}",
        data: formDatas,
        contentType: false,
        processData: false,
        success: function (data) {
          $('#message').html("<span class='sussecmsg'>Post Published Successfully!</span>");
          $('#storePost').prop('disabled', false);
          $('#storePost').html('Update')
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
          aspectRatio: 1.8,
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
          width:735,
          height:700
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
</script>
<script type="text/javascript">
   $.extend($.summernote.plugins, {
  'customImage': function (context) {
    var ui = $.summernote.ui;

    context.memo('button.customImage', function () {
      return ui.button({
        contents: '<i class="note-icon-picture"></i>',
        tooltip: 'Insert Image with ALT',
        click: function () {

          // File selector
          var input = $('<input type="file" accept="image/*">');
          input.click();

          input.on('change', function () {
            var file = this.files[0];
            if (!file) return;

            // ✅ Ask ALT
            var altText = prompt("Enter ALT text (SEO):");
            // ✅ Ask TITLE (NEW)
            var titleText = prompt("Enter Image Title (optional):");

            // ✅ Ask Link (optional)
            var link = prompt("Enter image link (optional):");

            var formData = new FormData();
            formData.append("file", file);

            $.ajax({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ route('summernote_image_upload') }}",
              type: "POST",
              data: formData,
              contentType: false,
              processData: false,

              success: function (imageUrl) {

                var img = document.createElement('img');
                img.src = imageUrl;
                img.alt = altText ? altText : "image";
                img.title = titleText ? titleText : "";
                img.style.maxWidth = '100%';

                // wrap with link if provided
                if (link) {
                  var a = document.createElement('a');
                  a.href = link;
                  a.target = "_blank";
                  a.appendChild(img);
                  $('#description').summernote('insertNode', a);
                } else {
                  $('#description').summernote('insertNode', img);
                }
              },

              error: function () {
                alert("Image upload failed");
              }
            });
          });
        }
      }).render();
    });
  }
});
 var editor = $('#description').summernote({
  spellCheck: true,
  height: 500,
  focus: false,
  toolbar: [
    ['style', ['style']],
    ['font', ['bold', 'underline', 'italic']],
    ['para', ['ul', 'ol', 'paragraph']],
    ['fontsize', ['fontsize']],
    ['table', ['table']],
    ['insert', ['link', 'customImage']],
    ['view', ['fullscreen', 'codeview']],
  ],

  // ✅ ALL callbacks combined into one block
  callbacks: {
    // 📋 Handle Paste
    onPaste: function (e) {
      var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('text/html');
      var bufferText1 = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
      e.preventDefault();
      var div = $('<div />');
      div.append(bufferText);
      div.find('*').removeAttr('style');

      setTimeout(function () {
        if (bufferText) {
          document.execCommand('insertHtml', false, div.html());
        } else {
          document.execCommand('insertText', false, bufferText1);
        }
      }, 10);

      let characters = $('#description').summernote('code').replace(/(<([^>]+)>)/ig, "");
      $(".totalCharEditor").text(characters.length);
    },

    // ⌨️ Keydown and Keyup Events
    onKeydown: function (e) {
      let characters = $('#description').summernote('code').replace(/(<([^>]+)>)/ig, "");
      $(".totalCharEditor").text(characters.length);
    },

    onKeyup: function (e) {
      var t = e.currentTarget.innerText;
      $('#description').text(t.trim().length);
    },

    // 🖼️ Image Upload
    onImageUpload: function (files) {
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
          if (!imageUrl) return;

          // ✅ Ask user for hyperlink
          var link = prompt("Enter URL to hyperlink the image (leave blank to skip):");

          var imgNode = document.createElement('img');
          imgNode.src = imageUrl;
          imgNode.style.maxWidth = '100%';

          if (link) {
            var aNode = document.createElement('a');
            aNode.href = link;
            aNode.target = "_blank";
            aNode.appendChild(imgNode);
            editor.summernote('insertNode', aNode);
          } else {
            editor.summernote('insertNode', imgNode);
          }
        },
        error: function () {
          console.error("Image upload failed.");
        }
      });
    }
  }
});

</script>
<script>
let faqCount = 1;

document.getElementById('addFaq').addEventListener('click', function() {
    if (faqCount >= 5) {
        alert('You can only add up to 5 FAQ questions');
        return;
    }
    
    const faqContainer = document.getElementById('faqContainer');
    const newFaqItem = document.createElement('div');
    newFaqItem.className = 'faq-item mb-3';
    newFaqItem.setAttribute('data-index', faqCount);
    
    newFaqItem.innerHTML = `
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">FAQ #${faqCount + 1}</h6>
                <button type="button" class="btn btn-sm btn-danger remove-faq" onclick="removeFaq(${faqCount})">Remove</button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="faq_question_${faqCount}" class="form-label">Question:</label>
                    <input type="text" class="form-control" name="faq_questions[]" id="faq_question_${faqCount}" placeholder="Enter FAQ question">
                </div>
                <div class="mb-3">
                    <label for="faq_answer_${faqCount}" class="form-label">Answer:</label>
                    <textarea class="form-control" name="faq_answers[]" id="faq_answer_${faqCount}" rows="3" placeholder="Enter FAQ answer"></textarea>
                </div>
            </div>
        </div>
    `;
    
    faqContainer.appendChild(newFaqItem);
    faqCount++;
    
    // Show remove button for all items when there's more than 1
    updateRemoveButtons();
});

function removeFaq(index) {
    const faqItem = document.querySelector(`[data-index="${index}"]`);
    if (faqItem) {
        faqItem.remove();
        faqCount--;
        updateFaqNumbers();
        updateRemoveButtons();
    }
}

function updateFaqNumbers() {
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach((item, index) => {
        item.setAttribute('data-index', index);
        const header = item.querySelector('.card-header h6');
        header.textContent = `FAQ #${index + 1}`;
        
        const removeBtn = item.querySelector('.remove-faq');
        removeBtn.setAttribute('onclick', `removeFaq(${index})`);
        
        const questionInput = item.querySelector('input[name="faq_questions[]"]');
        const answerTextarea = item.querySelector('textarea[name="faq_answers[]"]');
        questionInput.id = `faq_question_${index}`;
        answerTextarea.id = `faq_answer_${index}`;
        
        const questionLabel = item.querySelector('label[for^="faq_question"]');
        const answerLabel = item.querySelector('label[for^="faq_answer"]');
        questionLabel.setAttribute('for', `faq_question_${index}`);
        answerLabel.setAttribute('for', `faq_answer_${index}`);
    });
    
    faqCount = faqItems.length;
}

function updateRemoveButtons() {
    const removeButtons = document.querySelectorAll('.remove-faq');
    if (removeButtons.length <= 1) {
        removeButtons.forEach(btn => btn.style.display = 'none');
    } else {
        removeButtons.forEach(btn => btn.style.display = 'block');
    }
}
</script>
@endsection

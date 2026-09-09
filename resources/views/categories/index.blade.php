@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('category_index')}}">Categories</a></li>
    @can('category_create', $permission)
    <li class="breadcrumb-item active" aria-current="page">Add New Category</a></li>
    @endcan
  </ol>
</nav>
<h3 class="h3 text-gray-800 mb-5">Categories</h3>

<form id="categoryForm" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col-md-4 mb-3">
      <h5 class="h5 text-gray">Add New Category</h5>
      <div class="row">
        <div class="col-md-12 mb-3">
          <label for="name" class="form-label">Name <sup class="compulsayField">*</sup> <span class="spanColor name_error"></span></label>
          <input type="text" class="form-control" name="name" id="title" placeholder="Enter name" autocomplete="off">
        </div>
        <div class="col-md-12 mb-3">
          <label for="name" class="form-label">Heading <sup class="compulsayField">*</sup> <span class="spanColor name_error"></span></label>
          <input type="text" class="form-control" name="heading" id="heading" placeholder="Enter heading" autocomplete="off">
        </div>
        <div class="col-md-12 mb-3">
          <label for="slug" class="form-label">Slug <sup class="compulsayField">*</sup></label>
          <input type="text" class="form-control" name="slug" id="slug" placeholder="Enter slug" autocomplete="off">
        </div>
         <div class="col-md-12 mb-3">
            <label for="meta_title" class="form-label">Meta Title <sup class="compulsayField">*</sup></label>
            <input type="text" class="form-control" name="meta_title" id="meta_title" placeholder="Enter Meta Title" autocomplete="off">
          </div>
           <div class="col-md-12 mb-3">
            <label for="meta_keywords" class="form-label">Meta Keyword <sup class="compulsayField">*</sup></label>
            <input type="text" class="form-control" name="meta_keywords" id="meta_keywords" placeholder="Enter Meta Keyword" autocomplete="off">
          </div>
           <div class="col-md-12 mb-3">
            <label for="meta_description" class="form-label">Meta Description<sup class="compulsayField">*</sup></label>
            <input type="text" class="form-control" name="meta_description" id="meta_description" placeholder="Enter Meta Description" autocomplete="off">
          </div>
        <div class="col-md-12 mt-4 text-right">
          <span class="text-success d-block" id="message" style="margin-right: 10px"></span>
          @can('category_create', $permission)
          <button type="button" id="storeCategory" class="btn btn-primary">Add New</button>
          @endcan
        </div>
      </div>
    </div>
    <div class="col-md-8 mb-3">
      @if(count($categories)>0)
        <h5 class="h5 text-gray">All Categories</h5>
        <table class="table table-hover" id="dataTable" width="100%" cellspacing="0" style="border: 1px solid #d9dae5;">
          <thead>
              <tr>
                  <th>S.No</th>
                  <th>Title</th>
                  <th>Slug</th>
                  <th>Count</th>
                  <th>Action</th>
              </tr>
          </thead>
          
          <tbody>
              @foreach($categories as $key => $row)
              <tr>
                  <td>{{ ($categories->currentpage()-1) * $categories->perpage() + $key + 1 }}</td>
                  <td>{{$row->name}}</td>
                  <td>{{$row->slug}}</td>
                  <td>{{$row->count}}</td>
                  <td>
                    @can('category_edit', $permission)
                    <a class="editCategory" data-toggle="modal" data-id="{{ $row->id }}" data-cname="{{ $row->name }}" data-slug="{{ $row->slug }}" data-heading="{{ $row->heading }}" data-meta_title="{{ $row->meta_title }}" data-meta_keywords="{{ $row->meta_keywords }}"  data-meta_description="{{ $row->meta_description }}" title="Edit Category" style="cursor: pointer;margin-right: 5px;"><i class="fa fa-edit"></i>
                    </a>@endcan @can('category_delete', $permission)| <form id="delete-form-{{ $row->id }}" method="post" action="{{route('category_delete',$row->id)}}" style="display: none;">
                          @csrf

                          {{method_field('POST')}} <!-- delete query -->
                          </form>
                          <a href="" class="shadow btn-xs sharp" onclick="
                            if (confirm('Are you sure, You want to delete?')) 
                              {
                                event.preventDefault();
                                document.getElementById('delete-form-{{ $row->id }}').submit();
                              }
                              else
                              {
                                event.preventDefault();
                              }
                            " title="Click to delete">
                            <i class="fa fa-trash" style="color:#d74b4b;"></i>
                        </a>
                        @endcan
                  </td>
              </tr>
              @endforeach
          </tbody>
        </table>
        {{ $categories->links('pagination::bootstrap-4') }}
      @endif
    </div>
  </div>
</form>
<div class="modal fade modal-scroll modalRight" id="modal_category_edit" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Category</h5>
          <button type="button" class="close btns" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="img-container">
            <form method="post" enctype="multipart/form-data" id="updateCategoryForm">
              @csrf
              <div class="row">
                  <div class="col-md-12 mb-3">
                    <input type="hidden" class="form-control" name="edit_id" id="edit_id">
                    <label for="edit_name" class="form-label">Name <sup class="compulsayField">*</sup></label>
                    <input type="text" class="form-control" name="edit_name" id="edit_name" placeholder="Enter name" autocomplete="off">
                  </div>
                   <div class="col-md-12 mb-3">
                    <label for="edit_heading" class="form-label">Heading <sup class="compulsayField">*</sup></label>
                    <input type="text" class="form-control" name="edit_heading" id="edit_heading" placeholder="Enter heading" autocomplete="off">
                  </div>
                  <div class="col-md-12 mb-3">
                    <label for="edit_slug" class="form-label">Slug <sup class="compulsayField">*</sup></label>
                    <input type="text" class="form-control" name="edit_slug" id="edit_slug" placeholder="Enter slug" autocomplete="off">
                  </div>
                   <div class="col-md-12 mb-3">
                    <label for="edit_meta_title" class="form-label">Meta Title <sup class="compulsayField">*</sup></label>
                    <input type="text" class="form-control" name="edit_meta_title" id="edit_meta_title" placeholder="Enter Meta Title" autocomplete="off">
                  </div>
                   <div class="col-md-12 mb-3">
                    <label for="edit_meta_keywords" class="form-label">Meta Keyword <sup class="compulsayField">*</sup></label>
                    <input type="text" class="form-control" name="edit_meta_keywords" id="edit_meta_keywords" placeholder="Enter Meta Keyword" autocomplete="off">
                  </div>
                   <div class="col-md-12 mb-3">
                    <label for="edit_meta_description" class="form-label">Meta Description<sup class="compulsayField">*</sup></label>
                    <input type="text" class="form-control" name="edit_meta_description" id="edit_meta_description" placeholder="Enter Meta Description" autocomplete="off">
                  </div>
              </div>
            </form>
          </div>
        </div>
        <div class="modal-footer">
          <span class="text-success d-block" id="messageU" style="margin-right: 10px"></span>
          <button type="button" id="update_category" class="btn btn-primary">Update
          </button>
          <button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close">
              Close
          </button>
        </div>
    </div>
  </div>
</div>
@endsection

@section('footer')
<script type="text/javascript" src="{{asset('assets/js/ajax_js.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function () {
    $('#storeCategory').click(function (e) {
      e.preventDefault();
       var name = $('#title').val();
      if (name == "") {
          $(".name_error").html('This field is required!');
          $("input#title").focus();
          return false;
      }
      $('#storeCategory').prop('disabled', true);
      $('#storeCategory').html('Please wait...')
      var formDatas = new FormData(document.getElementById('categoryForm'));
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'POST',
        url: "{{ route('category_store') }}",
        data: formDatas,
        contentType: false,
        processData: false,
        success: function (data) {
          $('#message').html("<span class='sussecmsg'>Category added successfully!</span>");
          $('#storeCategory').prop('disabled', false);
          $('#storeCategory').html('Update')
          window.location.reload();
        },
        errors: function () {
            $('#message').html("<span class='sussecmsg'>Somthing went wrong!</span>");
        }
      });
    });

    // Model data
    $('.editCategory').click(function() {
        $('#modal_category_edit').modal('show');
        var id = $(this).data('id');
        var cname = $(this).data('cname');
        var heading = $(this).data('heading');
        var slug = $(this).data('slug');
        var meta_title = $(this).data('meta_title');
        var meta_keywords = $(this).data('meta_keywords');
        var meta_description = $(this).data('meta_description');
       
        $("#edit_name").val(cname);
        $("#edit_slug").val(slug);
        $("#edit_heading").val(heading);
        $("#edit_meta_title").val(meta_title);
        $("#edit_meta_keywords").val(meta_keywords);
        $("#edit_meta_description").val(meta_description);
        $("#edit_id").val(id);
    });
    $('#update_category').click(function (e) {
      e.preventDefault();
      $('#update_category').prop('disabled', true);
      $('#update_category').html('Please wait...')
      var formDatas = new FormData(document.getElementById('updateCategoryForm'));
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'POST',
        url: "{{ route('category_update') }}",
        data: formDatas,
        contentType: false,
        processData: false,
        success: function (data) {
          $('#messageU').html("<span class='sussecmsg'>Category updated successfully!</span>");
          $('#update_category').prop('disabled', false);
          $('#update_category').html('Update')
          window.location.reload();
        },
        errors: function () {
            $('#messageU').html("<span class='sussecmsg'>Somthing went wrong!</span>");
        }
      });
    });
  });
</script>
@endsection

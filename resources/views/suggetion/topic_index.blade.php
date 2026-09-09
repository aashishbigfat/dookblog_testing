@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Topic Suggetions</li>
  </ol>
</nav>
<h1 class="h3 text-gray-800 mb-5">Topic Suggetions</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Heading</th>
                        @if($admin_id == auth()->user()->id)
                            <th>To</th>
                        @endif
                        <th>CreatedAt</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($suggetions as $key => $row)
                    <tr>
                        <td>{{ ($suggetions->currentpage()-1) * $suggetions->perpage() + $key + 1 }}</td>
                        <td>{{$row->heading}}</td>
                        @if($admin_id == auth()->user()->id)
                            <td>{{$row->suggetionTo}}</td>
                        @endif
                        <td>{{date('d M, Y', strtotime($row->created_date))}}</td>
                        <td>{!! Str::limit($row->description, 35)!!}</td>
                        
                        <td>
                            <a class="viewSuggetion" data-toggle="modal" data-id="{{ $row->id }}" data-heading="{{ $row->heading }}" data-description="{!! $row->description !!}" title="View" style="cursor: pointer;"><i class="fa fa-eye"></i></a>
                        </td>  
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $suggetions->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
<div class="modal fade modal-scroll modalRight" id="modal_destination_suggetion" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">View Suggestion</h5>
          <button type="button" class="close btns" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="img-container">
              <div class="row">
                <div class="col-md-12 mb-3">
                    <input type="hidden" class="form-control" name="dest_id" id="dest_id">
                    <label>Heading</label>
                    <input type="text" class="form-control" name="suggetion_title" id="suggetion_title" disabled>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="suggetions" class="form-label">Suggetion</label>
                    <textarea class="form-control" name="description" id="description" rows="10" disabled></textarea>
                </div>
            </div>
          </div>
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
<script type="text/javascript">
    $('.viewSuggetion').click(function() {
        $('#modal_destination_suggetion').modal('show');
        var id = $(this).data('id');
        var heading = $(this).data('heading');
        var description = $(this).data('description');
        $("#suggetion_title").val(heading);
        $('#description').html(description);
        $("#dest_id").val(id);
    });
</script>
@endsection

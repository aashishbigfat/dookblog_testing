@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Notifications</li>
  </ol>
</nav>
<h1 class="h3 text-gray-800 mb-5">All Notifications</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Title</th>
                        <th>Message</th>
                        @if($admin_id == auth()->user()->id)
                            <th>Alert To</th>
                        @endif
                        <th>Date</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($notifications as $key => $row)
                       <tr @if($row->status_view == 0)  style="background: antiquewhite"; @endif>
                          <td>{{ ($notifications->currentpage()-1) * $notifications->perpage() + $key + 1 }}</td>
                          <td>{{$row->title}}</td>
                          <td>{!! $row->body !!}</td>
                          @if($admin_id == auth()->user()->id)
                            <td>{!! $row->writer !!}</td>
                          @endif
                          <td>{{date('d M, Y', strtotime($row->created_at))}}</td>
                         
                       </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $notifications->withQueryString()->links('pagination::bootstrap-4') }}
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

@endsection

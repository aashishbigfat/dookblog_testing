@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Topic view</li>
  </ol>
</nav>
<h1 class="h3 text-gray-800 mb-5">Topic View</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered " id="dataTable" width="100%" cellspacing="0">
                <tbody>
                    <tr><th class="tdtrtd">Title</th><td>{{$topic->title}}</td></tr>
                    <tr><th class="tdtrtd">Assign To</th> <td>{{$topic->assignedTo}}</td></tr>
                    <tr><th class="tdtrtd">Created Date</th><td>{{date('d M, Y', strtotime($topic->created_date))}}</td></tr>
                    <tr><th class="tdtrtd">Description</th><td>{!! $topic->description !!}</td></tr>
                    <tr>
                        <th class="tdtrtd">Status</th> 
                        @if($topic->status == 1)
                            <td style="color:green;">Complete</td>
                        @else
                            <td style="color:#d74b4b;">In Process</td>
                        @endif
                    </tr>
                    @can('topic_edit', $permission)
                    <tr><th class="tdtrtd">Write Content</th>
                        <td>
                            <a class="viewTopic" href="{{route('post_create')}}?topicId={{$topic->id}}">
                                <i class="fa fa-pencil" aria-hidden="true"></i>Click Here
                            </a> to write
                        </td>
                    </tr>
                    @endcan
                     
                  </tbody>
            </table>
        </div>
    </div>
</div>
<style type="text/css">
    th.tdtrtd {
        width: 30%;
    }
</style>
@endsection
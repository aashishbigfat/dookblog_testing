@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('post_index')}}">My Posts</a></li>
    <li class="breadcrumb-item active" aria-current="page">My Posts List</li>
  </ol>
</nav>
<span class="h3 text-gray-800">My Posts</span> 
@can('post_create', $permission)
<span class="texDecoration">
    <a class="page-title-action" href="{{route('post_create')}}">Add New</a>
</span>
@endcan
<ul class="subsubsub mt-3" style="padding-left:0px">
    <li class="all"><a href="?post_status=all" class="current" aria-current="page">All <span class="count">({{$all_post}})</span></a> |</li>
    <li class="publish"><a href="?post_status=publish">Published <span class="count">({{$publish_post}})</span></a> |</li>
    <li class="draft"><a href="?post_status=draft">Drafts <span class="count">({{$draft_post}})</span></a></li>
</ul>
<div class="row mt-3 mb-4 postSearchSelect">
    <form action="{{route('my_post')}}" method="get" style="display: inline-flex;">
        <div class="col-md-2 mlRight">
            <select class="form-select all_dates" id="all_dates" name="date">
                <option value="">All dates</option>
                @foreach($allDate as $date)
                    <option value="{{$date->yearManths}}" @if($date->yearManth == $date) selected @endif>{{$date->yearManth}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mlRight">
            <input type="text" class="form-control" name="keyword" id="keyword" placeholder="Search by title.." value="{{$keyword}}" style="height: calc(1.1em + 0.75rem + 2px) !important;padding: 0.375rem 0.5rem; !important">
            
        </div>
        <div class="col-md-2 mt-1 mlRight">
            <button type="submit" class="page-title-action">Search post</button>
        </div>
    </form>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th style="width: 6%;">Image</th>
                    <th style="width:35%">Title</th>
                    <th>Date</th>
                    <th>W.Count</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            
            <tbody>
                @foreach($posts as $key => $row)
                <tr>
                    <td>{{ ($posts->currentpage()-1) * $posts->perpage() + $key + 1 }}</td>
                    <td><img style="width: 100%" src="{{asset('images/posts/')}}/{{$row->image}}"></td>
                    <td>{{$row->title}}</td>
                    <td>{{date('d M, Y', strtotime($row->published_date))}}</td>
                    <td>{{$row->word_count}}</td>
                    @if($row->status == 1)
                        <td style="color:green;">Published</td>
                    @else
                        <td style="color:#d74b4b;">Draft</td>
                    @endif
                    <td>
                        @can('post_edit', $permission)
                            <a class="editPost" href="{{route('edit_post',$row->id)}}">
                                <i class="fa fa-edit"></i>
                            </a> @endcan @can('post_delete', $permission)| <form id="delete-form-{{ $row->id }}" method="post" action="{{route('post_status_change',$row->id)}}" style="display: none;">
                                @csrf

                                {{method_field('POST')}} <!-- delete query -->
                                </form>
                            @if($row->status == 0)
                            <a href="" class="shadow btn-xs sharp" onclick="
                                if (confirm('Are you sure, You want to publish?')) 
                                {
                                    event.preventDefault();
                                    document.getElementById('delete-form-{{ $row->id }}').submit();
                                }
                                else
                                {
                                    event.preventDefault();
                                }
                                " title="Click to publish">
                                
                                    <i class="fa fa-arrow-up" style="color:#d74b4b;"></i>
                            </a>
                             @else
                                <a href="" class="shadow btn-xs sharp" onclick="
                                if (confirm('Are you sure, You want to draft?')) 
                                {
                                    event.preventDefault();
                                    document.getElementById('delete-form-{{ $row->id }}').submit();
                                }
                                else
                                {
                                    event.preventDefault();
                                }
                                " title="Click to draft">
                                
                                    <i class="fa fa-arrow-down" style="color:#d74b4b;"></i>
                            </a>
                            @endif
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
      {{ $posts->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
<style type="text/css">
    .pagination{float: right;}
</style>
@endsection
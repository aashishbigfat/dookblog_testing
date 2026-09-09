@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" class="breadcrumbNavigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('post_index')}}">Posts</a></li>
    <li class="breadcrumb-item active" aria-current="page">Posts List</li>
  </ol>
</nav>
<span class="h3 text-gray-800">Posts</span> 
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
    <form action="{{route('post_index')}}" method="get" style="display: inline-flex;">
        <div class="col-md-2 mlRight">
            <select class="form-select all_dates" id="all_dates" name="date">
                <option value="">All dates</option>
                @foreach($allDate as $date)
                    <option value="{{$date->yearManths}}" @if($date->yearManth == $date) selected @endif>{{$date->yearManth}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mlRight">
            <select class="form-select category_search" id="category_search" name="cat">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{$category->id}}" @if($category->name == $cat) selected @endif>{{$category->name}}</option>
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
            @include('posts/data')
        </div>
    </div>
</div>
<style type="text/css">
    .pagination{float: right;}
</style>
@endsection
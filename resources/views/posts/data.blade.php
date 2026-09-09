<table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th>S.No</th>
            <th style="width: 5%;">Image</th>
            <th style="width:30%">Title</th>
            <th>Date</th>
            <th>Categories</th>
            <th>Creater</th>
            <th>W.Count</th>
            <th>Status</th>
            <th style="width:8%;">Action</th>
        </tr>
    </thead>
    
    <tbody>
        @foreach($posts as $key => $row)
        <tr>
            <td>{{ ($posts->currentpage()-1) * $posts->perpage() + $key + 1 }}</td>
            <td><img style="width: 100%" src="{{asset('images/posts/')}}/{{$row->image}}"></td>
            <td>{{$row->title}}</td>
            <td>{{date('d M, Y', strtotime($row->published_date))}}</td>
            <td>
                @foreach($row->categories as $category)
                    {{$category->name}}, 
                @endforeach
            </td>
            <td>{{$row->created_by}}</td>
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
                    </a> | <a class="ViewPost" target="_blank" href="https://www.dookinternational.com/blog/{{$row->slug}}">
                        <i class="fa fa-eye"></i>
                    </a> | <form id="delete-form-{{ $row->id }}" method="post" action="{{route('post_status_change',$row->id)}}" style="display: none;">
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
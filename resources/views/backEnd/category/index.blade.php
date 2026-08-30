@extends('backEnd.layouts.master')
@section('title','Category Manage')

@section('content')
<div class="container-fluid">
    
    <div class="page-title-box d-flex align-items-center justify-content-between py-3">
        <div>
            <h4 class="page-title mb-1 text-dark fw-bold">Categories</h4>
            <p class="text-muted font-size-13 mb-0">Manage top-level product groups and homepage visibility.</p>
        </div>
        <div class="page-title-right">
            <a href="{{route('categories.create')}}" class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="fe-plus me-1"></i> Add Category
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-hover w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th>Image</th>
                                <th>Category Name</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 150px;">Action</th>
                            </tr>
                        </thead>                
                        <tbody>
                            @foreach($data as $key=>$value)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                
                                <td>
                                    <img src="{{asset($value->image)}}" class="cat-img" alt="Category">
                                </td>

                                <td>
                                    <span class="cat-name">{{$value->name}}</span>
                                    @if ($value->front_view == 1)
                                        <span class="badge badge-pill badge-soft-primary ms-2" title="Visible on Homepage">
                                            <i class="fe-eye"></i> Front
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($value->status==1)
                                        <span class="badge badge-pill badge-soft-success">Active</span> 
                                    @else 
                                        <span class="badge badge-pill badge-soft-danger">Inactive</span> 
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        {{-- Status Toggle --}}
                                        @if($value->status == 1)
                                            <form method="post" action="{{route('categories.inactive')}}" class="d-inline"> 
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="action-btn btn-inactive" title="Deactivate">
                                                    <i class="fe-eye-off"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{route('categories.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="action-btn btn-active" title="Activate">
                                                    <i class="fe-eye"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit --}}
                                        <a href="{{route('categories.edit',$value->id)}}" class="action-btn btn-edit" title="Edit">
                                            <i class="fe-edit"></i>
                                        </a>

                                        {{-- Delete (শুধু category-delete পারমিশন থাকলে) --}}
                                        @can('category-delete')
                                        <form method="post" action="{{route('categories.destroy')}}" class="d-inline">        
                                            @csrf
                                            <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                            <button type="submit" class="action-btn btn-delete delete-confirm" title="Delete">
                                                <i class="fe-trash-2"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> </div> </div></div>
</div>
@endsection

@section('script')
<script src="{{asset('/public/backEnd/')}}/assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/pdfmake/build/vfs_fonts.js"></script>
@endsection
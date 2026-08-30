@extends('backEnd.layouts.master')
@section('title','Subcategory Manage')

@section('content')
<div class="container-fluid">
    
    <div class="page-title-box d-flex align-items-center justify-content-between py-3">
        <div>
            <h4 class="page-title mb-1 text-dark fw-bold">Subcategories</h4>
            <p class="text-muted font-size-13 mb-0">Organize your category tree and keep product browsing structured.</p>
        </div>
        <div class="page-title-right">
            <a href="{{route('subcategories.create')}}" class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="fe-plus me-1"></i> Add Subcategory
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
                                <th>Parent Category</th>
                                <th>Subcategory Name</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 150px;">Action</th>
                            </tr>
                        </thead>                
                        <tbody>
                            @foreach($data as $key=>$value)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                
                                <td>
                                    @if($value->category)
                                        <span class="badge badge-pill badge-soft-info parent-cat">
                                            <i class="fe-folder me-1"></i> {{$value->category->name}}
                                        </span>
                                    @else
                                        <span class="badge badge-pill badge-soft-secondary">Deleted</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="subcat-name">{{$value->subcategoryName}}</span>
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
                                            <form method="post" action="{{route('subcategories.inactive')}}" class="d-inline"> 
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="action-btn btn-inactive" title="Deactivate">
                                                    <i class="fe-eye-off"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{route('subcategories.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                                <button type="submit" class="action-btn btn-active" title="Activate">
                                                    <i class="fe-eye"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit --}}
                                        <a href="{{route('subcategories.edit',$value->id)}}" class="action-btn btn-edit" title="Edit">
                                            <i class="fe-edit"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <form method="post" action="{{ route('subcategories.destroy') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $value->id }}">
                                            <button type="submit" class="action-btn btn-delete delete-confirm" title="Delete">
                                                <i class="fe-trash-2"></i>
                                            </button>
                                        </form>
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
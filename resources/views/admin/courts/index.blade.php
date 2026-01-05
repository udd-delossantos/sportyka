@extends('layouts.admin.app')

@section('title', 'Sporty Ka | Courts')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Courts</h1>
        <a href="{{ route('admin.courts.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Add New Court
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Facility Courts</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="courtsTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Court Name</th>
                            <th>Sport Type</th>
                            <th>Hourly Rate</th>
                            <th>Status</th>
                            <th>Photos</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courts as $court)
                        <tr>
                            <td class="font-weight-bold text-dark">
                                {{ $court->name }}
                                <div class="small text-gray-500 font-weight-normal">
                                    {{ Str::limit($court->description, 40) }}
                                </div>
                            </td>
                            <td>{{ $court->sport }}</td>
                            <td class="text-success font-weight-bold">
                                ₱{{ number_format($court->hourly_rate, 2) }}
                            </td>
                            <td class="text-center">
                                @if(strtolower($court->status) == 'available')
                                    <span class="badge badge-success p-2">
                                         Available
                                    </span>
                                @else
                                    <span class="badge badge-warning shadow-sm px-2 py-1">
                                         In-use
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($court->images && is_array($court->images))
                                    <div class="d-flex align-items-center">
                                        @foreach(array_slice($court->images, 0, 2) as $img)
                                            <img src="{{ asset('storage/'.$img) }}" 
                                                 class="img-thumbnail mr-1 shadow-sm" 
                                                 style="width:45px; height:45px; object-fit:cover; border-radius: 4px;">
                                        @endforeach
                                        @if(count($court->images) > 2)
                                            <span class="badge badge-secondary ml-1">+{{ count($court->images) - 2 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">No images</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle btn btn-light btn-sm shadow-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                        <div class="dropdown-header">Manage Court:</div>
                                        <a class="dropdown-item" href="{{ route('admin.courts.edit', $court) }}">
                                            <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>Edit Details
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('admin.courts.destroy', $court) }}" method="POST" 
                                              onsubmit="return confirm('Delete this court? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger">
                                                <i class="fas fa-trash fa-sm fa-fw mr-2 text-danger"></i>Delete Court
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Aligning with SB Admin 2's specific color palette */
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }
    .img-thumbnail {
        border: 1px solid #e3e6f0;
    }
    .badge {
        font-weight: 500;
        text-transform: uppercase;
        font-size: 75%;
    }
</style>
@endsection
@push('scripts')
<script>
$(document).ready(function() {
    // Initializing DataTable with your specific requirements
    var table = $('#courtsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
    });
});
</script>

@endpush
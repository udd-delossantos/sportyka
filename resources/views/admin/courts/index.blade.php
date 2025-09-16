@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-secondary">Courts Management</h4>
            <a href="{{ route('admin.courts.create') }}" class="btn btn-primary">
                <i class="fas fa-volleyball"></i> Add Court</a>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <div class="row">
                    
                    
                </div>
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Court Name</th>
                            <th>Sport Type</th>
                            <th>Rate</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courts as $court)
                        <tr>
                            <td>{{ $court->id }}</td>
                            <td>{{ $court->name }}</td>
                            <td>{{ $court->sport }}</td>
                            <td>₱{{ number_format($court->hourly_rate, 2) }}</td>
                            <td>{{ ucfirst($court->status) }}</td>
                            <td class="text-center" style="white-space: nowrap;">
                                <a href="{{ route('admin.courts.edit', $court) }}" class="btn btn-sm btn-warning">
                                Edit</a>
                                <form action="{{ route('admin.courts.destroy', $court) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this court?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                 Delete
                                </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

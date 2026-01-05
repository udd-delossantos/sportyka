@extends('layouts.admin.app')

@section('title', 'Sporty Ka | Users')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Users</h1>
        <a href="{{ route('admin.users.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-user-plus fa-sm text-white-50 mr-2"></i> Add New User
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">System Users Database</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="usersTable" width="100%" cellspacing="0">
                    <thead class="thead-light text-gray-800">
                        <tr>
                            <th>Profile</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Role</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                         @forelse($users as $user)
                            <tr>
                                <td style="width: 50px;">
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white font-weight-bold shadow-sm" style="width: 35px; height: 35px; font-size: 12px;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                </td>
                                <td class="font-weight-bold text-dark">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="text-center">
                                    @if(strtolower($user->role) == 'admin')
                                        <span class="badge badge-primary p-2 ">Admin</span>
                                    @elseif(strtolower($user->role) == 'staff')
                                        <span class="badge badge-danger p-2 ">Staff</span>
                                    @else
                                        <span class="badge badge-info p-2 ">Customer</span>
                                    @endif
                             
                                </td>
                                <td class="text-center">
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle btn btn-light btn-sm shadow-sm" href="#" role="button" id="userAction{{ $user->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="userAction{{ $user->id }}">
                                            <div class="dropdown-header">Manage User:</div>
                                            <a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}">
                                                <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i> Edit Profile
                                            </a>
                                            
                                            @if($user->id !== auth()->id())
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete this user?')">
                                                        <i class="fas fa-trash-alt fa-sm fa-fw mr-2 text-danger"></i> Delete User
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-gray-500 py-4">No registered users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }
    .badge {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
   var table = $('#usersTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
    });
});
</script>
@endpush
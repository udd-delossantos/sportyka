@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">Manage GCash QR Codes</h5>
            <p>Manage QR Codes</p>
        </div>
        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.gcash_qr_codes.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <input type="file" name="gcash_qr" class="form-control" accept="image/*" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Upload New QR</button>
                    </div>
                </div>
            </form>

            <div class="row">
                @foreach($qrs as $qr)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img src="{{ asset($qr->file_path) }}" alt="QR" class="img-fluid mb-2" style="max-width: 250px;">
                                @if($qr->is_active)
                                    <span class="badge bg-success mb-2">Active</span>
                                @endif
                            </div>
                            <div class="card-footer text-center">
                                @if(!$qr->is_active)
                                    <form action="{{ route('admin.gcash_qr_codes.setActive', $qr->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Set Active</button>
                                    </form>
                                    <form action="{{ route('admin.gcash_qr_codes.destroy', $qr->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
@endsection

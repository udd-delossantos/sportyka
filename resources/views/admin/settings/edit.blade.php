@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0 text-primary">Update GCash QR Code</h5>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="gcash_qr" class="form-label">Upload New QR Code</label>
                    <input type="file" name="gcash_qr" class="form-control" accept="image/*">
                </div>

                @if($gcashQr)
                    <div class="mb-3 text-center">
                        <p>Current QR Code:</p>
                        <img src="{{ asset($gcashQr) }}" alt="GCash QR" class="img-fluid" style="max-width:300px;">
                    </div>
                @endif

                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payment QR Management</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                 QR Code Library
            </h6>
        </div>

        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                 {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="mb-5 p-4 bg-gray-100 rounded border">
                <h6 class="text-xs font-weight-bold text-uppercase text-gray-600 mb-3">Add New QR Code</h6>
                <form action="{{ route('admin.gcash_qr_codes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-md-9 mb-2 mb-md-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fas fa-image text-gray-500"></i></span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" name="gcash_qr" class="custom-file-input" id="qrUpload" accept="image/*" required>
                                    <label class="custom-file-label" for="qrUpload">Choose QR image...</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                <i class="fas fa-upload fa-sm text-white-50 mr-1"></i> Upload
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row">
                @foreach($qrs as $qr)
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card shadow h-100 {{ $qr->is_active ? 'border-bottom-success' : 'border-bottom-primary' }}">
                            
                            @if($qr->is_active)
                                <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                    <span class="badge badge-success shadow-sm px-2 py-1">
                                        <i class="fas fa-check mr-1"></i> ACTIVE
                                    </span>
                                </div>
                            @endif

                            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-4">
                                <div class="p-2 border rounded bg-white mb-3">
                                    <img src="{{ asset($qr->file_path) }}" alt="QR Code" class="img-fluid" style="max-height: 300px; max-width: 100%;">
                                </div>
                                <p class="text-xs text-muted mb-0">
                                    Uploaded: {{ $qr->created_at ? $qr->created_at->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>

                            @if(!$qr->is_active)
                                <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                                    <div class="row no-gutters">
                                        <div class="col-6 pr-1">
                                            <form action="{{ route('admin.gcash_qr_codes.setActive', $qr->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm btn-block" title="Set as Primary">
                                                    <i class="fas fa-check"></i> Set Active
                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-6 pl-1">
                                            <form action="{{ route('admin.gcash_qr_codes.destroy', $qr->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this QR code?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm btn-block" title="Delete QR">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card-footer bg-success text-white text-center py-2">
                                    <small class="font-weight-bold">Currently in Use</small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($qrs->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-qrcode fa-3x text-gray-300"></i>
                    </div>
                    <p class="text-gray-500 mb-0">No QR codes uploaded yet.</p>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Simple script to update the file label when a file is chosen
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush
{{-- staff/dashboard.blade.php --}}
@extends('layouts.staff.app')

@section('title', 'Staff Dashboard')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Welcome,  Staff!</h1>
        <a href="#" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
        </a>
    </div>
@endsection

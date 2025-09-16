@extends('layouts.staff.app') @section('content')
<div class="container">
    <form method="POST" action="{{ route('staff.game_sessions.store') }}">
        <div class="card shadow mb4">
            <div class="card-header pb-0">
                <h5><strong>Create Session</strong></h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif @csrf

                <div class="mb-3">
                    <label for="court_id">Court</label>
                    <select name="court_id" class="form-control" required>
                        @foreach($courts as $court)
                            @if($court->status === 'available')
                                <option value="{{ $court->id }}">
                                    {{ $court->name }} (₱{{ $court->hourly_rate }}/hr)
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

            

                <div class="mb-3">
                    <label for="customer_name">Customer Name</label>
                    <input type="text" name="customer_name" class="form-control" required />
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="hours">Hours</label>
                        <select name="hours" class="form-control" required>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="minutes">Minutes</label>
                        <select name="minutes" class="form-control" required>
                            <option value="0">0</option>
                            <option value="30">30</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-sm">Create Session</button>
                <a href="{{ route('staff.game_sessions.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
        </div>
    </form>
</div>
@endsection
@csrf
<div class="card shadow mb-4">
  <div class="card-header pb-0">
    <h5><strong>Court Information</strong></h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col md-6">
        <div class="mb-3">
          <label for="name" class="form-label">Court Name</label>
          <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $court->name ?? '') }}" required>
        </div>
      </div>
      <div class="col md-6">
        <div class="mb-3">
          <label for="sport" class="form-label">Sport Type</label>
          <input type="text" class="form-control" id="sport" name="sport" value="{{ old('name', $court->sport ?? '') }}" required>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col md-6">
        <div class="mb-3">
          <label for="hourly_rate" class="form-label">Hourly Rate</label>
          <input type="number" step="0.01" class="form-control" name="hourly_rate" value="{{ old('hourly_rate', $court->hourly_rate ?? '') }}" required>
        </div>
      </div>
      <div class="col md-6">
        <div class="mb-3">
          <label for="status" class="form-label">Status</label>
          <select class="form-control" name="status" required>
            @foreach (['available', 'in-use'] as $status)
              <option value="{{ $status }}" @selected(old('status', $court->status ?? '') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>    
  </div>

  <div class="card-footer">
    <button type="submit" class="btn btn-success btn-sm">Save</button>
    <a href="{{ route('admin.courts.index') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

</div>




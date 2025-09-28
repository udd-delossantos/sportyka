@csrf
<div class="card shadow mb-4">
  <div class="card-header pb-0">
    <h5><strong>Court Information</strong></h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label for="name" class="form-label">Court Name</label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $court->name ?? '') }}" required>
        </div>
      </div>  
      <div class="col-md-6">  
        <div class="mb-3">
          <label for="sport" class="form-label">Sport</label>
          <input type="text" name="sport" class="form-control" value="{{ old('sport', $court->sport ?? '') }}" required>
        </div>
      </div>  
    </div>  

    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label for="hourly_rate" class="form-label">Hourly Rate</label>
          <input type="number" step="0.01" name="hourly_rate" class="form-control" 
                  value="{{ old('hourly_rate', $court->hourly_rate ?? '') }}" required>
        </div>
      </div>  

      <div class="col-md-6">
        <div class="mb-3">
          <label for="status" class="form-label">Status</label>
          <select name="status" class="form-control" required>
              <option value="available" {{ old('status', $court->status ?? '') == 'available' ? 'selected' : '' }}>Available</option>
              <option value="in-use" {{ old('status', $court->status ?? '') == 'in-use' ? 'selected' : '' }}>In Use</option>
          </select>
        </div>
      </div>
    </div>   

    {{-- Full width description --}}
    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea name="description" rows="4" class="form-control">{{ old('description', $court->description ?? '') }}</textarea>
    </div>

    {{-- Full width images --}}
    <div class="mb-3">
      <label for="images" class="form-label">Court Images</label>
      <input type="file" name="images[]" class="form-control" multiple>
      <small class="text-muted">You can select multiple images (JPG, PNG, Max: 2MB each)</small>
    </div>

    @if(isset($court) && !empty($court->images))
      <div class="mb-3">
          <label class="form-label">Existing Images</label>
          <div class="d-flex flex-wrap gap-2">
              @foreach($court->images as $image)
                  <img src="{{ asset('storage/'.$image) }}" alt="Court Image" 
                       class="img-thumbnail" style="width: 150px; height: 120px; object-fit: cover;">
              @endforeach
          </div>
      </div>
    @endif
  </div>
  <div class="card-footer">
  <button type="submit" class="btn btn-success btn-sm">Save</button>
  <a href="{{ route('admin.courts.index') }}" class="btn btn-secondary btn-sm">Back</a>
</div>
</div>



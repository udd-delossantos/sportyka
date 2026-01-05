@csrf
<div class="card shadow mb-4 border-left-primary">
    
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Court Details</h6>
    </div>

    <div class="card-body">
        

        
      
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700" for="name">
                            Court Name
                        </label>
                        <input type="text" name="name" id="name" 
                               class="form-control form-control-solid" 
                               placeholder="Enter court name" 
                               value="{{ old('name', $court->name ?? '') }}" required>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700" for="sport">
                            Sport Category
                        </label>
                        <input type="text" name="sport" id="sport" 
                               class="form-control form-control-solid" 
                               placeholder="e.g. Basketball, Badminton" 
                               value="{{ old('sport', $court->sport ?? '') }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700" for="hourly_rate">
                            Hourly Rate (PHP)
                        </label>
                        <input type="number" step="0.01" name="hourly_rate" id="hourly_rate" 
                               class="form-control form-control-solid" 
                               placeholder="0.00" 
                               value="{{ old('hourly_rate', $court->hourly_rate ?? '') }}" required>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700" for="status">
                            Operational Status
                        </label>
                        <select name="status" id="status" class="form-control form-control-solid" required>
                            <option value="available" {{ old('status', $court->status ?? '') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="in-use" {{ old('status', $court->status ?? '') == 'in-use' ? 'selected' : '' }}>In Use / Maintenance</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-gray-7001" for="description">
                    Description & Amenities
                </label>
                <textarea name="description" id="description" rows="4" 
                          class="form-control form-control-solid"
                          placeholder="Describe the court surface, lighting, or rules...">{{ old('description', $court->description ?? '') }}</textarea>
            </div>
     

        <hr class="sidebar-divider my-4">

    

       
            <div class="form-group">
                <label class="font-weight-bold text-gray-700">Upload New Images</label>
                <div class="custom-file">
                    <input type="file" name="images[]" class="custom-file-input" id="courtImages" multiple>
                    <label class="custom-file-label" for="courtImages">Choose files...</label>
                </div>
                <div class="small mt-font-weight-bold text-gray-700-exclamation-triangle"></i> Supported formats: JPG, PNG. Max size: 2MB.
                </div>
            </div>

            @if(isset($court) && !empty($court->images))
                <div class="card bg-gray-100 border-0 mt-3">
                    <div class="card-body">
                        <div class="row">
                            @foreach($court->images as $index => $image)
                                <div class="col-xl-3 col-md-4 col-6 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div style="height: 120px; overflow: hidden;" class="rounded-top">
                                            <img src="{{ asset('storage/'.$image) }}" 
                                                 class="w-100 h-100" 
                                                 style="object-fit: cover;" 
                                                 alt="Court Preview">
                                        </div>
                                        <div class="card-footer bg-white border-top-0 p-2 text-center">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" 
                                                       name="delete_images[]" 
                                                       value="{{ $image }}" 
                                                       id="delSwitch{{ $index }}">
                                                <label class="custom-control-label font-weight-bold text-danger" for="delSwitch{{ $index }}">
                                                    Delete
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
       

    </div>

    <div class="card-footer py-3 d-flex justify-content-end bg-gray-100">
        <a href="{{ route('admin.courts.index') }}" class="btn btn-secondary btn mr-2">
            <span class="icon text-white-50">
                <i class="fas fa-times"></i>
            </span>
            <span class="text">Cancel</span>
        </a>
        <button type="submit" class="btn btn-primary btn">
            <span class="icon text-white-50">
                <i class="fas fa-save"></i>
            </span>
            <span class="text">Save Changes</span>
        </button>
    </div>

</div>
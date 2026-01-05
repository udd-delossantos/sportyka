@csrf

<div class="card shadow mb-4 border-left-primary">

    <!-- Header -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            User Details
        </h6>
    </div>

    <!-- Body -->
    <div class="card-body">

        <div class="row">
            <!-- Name -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="font-weight-bold text-gray-700" for="name">Full Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        class="form-control form-control-solid"
                        placeholder="Enter full name"
                        value="{{ old('name', $user->name ?? '') }}"
                        required
                    >
                </div>
            </div>

            <!-- Email -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="font-weight-bold text-gray-700" for="email">Email Address</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        class="form-control form-control-solid"
                        placeholder="user@example.com"
                        value="{{ old('email', $user->email ?? '') }}"
                        required
                    >
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Role -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="font-weight-bold text-gray-700" for="role">User Role</label>
                    <select name="role" id="role" class="form-control form-control-solid" required>
                        <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ old('role', $user->role ?? '') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="customer" {{ old('role', $user->role ?? '') == 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                </div>
            </div>

            <!-- Password Fields -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="font-weight-bold text-gray-700" for="password">
                        {{ isset($user) ? 'New Password (optional)' : 'Password' }}
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="form-control form-control-solid"
                        placeholder="{{ isset($user) ? 'Leave blank to keep current password' : 'Enter password' }}"
                        {{ isset($user) ? '' : 'required' }}
                    >
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <div class="card-footer py-3 d-flex justify-content-end bg-gray-100">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mr-2">
            <i class="fas fa-times text-white-50 mr-1"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save text-white-50 mr-1"></i> Save Changes
        </button>
    </div>

</div>

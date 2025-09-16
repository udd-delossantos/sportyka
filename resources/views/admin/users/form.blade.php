
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h3 class="m-0 font-weight-bold text-secondary">User Information</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="admin" {{ (old('role', $user->role ?? '') == 'admin') ? 'selected' : '' }}>Admin</option>
                    <option value="staff" {{ (old('role', $user->role ?? '') == 'staff') ? 'selected' : '' }}>Staff</option>
                    <option value="customer" {{ (old('role', $user->role ?? '') == 'customer') ? 'selected' : '' }}>Customer</option>
                </select>
            </div>

            @if(!isset($user))
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            @endif

            @if(isset($user))
            <div class="mb-3">
                <label>New Password (leave blank if unchanged)</label>
                <input type="password" name="password" class="form-control">
            </div>
            @endif
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>    










<form id="{{ $id ?? 'addStaffForm' }}" action="{{ $action }}" method="POST" class="space-y-3" aria-label="{{ $staff_id ? 'Edit Staff Form' : 'Add Staff Form' }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif
    @if ($staff_id)
        <input type="hidden" name="id" id="editId" value="{{ $staff_id }}">
    @endif
    <div>
        <label for="{{ $staff_id ? 'editName' : 'name' }}" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Name *</label>
        <input type="text" name="name" id="{{ $staff_id ? 'editName' : 'name' }}" value="{{ old('name', $staff_id ? $staff->name : '') }}" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-1 focus:ring-[#00553d] text-xs" aria-label="Staff Name">
    </div>
    <div>
        <label for="{{ $staff_id ? 'editDepartment' : 'department' }}" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Department *</label>
        <select name="department" id="{{ $staff_id ? 'editDepartment' : 'department' }}" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-1 focus:ring-[#00553d] text-xs" aria-label="Department">
            <option value="">Select Department</option>
            @foreach ($departments as $department)
                <option value="{{ $department->name }}" {{ old('department', $staff_id ? $staff->department : '') == $department->name ? 'selected' : '' }}>{{ $department->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="{{ $staff_id ? 'editEmail' : 'email' }}" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Email *</label>
        <input type="email" name="email" id="{{ $staff_id ? 'editEmail' : 'email' }}" value="{{ old('email', $staff_id ? $staff->email : '') }}" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-1 focus:ring-[#00553d] text-xs" aria-label="Email">
    </div>
    <div>
        <label for="{{ $staff_id ? 'editStatus' : 'status' }}" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Status *</label>
        <select name="status" id="{{ $staff_id ? 'editStatus' : 'status' }}" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-1 focus:ring-[#00553d] text-xs" aria-label="Status">
            <option value="">Select Status</option>
            <option value="Active" {{ old('status', $staff_id ? $staff->status : '') == 'Active' ? 'selected' : '' }}>Active</option>
            <option value="Resigned" {{ old('status', $staff_id ? $staff->status : '') == 'Resigned' ? 'selected' : '' }}>Resigned</option>
        </select>
    </div>
    <button type="submit" class="w-full gradient-btn text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#ffcc34] text-xs" aria-label="{{ $staff_id ? 'Update Staff' : 'Add Staff' }}">
        {{ $staff_id ? 'Update Staff' : 'Add Staff' }}
    </button>
</form>
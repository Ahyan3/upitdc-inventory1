<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-[#00553d] leading-tight">
            {{ __('Staff Management') }}
        </h2>
    </x-slot>

    <div class="flex min-h-screen bg-gray-50">
        <button id="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 bg-[#90143c] text-white p-2 rounded-lg border border-[#ffcc34]">
            <i class="fas fa-bars text-sm"></i>
        </button>

        <!-- Main Content -->
        <div class="flex-1 container mx-auto px-4 py-8">
            <div class="text-center mb-10 animate-fade-in">
                <h2 class="text-lg font-bold text-[#90143c]">Staff Management</h2>
                <p class="text-[0.65rem] text-[#00553d]">Manage staff members</p>
            </div>

            <!-- Buttons for Add Staff and Filter -->
            <div class="flex justify-between mb-8">
                <button id="openAddStaffModal" class="bg-[#00553d] hover:bg-[#003d2b] text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 flex items-center border border-[#ffcc34] text-sm" aria-label="Add New Staff">
                    <i class="fas fa-plus mr-1 text-xs"></i> Add Staff
                </button>
                <button id="openFilterModal" class="bg-[#90143c] hover:bg-[#6b102d] text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 flex items-center border border-[#ffcc34] text-sm" aria-label="Filter Staff">
                    <i class="fas fa-filter mr-1 text-xs"></i> Filter
                </button>
            </div>

            <!-- Add Staff Modal -->
            <div id="addStaffModal" class="fixed inset-0 bg-[#00553d] bg-opacity-50 hidden flex items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4 border border-[#ffcc34]">
                    <div class="bg-[#90143c] px-5 py-3 rounded-t-xl">
                        <h2 class="text-xs font-semibold text-white">Add Staff</h2>
                    </div>
                    <div class="p-5">
                        <form id="addStaffForm" action="{{ route('staff.store') }}" method="POST" class="space-y-3" aria-label="Add Staff Form">
                            @csrf
                            <div>
                                <label for="name" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Name *</label>
                                <input type="text" name="name" id="name" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm" aria-label="Staff Name">
                            </div>
                            <div>
                                <label for="department" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Department *</label>
                                <select name="department" id="department" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm" aria-label="Department">
                                    <option value="">Select Department</option>
                                    @if(isset($departments) && $departments->count() > 0)
                                    @foreach($departments as $department)
                                    <option value="{{ $department->name }}">{{ $department->name }}</option>
                                    @endforeach
                                    @else
                                    <option value="ITSG">ITSG</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Content Development">Content Development</option>
                                    <option value="Software Development">Software Development</option>
                                    <option value="Helpdesk">Helpdesk</option>
                                    <option value="Other">Other</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label for="email" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Email *</label>
                                <input type="email" name="email" id="email" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm" aria-label="Email">
                            </div>
                            <div>
                                <label for="status" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Status *</label>
                                <select name="status" id="status" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm" aria-label="Status">
                                    <option value="">Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Resigned">Resigned</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 bg-[#00553d] hover:bg-[#003d2b] text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#ffcc34] text-sm" aria-label="Add Staff">
                                    Add Staff
                                </button>
                                <button type="button" onclick="closeAddStaffModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#ffcc34] text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Filter Modal -->
            <div id="filterModal" class="fixed inset-0 bg-[#00553d] bg-opacity-50 hidden flex items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4 border border-[#ffcc34]">
                    <div class="bg-[#90143c] px-5 py-3 rounded-t-xl">
                        <h2 class="text-xs font-semibold text-white">Filter Staff</h2>
                    </div>
                    <div class="p-5">
                        <form id="filterForm" class="space-y-3" aria-label="Filter Staff Form">
                            <div>
                                <label for="filterDepartment" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Department</label>
                                <select name="filterDepartment" id="filterDepartment" class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm" aria-label="Filter Department">
                                    <option value="">All Departments</option>
                                    @if(isset($departments) && $departments->count() > 0)
                                    @foreach($departments as $department)
                                    <option value="{{ $department->name }}">{{ $department->name }}</option>
                                    @endforeach
                                    @else
                                    <option value="Other">Other</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label for="filterStatus" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Status</label>
                                <select name="filterStatus" id="filterStatus" class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm" aria-label="Filter Status">
                                    <option value="">All Statuses</option>
                                    <option value="Active">Active</option>
                                    <option value="Resigned">Resigned</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" onclick="applyFilter()" class="flex-1 bg-[#00553d] hover:bg-[#003d2b] text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#ffcc34] text-sm">
                                    Apply Filter
                                </button>
                                <button type="button" onclick="closeFilterModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#ffcc34] text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Staff Listing -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in border border-[#ffcc34]">
                <div class="bg-[#00553d] px-5 py-3">
                    <h2 class="text-xs font-semibold text-white">Staff Members</h2>
                </div>
                <div class="p-5">
                    <div class="flex justify-between items-center mb-4">
                        <div class="relative w-64">
                            <input type="text" id="searchInput" placeholder="Search staff..." class="w-full text-xs pl-8 pr-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d]" aria-label="Search staff">
                            <div class="absolute left-2 top-2 text-[#00553d]">
                                <i class="fas fa-search text-xs"></i>
                            </div>
                        </div>
                        <button id="exportBtn" class="bg-[#ffcc34] text-xs hover:bg-[#e6b82f] text-[#00553d] font-medium py-1.5 px-3 rounded-lg transition duration-200 flex items-center border border-[#90143c]" aria-label="Export to CSV">
                            <i class="fas fa-class-file-export mr-1 text-xs"></i> Export CSV
                        </button>
                    </div>

                    @if ($staff->isEmpty())
                    <div class="text-center py-6">
                        <p class="text-base text-[#00553d]">No Current Record</p>
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#ffcc34]" aria-label="Staff Records">
                            <thead class="bg-[#ffcc34]">
                                <tr>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="staffTableBody" class="bg-white divide-y divide-[#ffcc34]">
                                @foreach ($staff as $member)
                                <tr>
                                    <td class="px-5 py-3 whitespace-nowrap text-sm">{{ $member->name }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-sm">{{ $member->department }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-sm">{{ $member->email }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <span class="{{ $member->status === 'Active' ? 'text-[#00553d]' : 'text-[#90143c]' }} text-sm">
                                            {{ $member->status }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <button class="text-[#00553d] hover:text-[#003d2b] mr-2 text-sm" data-id="{{ $member->id }}" data-name="{{ $member->name }}" onclick="viewHistoryLogs(this)" aria-label="View logs for {{ $member->name }}">View</button>
                                        <button class="text-[#90143c] hover:text-[#6b102d] mr-2 text-sm" data-id="{{ $member->id }}" onclick="editStaff(this)" aria-label="Edit staff {{ $member->name }}">Edit</button>
                                        <form action="{{ route('staff.destroy', $member->id) }}" method="POST" class="inline delete-staff-form" data-name="{{ $member->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[#90143c] hover:text-[#6b102d] text-sm" aria-label="Delete staff {{ $member->name }}">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div id="editModal" class="fixed inset-0 bg-[#00553d] bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4 border border-[#ffcc34]">
            <div class="bg-[#90143c] px-5 py-3 rounded-t-xl">
                <h2 class="text-base font-semibold text-white">Edit Staff</h2>
            </div>
            <div class="p-5">
                <form id="editStaffForm" method="POST" action="" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editId">
                    <div>
                        <label for="editName" class="block text-xs font-medium text-[#00553d] mb-1">Name *</label>
                        <input type="text" name="name" id="editName" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                    </div>
                    <div>
                        <label for="editDepartment" class="block text-xs font-medium text-[#00553d] mb-1">Department *</label>
                        <select name="department" id="editDepartment" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                            <option value="">Select Department</option>
                            @if(isset($departments) && $departments->count() > 0)
                            @foreach($departments as $department)
                            <option value="{{ $department->name }}">{{ $department->name }}</option>
                            @endforeach
                            @else
                            <option value="Other">Other</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label for="editEmail" class="block text-xs font-medium text-[#00553d] mb-1">Email *</label>
                        <input type="email" name="email" id="editEmail" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                    </div>
                    <div>
                        <label for="editStatus" class="block text-xs font-medium text-[#00553d] mb-1">Status *</label>
                        <div class="flex gap-2 items-center">
                            <select name="status" id="editStatus" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                <option value="">Select Status</option>
                                <option value="Active">Active</option>
                                <option value="Resigned">Resigned</option>
                            </select>
                            <button type="button" id="toggleStatusBtn" onclick="toggleStatus()" class="bg-[#ffcc34] hover:bg-[#e6b82f] text-[#00553d] font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#90143c] text-sm" aria-label="Toggle Staff Status">
                                Toggle Status
                            </button>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-[#00553d] hover:bg-[#003d2b] text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#ffcc34] text-sm">
                            Update Staff
                        </button>
                        <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#ffcc34] text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- History Logs Modal -->
    <div id="historyLogsModal" class="fixed inset-0 bg-[#00553d] bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg max-w-6xl w-full mx-4 max-h-[80vh] flex flex-col border border-[#ffcc34]">
            <div class="bg-[#00553d] px-5 py-3 rounded-t-xl">
                <h2 class="text-base font-semibold text-white">History Logs</h2>
                <button onclick="closeHistoryLogsModal()" class="absolute top-3 right-3 text-white hover:text-gray-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-5 overflow-y-auto flex-1">
                <div id="historyLogsContent">
                    <!-- History logs will be loaded here -->
                </div>
            </div>
            <div class="px-5 py-3 bg-gray-50 rounded-b-xl border-t border-[#ffcc34]">
                <button onclick="closeHistoryLogsModal()" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#ffcc34] text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('staffTableBody');
        if (searchInput && tableBody) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = tableBody.querySelectorAll('tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Add Staff Modal
        function openAddStaffModal() {
            document.getElementById('addStaffModal').classList.remove('hidden');
            document.getElementById('addStaffModal').classList.add('flex');
            document.getElementById('addStaffForm').reset(); // Reset form fields
        }

        function closeAddStaffModal() {
            document.getElementById('addStaffModal').classList.add('hidden');
            document.getElementById('addStaffModal').classList.remove('flex');
        }

        document.getElementById('openAddStaffModal').addEventListener('click', openAddStaffModal);
        document.getElementById('addStaffModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddStaffModal();
            }
        });

        // Handle Add Staff Form Submission
        document.getElementById('addStaffForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            fetch(form.action, {
                    method: form.method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw response; // Throw response to handle non-2xx status codes
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message || 'Staff member added successfully!',
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => {
                            closeAddStaffModal();
                            window.location.reload();
                        });
                    }
                })
                .catch(async error => {
                    let errorMessage = 'An error occurred while adding the staff member.';
                    if (error.json) {
                        const errorData = await error.json();
                        if (errorData.status === 'error') {
                            if (errorData.errors) {
                                errorMessage = Object.values(errorData.errors).flat().join(' ');
                            } else {
                                errorMessage = errorData.message || errorMessage;
                            }
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                    });
                });
        });

        // Filter Modal
        function openFilterModal() {
            document.getElementById('filterModal').classList.remove('hidden');
            document.getElementById('filterModal').classList.add('flex');
        }

        function closeFilterModal() {
            document.getElementById('filterModal').classList.add('hidden');
            document.getElementById('filterModal').classList.remove('flex');
        }

        function applyFilter() {
            const department = document.getElementById('filterDepartment').value.toLowerCase();
            const status = document.getElementById('filterStatus').value.toLowerCase();
            const rows = tableBody.querySelectorAll('tr');

            rows.forEach(row => {
                const rowDepartment = row.cells[1].textContent.toLowerCase();
                const rowStatus = row.cells[3].textContent.toLowerCase();
                const departmentMatch = !department || rowDepartment === department;
                const statusMatch = !status || rowStatus === status;
                row.style.display = departmentMatch && statusMatch ? '' : 'none';
            });

            closeFilterModal();
        }

        document.getElementById('openFilterModal').addEventListener('click', openFilterModal);
        document.getElementById('filterModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFilterModal();
            }
        });

        // Update the editStaff function
        function editStaff(button) {
            const staffId = button.getAttribute('data-id');
            const row = button.closest('tr');
            const name = row.cells[0].textContent.trim();
            const department = row.cells[1].textContent.trim();
            const email = row.cells[2].textContent.trim();
            const status = row.cells[3].textContent.trim();

            // Set form values
            const form = document.getElementById('editStaffForm');
            form.action = `/staff/${staffId}`;
            document.getElementById('editId').value = staffId;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editStatus').value = status;

            // Handle department select
            const editDepartmentSelect = document.getElementById('editDepartment');
            editDepartmentSelect.value = department;

            // Update toggle button text
            updateToggleButtonText(status);

            // Show modal
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        // Update the form submission handler
        document.getElementById('editStaffForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            const staffId = form.querySelector('input[name="id"]').value;

            fetch(`/staff/${staffId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(Object.fromEntries(formData)),
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw err;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Staff updated successfully!',
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    let errorMessage = 'An error occurred while updating staff.';

                    if (error.errors) {
                        errorMessage = Object.values(error.errors).flat().join('\n');
                    } else if (error.message) {
                        errorMessage = error.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                    });
                });
        });

        function updateToggleButtonText(status) {
            const toggleBtn = document.getElementById('toggleStatusBtn');
            if (status === 'Active') {
                toggleBtn.textContent = 'Mark as Resigned';
                toggleBtn.classList.remove('bg-[#00553d]', 'hover:bg-[#003d2b]');
                toggleBtn.classList.add('bg-[#90143c]', 'hover:bg-[#6b102d]');
            } else {
                toggleBtn.textContent = 'Mark as Active';
                toggleBtn.classList.remove('bg-[#90143c]', 'hover:bg-[#6b102d]');
                toggleBtn.classList.add('bg-[#00553d]', 'hover:bg-[#003d2b]');
            }
        }

        function toggleStatus() {
            const statusSelect = document.getElementById('editStatus');
            const currentStatus = statusSelect.value;
            const newStatus = currentStatus === 'Active' ? 'Resigned' : 'Active';
            statusSelect.value = newStatus;
            updateToggleButtonText(newStatus);
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Handle Delete Staff Form Submission
        document.querySelectorAll('.delete-staff-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const staffName = this.getAttribute('data-name');

                Swal.fire({
                    title: `Are you sure you want to delete ${staffName}?`,
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#901c',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData(this);

                        fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value,
                                    'Accept': 'application/json',
                                },
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw response;
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted',
                                        text: data.message || `${staffName} has been deleted successfully!`,
                                        timer: 1500,
                                        showConfirmButton: false,
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                }
                            })
                            .catch(async error => {
                                let errorMessage = 'An error occurred while deleting the staff member.';
                                if (error.json) {
                                    const errorData = await error.json();
                                    if (errorData.status === 'error') {
                                        errorMessage = errorData.message || errorMessage;
                                    }
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMessage,
                                });
                            });
                    }
                });
            });
        });

        // Export CSV functionality
        document.getElementById('exportBtn').addEventListener('click', function() {
            const table = document.querySelector('table');
            if (!table) return;

            let csv = '';
            const rows = table.querySelectorAll('tr');

            rows.forEach(row => {
                const cols = row.querySelectorAll('td, th');
                const rowData = [];

                cols.forEach((col, index) => {
                    if (index < cols.length - 1) {
                        rowData.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
                    }
                });

                if (rowData.length > 0) {
                    csv += rowData.join(',') + '\n';
                }
            });

            const blob = new Blob([csv], {
                type: 'text/csv'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'staff_members.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        });

        // History Logs
        function viewHistoryLogs(button) {
            const staffId = button.getAttribute('data-id');
            const modal = document.getElementById('historyLogsModal');

            document.getElementById('historyLogsContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-xl text-[#00553d]"></i>
                    <p class="mt-2 text-[#00553d] text-sm">Loading history logs...</p>
                </div>
            `;

            fetch(`/staff/${staffId}/history-logs`, {
                    headers: {
                        'Accept': 'application+json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'error') {
                        throw new Error(data.message);
                    }

                    if (data.logs && data.logs.length > 0) {
                        renderHistoryLogs(data);
                    } else {
                        document.getElementById('historyLogsContent').innerHTML = `
                        <div class="text-center py-8">
                            <p class="text-sm text-[#00553d]">No history logs found for this staff member.</p>
                        </div>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('historyLogsContent').innerHTML = `
                    <div class="text-center py-8 text-[#90143c]">
                        <p class="text-sm">Error loading history logs: ${error.message}</p>
                        <p class="text-xs mt-2">Please check the console for more details.</p>
                    </div>
                `;
                });

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function renderHistoryLogs(data) {
            let html = `
                <div class="mb-4">
                    <h3 class="font-semibold text-base text-[#00553d]">Staff: ${data.staff_name}</h3>
                    <p class="text-xs text-[#00553d]">Total Logs: ${data.logs.length}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#ffcc34]">
                        <thead class="bg-[#ffcc34]">
                            <tr>
                                <th class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Date</th>
                                <th class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Action</th>
                                <th class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Model</th>
                                <th class="px-5 py-2 text-left text-[0.5rem] font-medium text-[#00553d] uppercase tracking-wider">Changes</th>
                                <th class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Description</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#ffcc34]">
            `;

            data.logs.forEach(log => {
                let changes = '-';
                try {
                    if (log.old_values || log.new_values) {
                        const oldValues = typeof log.old_values === 'string' ? JSON.parse(log.old_values) : log.old_values;
                        const newValues = typeof log.new_values === 'string' ? JSON.parse(log.new_values) : log.new_values;

                        if (oldValues && newValues) {
                            changes = Object.keys(newValues).map(key => {
                                const oldVal = oldValues[key] !== undefined ? oldValues[key] : 'null';
                                const newVal = newValues[key] !== undefined ? newValues[key] : 'null';
                                return `${key}: ${oldVal} → ${newVal}`;
                            }).join('<br>');
                        }
                    }
                } catch (e) {
                    changes = 'Changed (details unavailable)';
                    console.error('Error parsing changes:', e);
                }

                html += `
                    <tr>
                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">${log.action_date}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">${log.action}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">${log.model} (ID: ${log.model_id})</td>
                        <td class="px-5 py-3 text-xs text-[#00553d] changes-cell">${changes}</td>
                        <td class="px-5 py-3 text-xs text-[#00553d]">${log.description || '-'}</td>
                    </tr>
                `;
            });

            html += `</tbody></table></div>`;
            document.getElementById('historyLogsContent').innerHTML = html;
        }

        function closeHistoryLogsModal() {
            const modal = document.getElementById('historyLogsModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('historyLogsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeHistoryLogsModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Function to update staff count
            function updateStaffCount() {
                fetch('/api/total-staff')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const staffCountElement = document.querySelector('[data-name="Total Staff"] .text-[#00553d]');
                        if (staffCountElement) {
                            staffCountElement.textContent = data.count;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching staff count:', error);
                    });
            }

            // Initial load
            updateStaffCount();

            // Optionally: Refresh the count periodically (every 30 seconds)
            setInterval(updateStaffCount, 30000);
        });
    </script>
    <x-auth-footer />
</x-app-layout>
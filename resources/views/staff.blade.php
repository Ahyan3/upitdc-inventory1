<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff Management') }}
        </h2>
    </x-slot>

    <div class="flex min-h-screen bg-gray-50">

        <button id="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 bg-gray-800 text-white p-2 rounded-lg">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Main Content -->
        <div class="flex-1 container mx-auto px-4 py-8">
            <div class="text-center mb-10 animate-fade-in">
                <h2 class="text-2xl font-bold text-red-600">Staff Management</h2>
                <p class="text-lg text-black-600">Manage staff members</p>
            </div>

            <!-- Add Staff Form -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden form-card mb-8">
                <div class="bg-red-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Add Staff</h2>
                </div>
                <div class="p-6">
                    <form id="addStaffForm" action="{{ route('staff.store') }}" method="POST" class="space-y-4" aria-label="Add Staff Form">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                            <input type="text" name="name" id="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Staff Name">
                        </div>
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                            <select name="department" id="department" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Department">
                                <option value="">Select Department</option>
                                @if(isset($departments) && $departments->count() > 0)
                                    @foreach($departments as $department)
                                        <option value="{{ $department->name }}">{{ $department->name }}</option>
                                    @endforeach
                                @else
                                    <!-- Fallback options if no departments are set -->
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
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" id="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Email">
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200" aria-label="Add Staff">
                            Add Staff
                        </button>
                    </form>
                </div>
            </div>

            <!-- Staff Listing -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in">
                <div class="bg-gray-800 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Staff Members</h2>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div class="relative w-64">
                            <input type="text" id="searchInput" placeholder="Search staff..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Search staff">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                        <button id="exportBtn" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center" aria-label="Export to CSV">
                            <i class="fas fa-file-export mr-2"></i> Export CSV
                        </button>
                    </div>

                    @if ($staff->isEmpty())
                        <div class="text-center py-6">
                            <p class="text-lg text-gray-600">No Current Record</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" aria-label="Staff Records">
                                <thead class="bg-green-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Department</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="staffTableBody" class="bg-white divide-y divide-gray-200">
                                    @foreach ($staff as $member)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $member->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $member->department }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $member->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <button class="text-blue-600 hover:text-blue-900 mr-3" data-id="{{ $member->id }}" onclick="editStaff(this)" aria-label="Edit staff {{ $member->name }}">Edit</button>
                                                <form action="{{ route('staff.destroy', $member->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete {{ $member->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" aria-label="Delete staff {{ $member->name }}">Delete</button>
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
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4">
            <div class="bg-blue-600 px-6 py-4 rounded-t-xl">
                <h2 class="text-xl font-semibold text-white">Edit Staff</h2>
            </div>
            <div class="p-6">
                <form id="editStaffForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="editName" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" name="name" id="editName" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="editDepartment" class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                        <select name="department" id="editDepartment" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Department</option>
                            @if(isset($departments) && $departments->count() > 0)
                                @foreach($departments as $department)
                                    <option value="{{ $department->name }}">{{ $department->name }}</option>
                                @endforeach
                            @else
                                <!-- Fallback options if no departments are set -->
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
                        <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" id="editEmail" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                            Update Staff
                        </button>
                        <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Search functionality - Fixed table body ID
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('staffTableBody');
        if (searchInput && tableBody) {
            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                const rows = tableBody.querySelectorAll('tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Edit Staff Function
        function editStaff(button) {
            const staffId = button.getAttribute('data-id');
            const row = button.closest('tr');
            const name = row.cells[0].textContent.trim();
            const department = row.cells[1].textContent.trim();
            const email = row.cells[2].textContent.trim();

            // Populate the edit form
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            
            // Get the add form department options and copy them to edit modal
            const addDepartmentSelect = document.getElementById('department');
            const editDepartmentSelect = document.getElementById('editDepartment');
            
            // Clear existing options except the first one (Select Department)
            while (editDepartmentSelect.children.length > 1) {
                editDepartmentSelect.removeChild(editDepartmentSelect.lastChild);
            }
            
            // Copy options from add form to edit modal
            for (let i = 1; i < addDepartmentSelect.children.length; i++) {
                const option = addDepartmentSelect.children[i].cloneNode(true);
                editDepartmentSelect.appendChild(option);
            }
            
            // Set the department dropdown value
            editDepartmentSelect.value = department;
            
            // If the department still doesn't exist in the dropdown, add it as an option
            if (editDepartmentSelect.value !== department) {
                const option = document.createElement('option');
                option.value = department;
                option.textContent = department;
                option.selected = true;
                editDepartmentSelect.appendChild(option);
            }

            // Set the form action URL
            document.getElementById('editStaffForm').action = `/staff/${staffId}`;

            // Show the modal
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        // Close Edit Modal
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
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
                    // Skip the actions column (last column)
                    if (index < cols.length - 1) {
                        rowData.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
                    }
                });
                
                if (rowData.length > 0) {
                    csv += rowData.join(',') + '\n';
                }
            });

            // Download CSV
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'staff_members.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        });
    </script>
</x-app-layout>
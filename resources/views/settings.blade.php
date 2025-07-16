<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="flex min-h-screen bg-gray-50">
        <button id="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 bg-gray-800 text-white p-2 rounded-lg">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Main Content -->
        <div class="flex-1 container mx-auto px-4 py-8">
            <div class="text-center mb-10 animate-fade-in">
                <h2 class="text-2xl font-bold text-red-600">Settings</h2>
                <p class="text-lg text-black-600">Configure system settings and manage departments</p>
            </div>

            <!-- System Settings Form -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden form-card mb-8">
                <div class="bg-red-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">System Settings</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('settings.update') }}" method="POST" class="space-y-4" aria-label="Settings Form">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="system_title" class="block text-sm font-medium text-gray-700 mb-1">System Title *</label>
                            <input type="text" name="system_title" id="system_title" value="{{ $settings['system_title'] ?? 'UPITDC - Inventory System' }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="System Title">
                            <p class="text-sm text-gray-500 mt-1">{{ $settingsDetails['system_title']->description ?? 'The title displayed in the application header.' }}</p>
                        </div>
                        <div>
                            <label for="default_return_period" class="block text-sm font-medium text-gray-700 mb-1">Default Return Period (days) *</label>
                            <input type="number" name="default_return_period" id="default_return_period" value="{{ $settings['default_return_period'] ?? 30 }}" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Default Return Period">
                            <p class="text-sm text-gray-500 mt-1">{{ $settingsDetails['default_return_period']->description ?? 'Default number of days for equipment return.' }}</p>
                        </div>
                        <div>
                            <label for="allow_duplicate_pr" class="block text-sm font-medium text-gray-700 mb-1">Allow Duplicate PR Numbers</label>
                            <input type="checkbox" name="allow_duplicate_pr" id="allow_duplicate_pr" value="1" {{ ($settings['allow_duplicate_pr'] ?? 0) == 1 ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <p class="text-sm text-gray-500 mt-1">{{ $settingsDetails['allow_duplicate_pr']->description ?? 'Allow duplicate PR numbers in equipment records.' }}</p>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200" aria-label="Save Settings">
                            Save Settings
                        </button>
                    </form>
                </div>
            </div>

            <!-- Department Management -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden form-card">
                <div class="bg-red-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Manage Departments</h2>
                </div>
                <div class="p-6">
                    <!-- Add Department Form -->
                    <form action="{{ route('settings.department.store') }}" method="POST" class="space-y-4 mb-8" aria-label="Add Department Form">
                        @csrf
                        <div>
                            <label for="department_name" class="block text-sm font-medium text-gray-700 mb-1">New Department Name *</label>
                            <input type="text" name="department_name" id="department_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Department Name">
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200" aria-label="Add Department">
                            Add Department
                        </button>
                    </form>

                    <!-- Departments List -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" aria-label="Departments List">
                            <thead class="bg-green-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Department Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if ($departments->isEmpty())
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 text-center text-gray-500">No Departments</td>
                                    </tr>
                                @else
                                    @foreach ($departments as $department)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $department->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <button type="button" class="text-blue-600 hover:text-blue-900 edit-department-btn" data-id="{{ $department->id }}" data-name="{{ $department->name }}">Edit</button>
                                                <form action="{{ route('settings.department.destroy', $department) }}" method="POST" class="inline delete-department-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="text-red-600 hover:text-red-900 delete-department-btn" data-name="{{ $department->name }}">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // SweetAlert2 for Delete Department
            const deleteButtons = document.querySelectorAll('.delete-department-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const form = this.closest('form');
                    const departmentName = this.getAttribute('data-name');

                    Swal.fire({
                        title: `Delete "${departmentName}"?`,
                        text: "This action cannot be undone! Equipment linked to this department will have no department assigned.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // SweetAlert2 for Edit Department
            const editButtons = document.querySelectorAll('.edit-department-btn');
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const departmentId = this.getAttribute('data-id');
                    const departmentName = this.getAttribute('data-name');

                    Swal.fire({
                        title: 'Edit Department',
                        input: 'text',
                        inputValue: departmentName,
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Save',
                        cancelButtonText: 'Cancel',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Department name is required!';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/settings/department/${departmentId}`;
                            form.innerHTML = `
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                <input type="hidden" name="_method" value="PATCH">
                                <input type="hidden" name="department_name" value="${result.value}">
                            `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
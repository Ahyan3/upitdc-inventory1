<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-[#00553d] leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="flex min-h-screen bg-gray-50">
        <button id="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 bg-[#90143c] text-white p-2 rounded-lg border border-[#ffcc34]">
            <i class="fas fa-bars text-sm"></i>
        </button>

        <!-- Main Content -->
        <div class="flex-1 container mx-auto px-4 py-8 w-full">
            <div class="text-center mb-10 animate-fade-in w-full">
                <h2 class="text-base font-bold text-[#90143c]">Settings</h2>
                <p class="text-[0.65rem] text-[#00553d]">Configure system settings and manage departments</p>
            </div>

            <!-- System Settings Form -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden form-card mb-8 w-full border border-[#ffcc34]">
                <div class="bg-[#90143c] px-5 py-3">
                    <h2 class="text-xs font-semibold text-white">System Settings</h2>
                </div>
                <div class="p-5">
                    <form id="settings-form" action="{{ route('settings.update') }}" method="POST" class="space-y-3" aria-label="Settings Form">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="system_title" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">System Title *</label>
                            <input type="text" name="system_title" id="system_title" value="{{ $settings['system_title'] ?? 'UPITDC - Inventory System' }}" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm" aria-label="System Title">
                            <p class="text-[0.65rem] text-[#00553d] mt-1">{{ $settingsDetails['system_title']->description ?? 'The title displayed in the application header.' }}</p>
                        </div>
                        <div>
                            <label for="default_return_period" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Default Return Period (days) *</label>
                            <input type="number" name="default_return_period" id="default_return_period" value="{{ $settings['default_return_period'] ?? 30 }}" min="1" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm" aria-label="Default Return Period">
                            <p class="text-[0.65rem] text-[#00553d] mt-1">{{ $settingsDetails['default_return_period']->description ?? 'Default number of days for equipment return.' }}</p>
                        </div>
                        <div>
                            <label for="allow_duplicate_pr" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Allow Duplicate PR Numbers</label>
                            <input type="checkbox" name="allow_duplicate_pr" id="allow_duplicate_pr" value="1" {{ ($settings['allow_duplicate_pr'] ?? 0) == 1 ? 'checked' : '' }} class="h-3 w-3 text-[#00553d] focus:ring-[#00553d] border-[#ffcc34] rounded">
                            <p class="text-[0.65rem] text-[#00553d] mt-1">{{ $settingsDetails['allow_duplicate_pr']->description ?? 'Allow duplicate PR numbers in equipment records.' }}</p>
                        </div>
                        <button type="submit" id="save-settings-btn" class="w-full bg-[#00553d] hover:bg-[#003d2b] text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#ffcc34] text-sm" aria-label="Save Settings">
                            Save Settings
                        </button>
                    </form>
                </div>
            </div>

            <!-- Department Management -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden form-card w-full border border-[#ffcc34]">
                <div class="bg-[#90143c] px-5 py-3">
                    <h2 class="text-xs font-semibold text-white">Manage Departments</h2>
                </div>
                <div class="p-5">
                    <!-- Add Department Form -->
                    <form id="department-form" action="{{ route('settings.department.store') }}" method="POST" class="space-y-3 mb-8" aria-label="Add Department Form">
                        @csrf
                        <div>
                            <label for="department_name" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">New Department Name *</label>
                            <input type="text" name="department_name" id="department_name" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm" aria-label="Department Name">
                        </div>
                        <button type="submit" id="add-department-btn" class="w-full bg-[#00553d] hover:bg-[#003d2b] text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#ffcc34] text-sm" aria-label="Add Department">
                            Add Department
                        </button>
                    </form>

                    <!-- Departments List -->
                    <div class="overflow-x-auto w-full">
                        <table class="min-w-full table-auto divide-y divide-[#ffcc34]" aria-label="Departments List">
                            <thead class="bg-[#ffcc34]">
                                <tr>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Department Name</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#ffcc34]">
                                @if ($departments->isEmpty())
                                <tr>
                                    <td colspan="2" class="px-5 py-3 text-center text-[#00553d] text-xs">No Departments</td>
                                </tr>
                                @else
                                @foreach ($departments as $department)
                                <tr>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $department->name }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs">
                                        <button type="button" class="text-[#00553d] hover:text-[#ffcc34] edit-department-btn" data-id="{{ $department->id }}" data-name="{{ $department->name }}">Edit</button>
                                        <form action="{{ route('settings.department.destroy', $department) }}" method="POST" class="inline delete-department-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-[#90143c] hover:text-[#6b102d] delete-department-btn" data-name="{{ $department->name }}">Delete</button>
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
            // Add CSRF token to meta tag if not present
            if (!document.querySelector('meta[name="csrf-token"]')) {
                const metaTag = document.createElement('meta');
                metaTag.name = 'csrf-token';
                metaTag.content = '{{ csrf_token() }}';
                document.head.appendChild(metaTag);
            }

            // SweetAlert2 for Save Settings
            const settingsForm = document.getElementById('settings-form');
            const saveSettingsBtn = document.getElementById('save-settings-btn');

            if (settingsForm) {
                settingsForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Save Settings?',
                        text: "Do you want to save these settings?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#00553d',
                        cancelButtonColor: '#90143c',
                        confirmButtonText: 'Yes, save it!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            title: 'text-xs',
                            content: 'text-[0.65rem]'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            saveSettingsBtn.disabled = true;
                            saveSettingsBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                            this.submit();
                        }
                    });
                });
            }

            // SweetAlert2 for Add Department
            const departmentForm = document.getElementById('department-form');
            const addDepartmentBtn = document.getElementById('add-department-btn');

            if (departmentForm) {
                departmentForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const departmentName = document.getElementById('department_name').value;

                    Swal.fire({
                        title: 'Add Department?',
                        text: `Do you want to add "${departmentName}" as a new department?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#00553d',
                        cancelButtonColor: '#90143c',
                        confirmButtonText: 'Yes, add it!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            title: 'text-xs',
                            content: 'text-[0.65rem]'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            addDepartmentBtn.disabled = true;
                            addDepartmentBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                            this.submit();
                        }
                    });
                });
            }

            // SweetAlert2 for Delete Department
            const deleteButtons = document.querySelectorAll('.delete-department-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    const departmentName = this.getAttribute('data-name');

                    Swal.fire({
                        title: `Delete "${departmentName}"?`,
                        text: "This action cannot be undone! Equipment linked to this department will have no department assigned.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#90143c',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            title: 'text-xs',
                            content: 'text-[0.65rem]'
                        }
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
                button.addEventListener('click', function() {
                    const departmentId = this.getAttribute('data-id');
                    const departmentName = this.getAttribute('data-name');

                    Swal.fire({
                        title: 'Edit Department',
                        input: 'text',
                        inputValue: departmentName,
                        showCancelButton: true,
                        confirmButtonColor: '#00553d',
                        cancelButtonColor: '#90143c',
                        confirmButtonText: 'Save',
                        cancelButtonText: 'Cancel',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Department name is required!';
                            }
                        },
                        customClass: {
                            title: 'text-xs',
                            content: 'text-[0.65rem]',
                            input: 'text-sm'
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

    <!-- Laravel Blade-controlled success alert -->
    @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#00553d',
                customClass: {
                    title: 'text-xs',
                    content: 'text-[0.65rem]'
                }
            });
        });
    </script>
    @endif

    <!-- Laravel Blade-controlled error alert -->
    @if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#90143c',
                customClass: {
                    title: 'text-xs',
                    content: 'text-[0.65rem]'
                }
            });
        });
    </script>
    @endif
    <x-auth-footer />
</x-app-layout>
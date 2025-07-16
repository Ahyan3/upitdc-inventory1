<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventory') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="text-center mb-10 animate-fade-in">
                <h2 class="text-2xl font-bold text-red-600">Inventory Management</h2>
                <p class="text-lg text-gray-600">Manage equipment issuance and returns</p>
            </div>

            <!-- Tabs -->
            <div class="mb-8">
                <ul class="flex border-b border-gray-200" role="tablist">
                    <li class="mr-1">
                        <button class="tab-button px-4 py-2 font-medium text-gray-700 bg-gray-100 rounded-t-lg" data-tab="issue" role="tab" aria-selected="true">Issue Equipment</button>
                    </li>
                    <li class="mr-1">
                        <button class="tab-button px-4 py-2 font-medium text-gray-700 hover:bg-gray-100 rounded-t-lg" data-tab="return" role="tab">Return Equipment</button>
                    </li>
                </ul>
            </div>

            <!-- Issue Equipment Tab -->
            <div id="issue-tab" class="tab-content bg-white rounded-xl shadow-md overflow-hidden form-card mb-8">
                <div class="bg-red-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Issue Equipment</h2>
                </div>
                <div class="p-6">
                    <form id="issueForm" action="{{ route('inventory.issue') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="staff_name" class="block text-sm font-medium text-gray-700 mb-1">Staff Name *</label>
                            <input type="text" name="staff_name" id="staff_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                            <select name="department_id" id="department_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                <option value="">Select Department</option>
                                @foreach (\App\Models\Department::all() as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="equipment_name" class="block text-sm font-medium text-gray-700 mb-1">Equipment Name *</label>
                            <input type="text" name="equipment_name" id="equipment_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="model_brand" class="block text-sm font-medium text-gray-700 mb-1">Model/Brand *</label>
                            <input type="text" name="model_brand" id="model_brand" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="date_issued" class="block text-sm font-medium text-gray-700 mb-1">Date Issued *</label>
                            <input type="date" name="date_issued" id="date_issued" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-1">Serial Number *</label>
                            <input type="text" name="serial_number" id="serial_number" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="pr_number" class="block text-sm font-medium text-gray-700 mb-1">PR Number *</label>
                            <input type="text" name="pr_number" id="pr_number" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                            <textarea name="remarks" id="remarks" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"></textarea>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" id="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                <option value="available">Available</option>
                                <option value="issued">Issued</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                            Issue Equipment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Return Equipment Tab -->
            <div id="return-tab" class="tab-content hidden bg-white rounded-xl shadow-md overflow-hidden form-card mb-8">
                <div class="bg-red-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Return Equipment</h2>
                </div>
                <div class="p-6">
                    @if ($issuances->whereNull('date_returned')->isEmpty())
                    <div class="text-center text-gray-500">No Current Record</div>
                    @else
                    @foreach ($issuances->whereNull('date_returned') as $issuance)
                    <form action="{{ route('inventory.return', $issuance) }}" method="POST" class="space-y-4 mb-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Equipment</label>
                                <input type="text" value="{{ $issuance->equipment->equipment_name ?? 'N/A' }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Staff</label>
                                <input type="text" value="{{ $issuance->staff->name ?? 'N/A' }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
                            </div>
                            <div>
                                <label for="date_returned_{{ $issuance->id }}" class="block text-sm font-medium text-gray-700 mb-1">Date Returned *</label>
                                <input type="date" name="date_returned" id="date_returned_{{ $issuance->id }}" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label for="remarks_{{ $issuance->id }}" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                                <textarea name="remarks" id="remarks_{{ $issuance->id }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">{{ $issuance->return_notes ?? '' }}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                            Return Equipment
                        </button>
                    </form>
                    @endforeach
                    @endif
                </div>
            </div>

            <!-- Current Inventory Table -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in">
                <div class="bg-gray-800 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Current Inventory</h2>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div class="relative w-64">
                            <input type="text" id="searchInput" placeholder="Search inventory..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                        <button id="exportBtn" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                            <i class="fas fa-file-export mr-2"></i> Export CSV
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Staff</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Equipment</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Model/Brand</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Date Issued</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Serial</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">PR Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody" class="bg-white divide-y divide-gray-200">
                                @if ($equipment->isEmpty())
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">No Current Record</td>
                                </tr>
                                @else
                                @foreach ($equipment as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->staff_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->department->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->equipment_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->model_brand }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->date_issued }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->serial_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->pr_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status == 'issued' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($item->status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form action="{{ route('inventory.delete', $item->id) }}" method="POST" class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn" data-item="{{ $item->equipment_name }}">
                                                <i class="fas fa-trash-alt mr-1"></i> Delete
                                            </button>
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

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Tab switching
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    tabButtons.forEach(btn => {
                        btn.classList.remove('bg-gray-100');
                        btn.classList.add('hover:bg-gray-100');
                    });
                    button.classList.add('bg-gray-100');
                    button.classList.remove('hover:bg-gray-100');

                    tabContents.forEach(content => content.classList.add('hidden'));
                    document.getElementById(`${button.dataset.tab}-tab`).classList.remove('hidden');
                });
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    const itemName = this.dataset.item;

                    Swal.fire({
                        title: `Delete "${itemName}"?`,
                        text: "This action cannot be undone!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            const originalHtml = this.innerHTML;
                            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Deleting...';
                            this.disabled = true;

                            // Send AJAX request
                            fetch(form.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        _method: 'DELETE'
                                    })
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
                                    if (data.success) {
                                        // Remove the row from table
                                        form.closest('tr').remove();
                                        Swal.fire('Deleted!', data.message, 'success');
                                    } else {
                                        throw new Error(data.message);
                                    }
                                })
                                .catch(error => {
                                    this.innerHTML = originalHtml;
                                    this.disabled = false;
                                    Swal.fire('Error!', error.message || 'Failed to delete item', 'error');
                                });
                        }
                    });
                });
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    document.querySelectorAll('#inventoryTableBody tr').forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            // CSV Export
            document.getElementById('exportBtn')?.addEventListener('click', function() {
                const rows = [];
                const headers = [];

                document.querySelectorAll('#inventoryTableBody thead th').forEach(header => {
                    headers.push(header.textContent.trim());
                });
                rows.push(headers.join(','));

                document.querySelectorAll('#inventoryTableBody tr:not([style*="none"])').forEach(row => {
                    const rowData = [];
                    row.querySelectorAll('td').forEach(cell => {
                        rowData.push(`"${cell.textContent.replace(/"/g, '""')}"`);
                    });
                    rows.push(rowData.join(','));
                });

                const csvContent = rows.join('\n');
                const blob = new Blob([csvContent], {
                    type: 'text/csv'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'inventory_export.csv';
                a.click();
                URL.revokeObjectURL(url);
            });

            // Form submission with duplicate check
            const issueForm = document.getElementById('issueForm');
            if (issueForm) {
                issueForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const payload = Object.fromEntries(formData.entries());

                    try {
                        // 1. Check for duplicates
                        const checkResponse = await fetch('{{ route("inventory.check-duplicates") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                serial_number: payload.serial_number,
                                pr_number: payload.pr_number
                            })
                        });

                        if (!checkResponse.ok) throw new Error(`HTTP error! Status: ${checkResponse.status}`);

                        const checkData = await checkResponse.json();
                        console.log("Duplicate check:", checkData);

                        // 2. Handle duplicates
                        if (checkData.serial_exists || checkData.pr_exists) {
                            let message = 'Potential duplicates found:\n';
                            if (checkData.serial_exists) message += `• Serial Number "${payload.serial_number}" exists\n`;
                            if (checkData.pr_exists) message += `• PR Number "${payload.pr_number}" exists\n`;

                            const result = await Swal.fire({
                                title: 'Duplicate Detected',
                                text: message,
                                icon: 'warning',
                                showCancelButton: !checkData.serial_exists,
                                confirmButtonText: checkData.serial_exists ? 'OK' : 'Proceed Anyway',
                                cancelButtonText: 'Cancel'
                            });

                            if (result.isConfirmed && !checkData.serial_exists) {
                                this.submit();
                            }
                        } else {
                            this.submit();
                        }
                    } catch (error) {
                        console.error("Error:", error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to validate data. Please try again.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    </script>
</x-app-layout>
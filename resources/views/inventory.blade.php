<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventory Management') }}
        </h2>
    </x-slot>

    <div class="flex min-h-screen bg-gray-50">
        <button id="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 bg-gray-800 text-white p-2 rounded-lg">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Main Content -->
        <div class="flex-1 container mx-auto px-4 py-8">
            <div class="text-center mb-10 animate-fade-in">
                <h2 class="text-2xl font-bold text-red-600">Inventory Management</h2>
                <p class="text-lg text-green-600">Track equipment issuance and returns</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Equipment Issuance Form -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden form-card">
                    <div class="bg-red-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white">Issue Equipment</h2>
                    </div>
                    <div class="p-6">
                        <form id="issueForm" action="{{ route('inventory.issue') }}" method="POST" class="space-y-4" aria-label="Issue Equipment Form">
                            @csrf
                            <div>
                                <label for="staff_name" class="block text-sm font-medium text-gray-700 mb-1">Staff Name *</label>
                                <input type="text" name="staff_name" id="staff_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Staff Name">
                            </div>
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                                <select name="department" id="department" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Department">
                                    <option value="">Select Department</option>
                                    <option value="IT">IT</option>
                                    <option value="Finance">Finance</option>
                                    <option value="HR">Human Resources</option>
                                    <option value="Operations">Operations</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Sales">Sales</option>
                                    <option value="Engineering">Engineering</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Logistics">Logistics</option>
                                    <option value="Customer Support">Customer Support</option>
                                </select>
                            </div>
                            <div>
                                <label for="equipment_name" class="block text-sm font-medium text-gray-700 mb-1">Equipment Issued *</label>
                                <input type="text" name="equipment_name" id="equipment_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Equipment Issued">
                            </div>
                            <div>
                                <label for="model_brand" class="block text-sm font-medium text-gray-700 mb-1">Model / Brand *</label>
                                <input type="text" name="model_brand" id="model_brand" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Model or Brand">
                            </div>
                            <div>
                                <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-1">Serial Number *</label>
                                <input type="text" name="serial_number" id="serial_number" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Serial Number">
                            </div>
                            <div>
                                <label for="date_issued" class="block text-sm font-medium text-gray-700 mb-1">Date Issued *</label>
                                <input type="date" name="date_issued" id="date_issued" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Date Issued">
                            </div>
                            <div>
                                <label for="pr_number" class="block text-sm font-medium text-gray-700 mb-1">PR Number *</label>
                                <input type="text" name="pr_number" id="pr_number" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="PR Number">
                            </div>
                            <div>
                                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                                <textarea name="remarks" id="remarks" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Remarks"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200" aria-label="Submit Equipment Issuance">
                                Issue Equipment
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Equipment Return Form -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden form-card">
                    <div class="bg-green-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white">Return Equipment</h2>
                    </div>
                    <div class="p-6">
                        <form id="returnForm" action="{{ route('inventory.return') }}" method="POST" class="space-y-4" aria-label="Return Equipment Form">
                            @csrf
                            <div>
                                <label for="return_staff_name" class="block text-sm font-medium text-gray-700 mb-1">Staff Name *</label>
                                <input type="text" name="return_staff_name" id="return_staff_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Staff Name">
                            </div>
                            <div>
                                <label for="return_serial_number" class="block text-sm font-medium text-gray-700 mb-1">Serial Number *</label>
                                <input type="text" name="return_serial_number" id="return_serial_number" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Serial Number">
                            </div>
                            <div>
                                <label for="date_returned" class="block text-sm font-medium text-gray-700 mb-1">Date Returned *</label>
                                <input type="date" name="date_returned" id="date_returned" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Date Returned">
                            </div>
                            <div>
                                <label for="return_remarks" class="block text-sm font-medium text-gray-700 mb-1">Condition Remarks</label>
                                <textarea name="return_remarks" id="return_remarks" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Condition Remarks"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200" aria-label="Submit Equipment Return">
                                Return Equipment
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Inventory Listing -->
            <div class="mt-12 bg-white rounded-xl shadow-md overflow-hidden animate-fade-in">
                <div class="bg-gray-800 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Current Inventory Records</h2>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div class="relative w-64">
                            <input type="text" id="searchInput" placeholder="Search inventory..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Search inventory">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                        <button id="exportBtn" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center" aria-label="Export to CSV">
                            <i class="fas fa-file-export mr-2"></i> Export CSV
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" aria-label="Inventory Records">
                            <thead class="bg-green-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Staff</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Equipment</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Model</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Serial</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Issued</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">PR Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody" class="bg-white divide-y divide-gray-200">
                                @foreach ($issuances as $issuance)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->staff_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->department }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->equipment->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->equipment->model_brand }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->equipment->serial_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->date_issued }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->pr_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $issuance->date_returned ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $issuance->date_returned ? 'Returned' : 'Active' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3" data-id="{{ $issuance->id }}" onclick="editIssuance(this)" aria-label="Edit record for {{ $issuance->staff_name }}">Edit</button>
                                            <form action="{{ route('inventory.delete', $issuance->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete the record for {{ $issuance->staff_name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" aria-label="Delete record for {{ $issuance->staff_name }}">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            Showing <span id="startItem">{{ $issuances->firstItem() }}</span> to <span id="endItem">{{ $issuances->lastItem() }}</span> of <span id="totalItems">{{ $issuances->total() }}</span> entries
                        </div>
                        {{ $issuances->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
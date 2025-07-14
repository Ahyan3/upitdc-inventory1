<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventory') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center mb-10 animate-fade-in">
                <h2 class="text-2xl font-bold text-red-600">Inventory Management</h2>
                <p class="text-lg text-black-600">Manage equipment issuance and returns</p>
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
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="equipment_name" class="block text-sm font-medium text-gray-700 mb-1">Equipment Issued *</label>
                            <input type="text" name="equipment_name" id="equipment_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Equipment Name">
                        </div>
                        <div>
                            <label for="model_brand" class="block text-sm font-medium text-gray-700 mb-1">Model/Brand *</label>
                            <input type="text" name="model_brand" id="model_brand" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Model/Brand">
                        </div>
                        <div>
                            <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-1">Serial Number *</label>
                            <input type="text" name="serial_number" id="serial_number" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Serial Number">
                        </div>
                        <div>
                            <label for="date_issued" class="block text-sm font-medium text-gray-700 mb-1">Date Issued *</label>
                            <input type="date" name="date_issued" id="date_issued" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Date Issued">
                        </div>
                        <div>
                            <label for="pr_number" class="block text-sm font-medium text-gray-700 mb-1">PR Number *</label>
                            <input type="text" name="pr_number" id="pr_number" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="PR Number">
                        </div>
                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                            <textarea name="remarks" id="remarks" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Remarks"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200" aria-label="Issue Equipment">
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
                            <form action="{{ route('inventory.return', $issuance) }}" method="POST" class="space-y-4 mb-4" aria-label="Return Equipment Form">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Equipment</label>
                                        <input type="text" value="{{ $issuance->equipment->name }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" aria-label="Equipment Name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Staff</label>
                                        <input type="text" value="{{ $issuance->staff_name }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" aria-label="Staff Name">
                                    </div>
                                    <div>
                                        <label for="date_returned_{{ $issuance->id }}" class="block text-sm font-medium text-gray-700 mb-1">Date Returned *</label>
                                        <input type="date" name="date_returned" id="date_returned_{{ $issuance->id }}" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Date Returned">
                                    </div>
                                    <div>
                                        <label for="remarks_{{ $issuance->id }}" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                                        <textarea name="remarks" id="remarks_{{ $issuance->id }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Remarks">{{ $issuance->remarks }}</textarea>
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200" aria-label="Return Equipment">
                                    Return Equipment
                                </button>
                            </form>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Current Inventory -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in">
                <div class="bg-gray-800 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Current Inventory</h2>
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Equipment</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Model/Brand</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Serial</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody" class="bg-white divide-y divide-gray-200">
                                @if ($equipment->isEmpty())
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No Current Record</td>
                                    </tr>
                                @else
                                    @foreach ($equipment as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->model_brand }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->serial_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($item->issuances()->whereNull('date_returned')->exists())
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Issued</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Available</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form action="{{ route('inventory.delete', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ $item->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" aria-label="Delete {{ $item->name }}">Delete</button>
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
</x-app-layout>
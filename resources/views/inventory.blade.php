<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-[#00553d] leading-tight">
            {{ __('Inventory') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8 w-full">
            <!-- CSRF Token -->
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <!-- Header -->
            <div class="text-center mb-10 animate-fade-in w-full">
                <h2 class="text-base font-bold text-[#90143c]">Inventory Management</h2>
                <p class="text-[0.65rem] text-[#00553d]">Manage equipment issuance and returns</p>
            </div>

            <!-- Accordion Forms Section -->
            <div class="space-y-4 w-full mb-8">
                <!-- Issue Equipment Accordion -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden w-full border border-[#ffcc34]">
                    <button class="accordion-toggle w-full flex justify-between items-center p-5 bg-[#90143c] text-white text-xs font-semibold rounded-t-xl hover:bg-[#6b102d] transition duration-200">
                        <span>Issue Equipment</span>
                        <svg class="w-3 h-3 transform transition-transform duration-200 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="accordion-content p-5">
                        <form id="issueForm" action="{{ route('inventory.issue') }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="staff_name" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Staff Name *</label>
                                    <input type="text" name="staff_name" id="staff_name" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                </div>
                                <div>
                                    <label for="department_id" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Department *</label>
                                    <select name="department_id" id="department_id" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="equipment_name" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Equipment Name *</label>
                                    <input type="text" name="equipment_name" id="equipment_name" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                </div>
                                <div>
                                    <label for="model_brand" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Model/Brand *</label>
                                    <input type="text" name="model_brand" id="model_brand" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                </div>
                                <div>
                                    <label for="date_issued" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Date Issued *</label>
                                    <input type="date" name="date_issued" id="date_issued" value="{{ now()->format('Y-m-d') }}" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                </div>
                                <div>
                                    <label for="serial_number" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Serial Number *</label>
                                    <input type="text" name="serial_number" id="serial_number" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                </div>
                                <div>
                                    <label for="pr_number" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">PR Number *</label>
                                    <input type="text" name="pr_number" id="pr_number" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                </div>
                                <div>
                                    <label for="status" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Status *</label>
                                    <select name="status" id="status" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                        <option value="available">Available</option>
                                        <option value="issued">Issued</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="remarks" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Remarks</label>
                                    <textarea name="remarks" id="remarks" class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="w-full mt-4 bg-[#00553d] hover:bg-[#003d2b] text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#ffcc34] text-sm">
                                Issue Equipment
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Return Equipment Accordion -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden w-full border border-[#ffcc34]">
                    <button class="accordion-toggle w-full flex justify-between items-center p-5 bg-[#90143c] text-white text-xs font-semibold rounded-t-xl hover:bg-[#6b102d] transition duration-200">
                        <span>Return Equipment</span>
                        <svg class="w-3 h-3 transform transition-transform duration-200 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="accordion-content p-5">
                        @if ($issuances->whereNull('date_returned')->isEmpty())
                        <div class="text-center text-[#00553d] py-4 text-xs">No equipment currently issued out</div>
                        @else
                        <!-- Search and Filter Controls -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="relative">
                                <input type="text" id="returnSearch" placeholder="Search equipment..." class="w-full pl-8 pr-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                <div class="absolute left-2 top-2 text-[#00553d]">
                                    <i class="fas fa-search text-xs"></i>
                                </div>
                            </div>
                            <div>
                                <select id="returnDepartmentFilter" class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->name }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select id="returnStatusFilter" class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                    <option value="">All Equipment</option>
                                    <option value="laptop">Laptops</option>
                                    <option value="desktop">Desktops</option>
                                    <option value="monitor">Monitors</option>
                                </select>
                            </div>
                        </div>

                        <!-- Equipment Accordions -->
                        <div class="space-y-4 w-full" id="returnEquipmentContainer">
                            @foreach ($issuances->whereNull('date_returned') as $issuance)
                            <div class="equipment-accordion bg-gray-50 rounded-lg overflow-hidden shadow-sm w-full border border-[#ffcc34]">
                                <button class="equipment-toggle w-full flex justify-between items-center p-4 bg-[#ffcc34] hover:bg-[#e6b82f] transition duration-200">
                                    <div class="text-left">
                                        <h3 class="font-medium text-xs text-[#00553d]">{{ $issuance->equipment->equipment_name ?? 'N/A' }}</h3>
                                        <p class="text-[0.65rem] text-[#00553d]">{{ $issuance->staff->name ?? 'N/A' }} • {{ $issuance->department->name ?? 'N/A' }}</p>
                                    </div>
                                    <svg class="w-3 h-3 transform transition-transform duration-200 rotate-180" fill="none" stroke="#00553d" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="equipment-content p-4">
                                    <form action="{{ route('inventory.return', $issuance) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Equipment</label>
                                                <input type="text" value="{{ $issuance->equipment->equipment_name ?? 'N/A' }}" disabled class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg bg-gray-100 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Staff</label>
                                                <input type="text" value="{{ $issuance->staff->name ?? 'N/A' }}" disabled class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg bg-gray-100 text-sm">
                                            </div>
                                            <div>
                                                <label for="date_returned_{{ $issuance->id }}" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Date Returned *</label>
                                                <input type="date" name="date_returned" id="date_returned_{{ $issuance->id }}" value="{{ now()->format('Y-m-d') }}" required class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                            </div>
                                            <div>
                                                <label for="condition_{{ $issuance->id }}" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Condition</label>
                                                <select name="condition" id="condition_{{ $issuance->id }}" class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                                    <option value="good">Good</option>
                                                    <option value="damaged">Damaged</option>
                                                    <option value="lost">Lost</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label for="remarks_{{ $issuance->id }}" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Remarks</label>
                                                <textarea name="remarks" id="remarks_{{ $issuance->id }}" class="w-full px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">{{ $issuance->return_notes ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        <button type="submit" class="w-full mt-4 bg-[#00553d] hover:bg-[#003d2b] text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 border border-[#ffcc34] text-sm">
                                            Return Equipment
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination Controls -->
                        <div class="mt-6 flex items-center justify-between w-full">
                            <div class="text-[0.65rem] text-[#00553d]">
                                Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">{{ $issuances->whereNull('date_returned')->count() }}</span> results
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-2 py-1 border rounded-lg bg-gray-100 text-[#00553d] hover:bg-gray-200 text-xs border-[#ffcc34]">
                                    « Previous
                                </button>
                                <button class="px-2 py-1 border rounded-lg bg-[#90143c] text-white hover:bg-[#6b102d] text-xs border-[#ffcc34]">
                                    1
                                </button>
                                <button class="px-2 py-1 border rounded-lg bg-gray-100 text-[#00553d] hover:bg-gray-200 text-xs border-[#ffcc34]">
                                    2
                                </button>
                                <button class="px-2 py-1 border rounded-lg bg-gray-100 text-[#00553d] hover:bg-gray-200 text-xs border-[#ffcc34]">
                                    3
                                </button>
                                <button class="px-2 py-1 border rounded-lg bg-gray-100 text-[#00553d] hover:bg-gray-200 text-xs border-[#ffcc34]">
                                    4
                                </button>
                                <button class="px-2 py-1 border rounded-lg bg-gray-100 text-[#00553d] hover:bg-gray-200 text-xs border-[#ffcc34]">
                                    5
                                </button>
                                <button class="px-2 py-1 border rounded-lg bg-gray-100 text-[#00553d] hover:bg-gray-200 text-xs border-[#ffcc34]">
                                    Next »
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Current Inventory Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in mb-8 w-full border border-[#ffcc34]">
                    <div class="bg-[#00553d] px-5 py-3">
                        <h2 class="text-xs font-semibold text-white">Current Inventory</h2>
                    </div>
                    <div class="p-5">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4 w-full">
                            <div class="relative w-full md:w-64">
                                <input type="text" id="searchInput" placeholder="Search inventory..." class="w-full pl-8 pr-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                <div class="absolute left-2 top-2 text-[#00553d]">
                                    <i class="fas fa-search text-xs"></i>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                                <select id="departmentFilter" class="px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->name }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                <select id="statusFilter" class="px-3 py-1.5 border border-[#ffcc34] rounded-lg focus:ring-[#00553d] focus:border-[#00553d] text-sm">
                                    <option value="">All Statuses</option>
                                    <option value="available">Available</option>
                                    <option value="issued">Issued</option>
                                </select>
                                <button id="exportBtn" class="bg-[#90143c] hover:bg-[#6b102d] text-white font-medium py-1.5 px-3 rounded-lg transition duration-200 flex items-center border border-[#ffcc34] text-sm">
                                    <i class="fas fa-file-export mr-1 text-xs"></i> Export CSV
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto w-full">
                            <table class="min-w-full table-auto divide-y divide-[#ffcc34]">
                                <thead class="bg-[#ffcc34]">
                                    <tr>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Staff</th>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Department</th>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Equipment</th>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Model/Brand</th>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Date Issued</th>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Serial</th>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">PR Number</th>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="inventoryTableBody" class="bg-white divide-y divide-[#ffcc34]">
                                    @if ($equipment->isEmpty())
                                    <tr>
                                        <td colspan="9" class="px-5 py-3 text-center text-[#00553d] text-xs">No equipment records found</td>
                                    </tr>
                                    @else
                                    @foreach ($equipment as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->staff_name }}</td>
                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->department->name ?? 'N/A' }}</td>
                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->equipment_name }}</td>
                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->model_brand }}</td>
                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->date_issued }}</td>
                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->serial_number }}</td>
                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->pr_number }}</td>
                                        <td class="px-5 py-3 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status == 'issued' ? 'bg-[#ffcc34] text-[#00553d]' : 'bg-gray-100 text-[#00553d]' }}">{{ ucfirst($item->status) }}</span>
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap flex space-x-2">
                                            <a href="{{ route('inventory.view', $item->id) }}" class="text-[#90143c] hover:text-[#6b102d]" title="View Details">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            <a href="{{ route('inventory.edit', $item->id) }}" class="text-[#90143c] hover:text-[#6b102d]" title="Edit">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            <form action="{{ route('inventory.delete', $item->id) }}" method="POST" class="inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="text-[#90143c] hover:text-[#6b102d] delete-btn" data-item="{{ $item->equipment_name }}" title="Delete">
                                                    <i class="fas fa-trash-alt text-xs"></i>
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

                <!-- History Log Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in mb-8 w-full border border-[#ffcc34]">
                    <div class="bg-[#00553d] px-5 py-3">
                        <h2 class="text-xs font-semibold text-white">History Log</h2>
                    </div>
                    <div class="p-5">
                        <div class="overflow-x-auto w-full">
                            <table class="min-w-full table-auto divide-y divide-[#ffcc34]">
                                <thead class="bg-[#ffcc34]">
                                    <tr>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Action</th>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">User</th>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Staff</th>
                                        <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Description</th>
                                    </tr>
                                </thead>
                                <tbody id="historyTableBody" class="bg-white divide-y divide-[#ffcc34]">
                                    @if ($historyLogs->isEmpty())
                                    <tr>
                                        <td colspan="5" class="px-5 py-3 text-center text-[#00553d] text-xs">No history records found</td>
                                    </tr>
                                    @else
                                    @foreach ($historyLogs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->action }}</td>
                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->action_date }}</td>
                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->user->name ?? 'N/A' }}</td>
                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->staff->name ?? 'N/A' }}</td>
                                        <td class="px-5 py-3 text-xs text-[#00553d]">{{ $log->description }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Equipment Issuance Graph -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in mb-8 w-full border border-[#ffcc34]">
                    <div class="bg-[#00553d] px-5 py-3">
                        <h2 class="text-xs font-semibold text-white">Equipment Issuance Statistics</h2>
                    </div>
                    <div class="p-5">
                        <canvas id="equipmentChart" class="w-full h-64" data-equipment="{{ json_encode($equipmentData) }}"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript Dependencies -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script src="{{ asset('js/inventory.js') }}"></script>

        <script>
            const apiUrl = "{{ url('/api/total-staff') }}";

            function updateStaffCount() {
                fetch(apiUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('staff-count').innerText = data.count;
                    })
                    .catch(error => {
                        console.error('Error fetching staff count:', error);
                    });
            }

            updateStaffCount();
            setInterval(updateStaffCount, 5000);
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Equipment accordion toggles
                const equipmentToggles = document.querySelectorAll('.equipment-toggle');
                equipmentToggles.forEach(toggle => {
                    toggle.addEventListener('click', function() {
                        const content = this.nextElementSibling;
                        const icon = this.querySelector('svg');

                        content.classList.toggle('hidden');
                        icon.classList.toggle('rotate-180');
                    });
                });

                // Search and filter functionality for return equipment
                const returnSearch = document.getElementById('returnSearch');
                const returnDeptFilter = document.getElementById('returnDepartmentFilter');
                const returnStatusFilter = document.getElementById('returnStatusFilter');

                function filterReturnEquipment() {
                    const searchTerm = returnSearch.value.toLowerCase();
                    const department = returnDeptFilter.value;
                    const equipmentType = returnStatusFilter.value;

                    document.querySelectorAll('#returnEquipmentContainer .equipment-accordion').forEach(item => {
                        const text = item.textContent.toLowerCase();
                        const deptText = item.querySelector('p').textContent;
                        const matchesSearch = text.includes(searchTerm);
                        const matchesDept = !department || deptText.includes(department);
                        const matchesType = !equipmentType || text.includes(equipmentType);

                        item.style.display = matchesSearch && matchesDept && matchesType ? '' : 'none';
                    });
                }

                returnSearch.addEventListener('input', filterReturnEquipment);
                returnDeptFilter.addEventListener('change', filterReturnEquipment);
                returnStatusFilter.addEventListener('change', filterReturnEquipment);

                // Pagination functionality (would need server-side implementation)
                const paginationButtons = document.querySelectorAll('.pagination-button');
                paginationButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        paginationButtons.forEach(btn => {
                            btn.classList.remove('bg-[#90143c]', 'text-white');
                            btn.classList.add('bg-gray-100', 'text-[#00553d]');
                        });
                        this.classList.add('bg-[#90143c]', 'text-white');
                        this.classList.remove('bg-gray-100', 'text-[#00553d]');
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                // Accordion functionality
                const accordionToggles = document.querySelectorAll('.accordion-toggle');

                // Open all accordions by default
                accordionToggles.forEach(toggle => {
                    toggle.nextElementSibling.classList.remove('hidden');
                    toggle.querySelector('svg').classList.add('rotate-180');
                });

                accordionToggles.forEach(toggle => {
                    toggle.addEventListener('click', function() {
                        const content = this.nextElementSibling;
                        const icon = this.querySelector('svg');

                        // Toggle content visibility
                        content.classList.toggle('hidden');

                        // Rotate icon
                        icon.classList.toggle('rotate-180');
                    });
                });

                // Delete functionality
                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const form = this.closest('form');
                        const itemName = this.dataset.item;

                        Swal.fire({
                            title: `Delete "${itemName}"?`,
                            text: "This action cannot be undone!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: '#90143c',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, delete it!',
                            customClass: {
                                title: 'text-xs',
                                content: 'text-[0.65rem]'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const originalHtml = this.innerHTML;
                                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Deleting...';
                                this.disabled = true;

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
                                            form.closest('tr').remove();
                                            Swal.fire({
                                                title: 'Deleted!',
                                                text: data.message,
                                                icon: 'success',
                                                confirmButtonColor: '#00553d',
                                                customClass: {
                                                    title: 'text-xs',
                                                    content: 'text-[0.65rem]'
                                                }
                                            });
                                        } else {
                                            throw new Error(data.message);
                                        }
                                    })
                                    .catch(error => {
                                        this.innerHTML = originalHtml;
                                        this.disabled = false;
                                        Swal.fire({
                                            title: 'Error!',
                                            text: error.message || 'Failed to delete item',
                                            icon: 'error',
                                            confirmButtonColor: '#90143c',
                                            customClass: {
                                                title: 'text-xs',
                                                content: 'text-[0.65rem]'
                                            }
                                        });
                                    });
                            }
                        });
                    });
                });

                // Search and Filter functionality
                const searchInput = document.getElementById('searchInput');
                const departmentFilter = document.getElementById('departmentFilter');
                const statusFilter = document.getElementById('statusFilter');

                function filterTable() {
                    const searchTerm = searchInput.value.toLowerCase();
                    const department = departmentFilter.value;
                    const status = statusFilter.value;

                    document.querySelectorAll('#inventoryTableBody tr').forEach(row => {
                        if (row.cells.length < 8) return; // Skip header/empty rows

                        const text = row.textContent.toLowerCase();
                        const departmentText = row.cells[1].textContent;
                        const statusText = row.cells[7].textContent.toLowerCase();

                        const matchesSearch = text.includes(searchTerm);
                        const matchesDepartment = !department || departmentText === department;
                        const matchesStatus = !status || statusText.includes(status.toLowerCase());

                        row.style.display = matchesSearch && matchesDepartment && matchesStatus ? '' : 'none';
                    });
                }

                searchInput.addEventListener('input', filterTable);
                departmentFilter.addEventListener('change', filterTable);
                statusFilter.addEventListener('change', filterTable);

                // CSV Export
                document.getElementById('exportBtn').addEventListener('click', function() {
                    const rows = [];
                    const headers = [];

                    document.querySelectorAll('#inventoryTableBody thead th').forEach(header => {
                        headers.push(header.textContent.trim());
                    });
                    rows.push(headers.join(','));

                    document.querySelectorAll('#inventoryTableBody tr:not([style*="none"])').forEach(row => {
                        if (row.cells.length < 8) return; // Skip header/empty rows

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
                    a.download = 'inventory_export_' + new Date().toISOString().slice(0, 10) + '.csv';
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
                        const submitButton = this.querySelector('button[type="submit"]');
                        const originalButtonText = submitButton.innerHTML;

                        try {
                            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Checking...';
                            submitButton.disabled = true;

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
                            if (checkData.serial_exists || checkData.pr_exists) {
                                let message = 'Potential duplicates found:\n';
                                if (checkData.serial_exists) message += `• Serial Number "${payload.serial_number}" exists\n`;
                                if (checkData.pr_exists) message += `• PR Number "${payload.pr_number}" exists\n`;

                                const result = await Swal.fire({
                                    title: 'Duplicate Detected',
                                    text: message,
                                    icon: 'warning',
                                    showCancelButton: !checkData.serial_exists,
                                    confirmButtonColor: '#00553d',
                                    cancelButtonColor: '#90143c',
                                    confirmButtonText: checkData.serial_exists ? 'OK' : 'Proceed Anyway',
                                    cancelButtonText: 'Cancel',
                                    customClass: {
                                        title: 'text-xs',
                                        content: 'text-[0.65rem]'
                                    }
                                });

                                if (result.isConfirmed && !checkData.serial_exists) {
                                    this.submit();
                                } else {
                                    submitButton.innerHTML = originalButtonText;
                                    submitButton.disabled = false;
                                }
                            } else {
                                this.submit();
                            }
                        } catch (error) {
                            submitButton.innerHTML = originalButtonText;
                            submitButton.disabled = false;
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to validate data. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#90143c',
                                customClass: {
                                    title: 'text-xs',
                                    content: 'text-[0.65rem]'
                                }
                            });
                        }
                    });
                }

                // Line Graph for Equipment Issuance
                const ctx = document.getElementById('equipmentChart');
                if (ctx) {
                    const equipmentData = JSON.parse(ctx.dataset.equipment);
                    const labels = equipmentData.map(item => item.equipment_name);
                    const data = equipmentData.map(item => item.issuance_count);

                    ```chartjs
                    {
                        type: "bar",
                        data: {
                            labels: labels,
                            datasets: [{
                                label: "Issuance Count",
                                data: data,
                                backgroundColor: "#ffcc34",
                                borderColor: "#00553d",
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: "Number of Issuances",
                                        font: {
                                            size: 10
                                        },
                                        color: "#00553d"
                                    },
                                    ticks: {
                                        color: "#00553d",
                                        font: {
                                            size: 10
                                        }
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: "Equipment Type",
                                        font: {
                                            size: 10
                                        },
                                        color: "#00553d"
                                    },
                                    ticks: {
                                        color: "#00553d",
                                        font: {
                                            size: 10
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    labels: {
                                        font: {
                                            size: 10
                                        },
                                        color: "#00553d"
                                    }
                                }
                            }
                        }
                    }
                    ```
                }
            });
        </script>

        <x-auth-footer />
</x-app-layout>
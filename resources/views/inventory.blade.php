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
                    <div class="bg-[#90143c] px-5 py-3">
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
                    <div class="bg-[#90143c] px-5 py-3">
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
                    <div class="bg-[#90143c] px-5 py-3">
                        <h2 class="text-xs font-semibold text-white">Equipment Issuance Statistics</h2>
                    </div>
                    <div class="p-5">
                        <canvas id="equipmentChart" class="w-full h-64" data-equipment="{{ json_encode($equipmentData) }}"></canvas>
                    </div>
                    <!-- <div id="staff-count">Loading...</div> -->
                </div>
            </div>
        </div>

        <!-- JavaScript Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    window.checkDuplicatesUrl = "{{ route('inventory.check-duplicates') }}";

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Accordions
        const accordionToggles = document.querySelectorAll('.accordion-toggle');
        accordionToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const content = toggle.nextElementSibling;
                const svg = toggle.querySelector('svg');
                content.classList.toggle('hidden');
                svg.classList.toggle('rotate-180');
            });
        });

        // Initialize Equipment Toggles
        const equipmentToggles = document.querySelectorAll('.equipment-toggle');
        equipmentToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const content = toggle.nextElementSibling;
                const svg = toggle.querySelector('svg');
                content.classList.toggle('hidden');
                svg.classList.toggle('rotate-180');
            });
        });

        // Form submission with duplicate check
        const issueForm = document.getElementById('issueForm');
        if (issueForm) {
            issueForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const payload = Object.fromEntries(formData.entries());
                const submitButton = this.querySelector('button[type="submit"]');

                if (!submitButton) {
                    console.error('Submit button not found');
                    return;
                }

                if (!payload.serial_number || !payload.pr_number) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Serial number and PR number are required.',
                        icon: 'error',
                        confirmButtonColor: '#90143c',
                        customClass: {
                            title: 'text-xs',
                            content: 'text-[0.65rem]'
                        }
                    });
                    return;
                }

                const originalButtonText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Checking...';
                submitButton.disabled = true;

                try {
                    const checkResponse = await fetch(window.checkDuplicatesUrl, {
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

        // Chart initialization
        const ctx = document.getElementById('equipmentChart');
        if (ctx) {
            try {
                const equipmentDataAttr = ctx.dataset.equipment;
                if (!equipmentDataAttr) {
                    console.log('No equipment data found in canvas dataset');
                    return;
                }

                const equipmentData = JSON.parse(equipmentDataAttr);
                const labels = Object.keys(equipmentData);
                const data = Object.values(equipmentData);

                if (labels.length === 0) {
                    console.log('Equipment data is empty');
                    ctx.style.display = 'none';
                    return;
                }

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Issuance Count',
                            data: data,
                            backgroundColor: '#ffcc34',
                            borderColor: '#00553d',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Issuances',
                                    font: { size: 10 },
                                    color: '#00553d'
                                },
                                ticks: { color: '#00553d', font: { size: 10 } }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Equipment Type',
                                    font: { size: 10 },
                                    color: '#00553d'
                                },
                                ticks: { color: '#00553d', font: { size: 10 } }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: { font: { size: 10 }, color: '#00553d' }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error initializing chart:', error);
                ctx.style.display = 'none';
            }
        }
    });
</script>

<x-auth-footer />
<!--        
       <script>
            fetch('http://127.0.0.1:8000/inventory/inventory/check-duplicates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    // your data here
                })
            })

            const checkDuplicatesUrl = "{{ route('inventory.check-duplicates') }}";

            // Move checkForDuplicates function to global scope
            async function checkForDuplicates(serialNumber, prNumber) {
                try {
                    const response = await fetch(checkDuplicatesUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            serial_number: serialNumber,
                            pr_number: prNumber
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    console.log('Duplicate check results:', data);

                    // Handle the response (show alerts, mark fields, etc.)
                    if (data.serial_exists || data.pr_exists) {
                        Swal.fire({
                            title: 'Duplicate Found!',
                            text: data.message,
                            icon: 'warning'
                        });
                    }

                    return data;
                } catch (error) {
                    console.error('Error checking duplicates:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to check for duplicates',
                        icon: 'error'
                    });
                    return {
                        error: true
                    };
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                console.log('Inventory system initializing...');

                // Initialize all components
                initializeStaffCount();
                initializeEquipmentToggles();
                initializeSearchAndFilters();
                initializePagination();
                initializeAccordions();
                initializeDeleteFunctionality();
                initializeFormSubmission();
                initializeExport();
                initializeChart();

                // Update staff count periodically
                setInterval(updateStaffCount, 30000);
            });

            // Form submission with duplicate check
            function initializeFormSubmission() {
                // Fix: Check for both possible form IDs
                const inventoryForm = document.querySelector('#inventoryForm');
                const issueForm = document.getElementById('issueForm');

                // Handle inventory form if it exists
                if (inventoryForm) {
                    inventoryForm.addEventListener('submit', async function(e) {
                        e.preventDefault();

                        const serial = document.querySelector('#serial_number').value;
                        const pr = document.querySelector('#pr_number').value;

                        const duplicates = await checkForDuplicates(serial, pr);

                        if (!duplicates.error && !duplicates.serial_exists && !duplicates.pr_exists) {
                            this.submit(); // Only submit if no duplicates
                        }
                    });
                }

                // Handle issue form
                if (issueForm) {
                    issueForm.addEventListener('submit', async function(e) {
                        e.preventDefault();

                        const formData = new FormData(this);
                        const payload = Object.fromEntries(formData.entries());
                        const submitButton = this.querySelector('button[type="submit"]');

                        if (!submitButton) {
                            console.error('Submit button not found');
                            return;
                        }

                        const originalButtonText = submitButton.innerHTML;

                        try {
                            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Checking...';
                            submitButton.disabled = true;

                            // Use the global function
                            const checkData = await checkForDuplicates(payload.serial_number, payload.pr_number);

                            if (checkData.error) {
                                throw new Error('Duplicate check failed');
                            }

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
                            console.error('Form submission error:', error);
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
            } -->

</x-app-layout>
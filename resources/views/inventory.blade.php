<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-[#00553d] leading-tight">{{ __('Inventory') }}</h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8 max-w-5xl">
            <!-- CSRF Token -->
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <!-- Styles -->
            <style>
                .accordion-content {
                    transition: max-height 0.3s ease-in-out, padding 0.3s ease-in-out;
                    max-height: 0;
                    overflow: hidden;
                    padding: 0 1rem;
                }

                .accordion-content.open {
                    max-height: 2000px;
                    padding: 1rem;
                }

                .equipment-content {
                    transition: max-height 0.3s ease-in-out, padding 0.3s ease-in-out;
                    max-height: 0;
                    overflow: hidden;
                    padding: 0 1rem;
                }

                .equipment-content.open {
                    max-height: 2000px;
                    padding: 1rem;
                }

                .setting-card {
                    transition: all 0.2s ease-in-out;
                }

                .setting-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(0, 85, 61, 0.12);
                }

                .btn-loading .spinner {
                    display: inline-block;
                }

                .btn-loading .btn-text {
                    display: none;
                }

                .spinner {
                    display: none;
                }

                .fade-in {
                    animation: fadeIn 0.4s ease-in;
                }

                .slide-up {
                    animation: slideUp 0.3s ease-out;
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(8px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @keyframes slideUp {
                    from {
                        opacity: 0;
                        transform: translateY(16px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .status-indicator {
                    display: inline-block;
                    width: 6px;
                    height: 6px;
                    border-radius: 50%;
                    margin-right: 6px;
                    animation: pulse 2s infinite;
                }

                .status-available,
                .status-working,
                .status-in_use {
                    background-color: #10b981;
                }

                .status-maintenance {
                    background-color: #f59e0b;
                }

                .status-damaged {
                    background-color: #ef4444;
                }

                @keyframes pulse {

                    0%,
                    100% {
                        opacity: 1;
                    }

                    50% {
                        opacity: 0.5;
                    }
                }

                .gradient-btn {
                    background: linear-gradient(90deg, #90143c, #b01a47);
                }

                .gradient-btn:hover {
                    background: linear-gradient(90deg, #6b102d, #8e1539);
                }

                .pagination-container {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    gap: 0.5rem;
                    margin-top: 1rem;
                }

                .pagination-container a,
                .pagination-container span {
                    padding: 0.5rem 1rem;
                    border: 1px solid #ffcc34;
                    border-radius: 0.375rem;
                    font-size: 0.75rem;
                    color: #00553d;
                    transition: all 0.2s ease-in-out;
                }

                .pagination-container a:hover {
                    background-color: #00553d;
                    color: white;
                    border-color: #00553d;
                }

                .pagination-container .current {
                    background-color: #90143c;
                    color: white;
                    border-color: #90143c;
                }
            </style>

            <!-- Header -->
            <div class="text-center mb-8 fade-in">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-full mb-4 shadow-lg relative">
                    <i class="fas fa-boxes text-white text-xl animate-spin" style="animation-duration: 8s;"></i>
                    <div
                        class="absolute inset-0 rounded-full bg-gradient-to-br from-[#90143c] to-[#b01a47] opacity-20 animate-ping">
                    </div>
                </div>
                <h1
                    class="text-2xl font-bold bg-gradient-to-r from-[#90143c] to-[#00553d] bg-clip-text text-transparent">
                    Inventory Management</h1>
                <p class="text-xs text-[#00553d] opacity-80 max-w-sm mx-auto">Manage equipment issuance and returns</p>
            </div>

            <!-- Alert Container -->
            <div id="alert-container" class="mb-4"></div>

            <!-- Accordion Forms Section -->
            <div class="space-y-4 mb-8">
                <!-- Issue Equipment Accordion -->
                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center p-4 gradient-btn text-white transition-all duration-500"
                        data-target="issue-equipment-section">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 bg-white/20 rounded-md">
                                <i class="fas fa-plus-circle text-base"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-semibold block">Issue Equipment</span>
                                <span class="text-xs opacity-80">Assign equipment to staff</span>
                            </div>
                        </div>
                        <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300 rotate-180"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div id="issue-equipment-section" class="accordion-content open">
                        <div class="p-4">
                            <form id="issueForm" action="{{ route('inventory.issue') }}" method="POST"
                                class="space-y-3">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="relative group">
                                        <label for="staff_name"
                                            class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Staff Name
                                            *</label>
                                        <input type="text" name="staff_name" id="staff_name" required
                                            class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                                        <div
                                            class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                            <i class="fas fa-user text-xs"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="department_id"
                                            class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Department
                                            *</label>
                                        <select name="department_id" id="department_id" required
                                            class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                            <option value="">Select Department</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="relative group">
                                        <label for="equipment_name"
                                            class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Equipment Name
                                            *</label>
                                        <input type="text" name="equipment_name" id="equipment_name" required
                                            class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                                        <div
                                            class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                            <i class="fas fa-box text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="relative group">
                                        <label for="model_brand"
                                            class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Model/Brand
                                            *</label>
                                        <input type="text" name="model_brand" id="model_brand" required
                                            class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                                        <div
                                            class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                            <i class="fas fa-tag text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="relative group">
                                        <label for="date_issued"
                                            class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Date Issued
                                            *</label>
                                        <input type="date" name="date_issued" id="date_issued"
                                            value="{{ now()->format('Y-m-d') }}" required
                                            class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                                        <div
                                            class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                            <i class="fas fa-calendar-alt text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="relative group">
                                        <label for="serial_number"
                                            class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Serial Number
                                            *</label>
                                        <input type="text" name="serial_number" id="serial_number" required
                                            class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                                        <div
                                            class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                            <i class="fas fa-barcode text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="relative group">
                                        <label for="pr_number"
                                            class="block text-[0.65rem] font-medium text-[#00553d] mb-1">PR Number
                                            *</label>
                                        <input type="text" name="pr_number" id="pr_number" required
                                            class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                                        <div
                                            class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                            <i class="fas fa-hashtag text-xs"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="status"
                                            class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Status
                                            *</label>
                                        <select name="status" id="status" required
                                            class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                            <option value="available">Available</option>
                                            <option value="in_use">In Use</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="damaged">Damaged</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="remarks"
                                            class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Remarks</label>
                                        <textarea name="remarks" id="remarks"
                                            class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs"></textarea>
                                    </div>
                                </div>
                                <button type="submit"
                                    class="gradient-btn w-full mt-4 px-4 py-2 text-xs font-semibold text-white rounded-lg border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center justify-center transition-all duration-300">
                                    <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                    <span class="btn-text"><i class="fas fa-plus mr-2"></i>Issue Equipment</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Return Equipment Accordion -->
                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center p-4 gradient-btn text-white transition-all duration-500"
                        data-target="return-equipment-section">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 bg-white/20 rounded-md">
                                <i class="fas fa-undo text-base"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-semibold block">Return Equipment</span>
                                <span class="text-xs opacity-80">Manage equipment returns</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="bg-white bg-opacity-30 text-xs px-2 py-0.5 rounded-full font-medium">{{ $issuances->count() }}
                                items</span>
                            <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300 rotate-180"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="return-equipment-section" class="accordion-content open">
                        <div class="p-4">
                            @if ($issuances->isEmpty())
                                <div
                                    class="text-center text-[#00553d] py-4 text-xs bg-gradient-to-br from-gray-50 to-white rounded-lg border-2 border-dashed border-gray-300">
                                    <div
                                        class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-undo text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-2 font-medium">No equipment currently issued out
                                    </p>
                                    <p class="text-[0.6rem] text-gray-400">Issue equipment to start tracking returns
                                    </p>
                                </div>
                            @else
                                <!-- Search and Filter Controls -->
                                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="relative">
                                        <input type="text" id="returnSearch" placeholder="Search equipment..."
                                            class="w-full pl-8 pr-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                        <div class="absolute left-2 top-2.5 text-[#00553d]">
                                            <i class="fas fa-search text-xs"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <select id="returnDepartmentFilter"
                                            class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                            <option value="">All Departments</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->name }}">{{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <select id="returnStatusFilter"
                                            class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                            <option value="">All Equipment</option>
                                            <option value="laptop">Laptops</option>
                                            <option value="desktop">Desktops</option>
                                            <option value="monitor">Monitors</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Equipment Accordions -->
                                <div class="space-y-4" id="returnEquipmentContainer">
                                    @foreach ($issuances as $issuance)
                                        <div
                                            class="equipment-accordion bg-gray-50 rounded-lg overflow-hidden shadow-sm border border-[#ffcc34] slide-up">
                                            <button
                                                class="equipment-toggle w-full flex justify-between items-center p-4 bg-[#ffcc34] hover:bg-[#e6b82f] transition duration-200">
                                                <div class="text-left">
                                                    <h3 class="font-medium text-xs text-[#00553d]">
                                                        {{ $issuance->equipment->equipment_name ?? 'N/A' }}</h3>
                                                    <p class="text-[0.65rem] text-[#00553d]">
                                                        {{ $issuance->staff->name ?? 'N/A' }} •
                                                        {{ $issuance->department->name ?? 'N/A' }}</p>
                                                </div>
                                                <svg class="w-4 h-4 transform transition-transform duration-300"
                                                    fill="none" stroke="#00553d" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            <div class="equipment-content">
                                                <div class="p-4">
                                                    <form action="{{ route('inventory.return', $issuance) }}"
                                                        method="POST" class="space-y-3">
                                                        @csrf
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <div>
                                                                <label
                                                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Equipment</label>
                                                                <input type="text"
                                                                    value="{{ $issuance->equipment->equipment_name ?? 'N/A' }}"
                                                                    disabled
                                                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg bg-gray-100 text-xs">
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Staff</label>
                                                                <input type="text"
                                                                    value="{{ $issuance->staff->name ?? 'N/A' }}"
                                                                    disabled
                                                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg bg-gray-100 text-xs">
                                                            </div>
                                                            <div class="relative group">
                                                                <label for="date_returned_{{ $issuance->id }}"
                                                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Date
                                                                    Returned *</label>
                                                                <input type="date" name="date_returned"
                                                                    id="date_returned_{{ $issuance->id }}"
                                                                    value="{{ now()->format('Y-m-d') }}" required
                                                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                                                                <div
                                                                    class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                                                    <i class="fas fa-calendar-alt text-xs"></i>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label for="condition_{{ $issuance->id }}"
                                                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Condition</label>
                                                                <select name="condition"
                                                                    id="condition_{{ $issuance->id }}"
                                                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                                                    <option value="good">Good</option>
                                                                    <option value="damaged">Damaged</option>
                                                                    <option value="lost">Lost</option>
                                                                </select>
                                                            </div>
                                                            <div class="md:col-span-2">
                                                                <label for="remarks_{{ $issuance->id }}"
                                                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Remarks</label>
                                                                <textarea name="remarks" id="remarks_{{ $issuance->id }}"
                                                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">{{ $issuance->return_notes ?? '' }}</textarea>
                                                            </div>
                                                        </div>
                                                        <button type="submit"
                                                            class="gradient-btn w-full mt-4 px-4 py-2 text-xs font-semibold text-white rounded-lg border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300">
                                                            <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                                            <span class="btn-text"><i
                                                                    class="fas fa-undo mr-2"></i>Return
                                                                Equipment</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Pagination Controls -->
                                <div class="pagination-container mt-6">
                                    {{ $issuances->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Current Inventory Table -->
                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up">
                    <div class="p-4 bg-gradient-to-r from-[#90143c] to-[#b01a47]">
                        <h2 class="text-xs font-semibold text-white">Current Inventory</h2>
                    </div>
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0 sm:space-x-4 w-full">
                            <div class="relative w-full sm:w-64">
                                <input id="searchInput" type="text" placeholder="Search staff or equipment..."
                                    class="border border-gray-300 rounded-lg px-3 py-2 text-xs w-full focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                <div class="absolute left-2 top-2.5 text-[#00553d]">
                                    <i class="fas fa-search text-xs"></i>
                                </div>
                            </div>
                            <select id="departmentFilter"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-xs w-full sm:w-48 focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                <option value="">All Departments</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->name }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            <select id="statusFilter"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-xs w-full sm:w-48 focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                <option value="">All Statuses</option>
                                <option value="available">Available</option>
                                <option value="in_use">In Use</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                        <button id="exportBtn"
                            class="gradient-btn px-4 py-2 text-xs font-semibold text-white rounded-lg border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300">
                            <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                            <span class="btn-text"><i class="fas fa-file-export mr-2"></i>Export CSV</span>
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto divide-y divide-[#ffcc34]" aria-label="Current Inventory">
                            <thead class="bg-[#ffcc34]">
                                <tr>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        Staff</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        Department</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        Equipment</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        Model/Brand</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        Date Issued</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        Serial</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        PR Number</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        Status</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody" class="bg-white divide-y divide-[#ffcc34]">
                                @if ($equipment->isEmpty())
                                    <tr>
                                        <td colspan="9"
                                            class="px-4 py-2 text-center text-xs text-[#00553d] bg-gradient-to-br from-gray-50 to-white rounded-lg border-2 border-dashed border-gray-300">
                                            <div
                                                class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-boxes text-2xl text-gray-400"></i>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2 font-medium">No equipment records
                                                found</p>
                                            <p class="text-[0.6rem] text-gray-400">Add items to the inventory to
                                                get started</p>
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($equipment as $item)
                                        <tr class="hover:bg-gray-50 slide-up">
                                            <td class="px-4 py-2 whitespace-nowrap text-xs text-[#00553d]">
                                                {{ $item->staff_name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-xs text-[#00553d]">
                                                {{ $item->department->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-xs text-[#00553d]">
                                                {{ $item->equipment_name }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-xs text-[#00553d]">
                                                {{ $item->model_brand }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-xs text-[#00553d]">
                                                @if ($item->date_issued instanceof \Carbon\Carbon)
                                                    {{ $item->date_issued->format('Y-m-d') }}
                                                @elseif (is_string($item->date_issued) &&
                                                        !empty($item->date_issued) &&
                                                        \Carbon\Carbon::canBeCreatedFromFormat($item->date_issued, 'Y-m-d'))
                                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', $item->date_issued)->format('Y-m-d') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-xs text-[#00553d]">
                                                {{ $item->serial_number }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-xs text-[#00553d]">
                                                {{ $item->pr_number }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <span class="flex items-center">
                                                    <span class="status-indicator status-{{ $item->status }}"></span>
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status == 'in_use' ? 'bg-green-100 text-green-700' : ($item->status == 'maintenance' ? 'bg-yellow-100 text-yellow-700' : ($item->status == 'damaged' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                    </span>
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap flex space-x-2">
                                                <a href="{{ route('inventory.show', $item->id) }}"
                                                    class="text-[#00553d] hover:text-[#007a52] px-2 py-1 rounded-md hover:bg-blue-50 transition-all duration-200 text-[0.6rem] font-semibold border border-blue-200 hover:border-blue-300"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button data-id="{{ $item->id }}"
                                                    class="edit-inventory-btn text-[#00553d] hover:text-[#007a52] px-2 py-1 rounded-md hover:bg-blue-50 transition-all duration-200 text-[0.6rem] font-semibold border border-blue-200 hover:border-blue-300"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('inventory.destroy', $item->id) }}"
                                                    method="POST" class="inline delete-inventory-form"
                                                    data-name="{{ $item->equipment_name }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="delete-inventory-btn text-[#90143c] hover:text-[#b01a47] px-2 py-1 rounded-md hover:bg-red-50 transition-all duration-200 text-[0.6rem] font-semibold border border-red-200 hover:border-red-300"
                                                        title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-container mt-4">
                        {{ $equipment->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

            <!-- History Log Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up">
                <div class="p-4 bg-gradient-to-r from-[#90143c] to-[#b01a47]">
                    <h2 class="text-xs font-semibold text-white">History Log</h2>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto divide-y divide-[#ffcc34]" aria-label="History Log">
                            <thead class="bg-[#ffcc34]">
                                <tr>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        Action</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        Date</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        User</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        Staff</th>
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                        Description</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody" class="bg-white divide-y divide-[#ffcc34]">
                                @if ($historyLogs->isEmpty())
                                    <tr>
                                        <td colspan="5"
                                            class="px-4 py-2 text-center text-xs text-[#00553d] bg-gradient-to-br from-gray-50 to-white rounded-lg border-2 border-dashed border-gray-300">
                                            <div
                                                class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-history text-2xl text-gray-400"></i>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2 font-medium">No history records
                                                found</p>
                                            <p class="text-[0.6rem] text-gray-400">Actions will appear here once
                                                performed</p>
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($historyLogs as $log)
                                        <tr class="hover:bg-gray-50 slide-up">
                                            <td class="px-4 py-2 whitespace-nowrap text-xs text-[#00553d]">
                                                {{ $log->action }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-xs text-[#00553d]">
                                                @if ($log->action_date instanceof \Carbon\Carbon)
                                                    {{ $log->action_date->format('Y-m-d') }}
                                                @elseif (is_string($log->action_date) &&
                                                        !empty($log->action_date) &&
                                                        \Carbon\Carbon::canBeCreatedFromFormat($log->action_date, 'Y-m-d'))
                                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', $log->action_date)->format('Y-m-d') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-xs text-[#00553d]">
                                                {{ $log->user->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-xs text-[#00553d]">
                                                {{ $log->staff->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 text-xs text-[#00553d]">{{ $log->description }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-container mt-4">
                        {{ $historyLogs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

            <!-- Equipment Issuance Graph -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up">
                <div class="p-4 bg-gradient-to-r from-[#90143c] to-[#b01a47]">
                    <h2 class="text-xs font-semibold text-white">Equipment Issuance Statistics</h2>
                </div>
                <div class="p-4">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
                        <h3 class="text-xs font-semibold text-[#00553d] flex items-center">
                            <div class="p-1.5 bg-gradient-to-br from-[#00553d] to-[#007a52] rounded-md mr-2">
                                <i class="fas fa-filter text-white text-xs"></i>
                            </div>
                            Filter Chart Data
                        </h3>
                        <select id="chart-time-filter"
                            class="px-3 py-2 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-36">
                            <option value="month" {{ request('chart_time', 'month') == 'month' ? 'selected' : '' }}>
                                This Month
                            </option>
                            <option value="week" {{ request('chart_time') == 'week' ? 'selected' : '' }}>This
                                Week</option>
                            <option value="year" {{ request('chart_time') == 'year' ? 'selected' : '' }}>This
                                Year</option>
                        </select>
                    </div>
                    <canvas id="equipmentChart" class="w-full h-64"
                        data-equipment="{{ json_encode($equipmentData) }}"></canvas>
                </div>
            </div>
        </div>

        <!-- Edit Inventory Modal -->
        <div id="edit-inventory-modal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-xl p-6 w-full max-w-md border border-[#ffcc34] shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-semibold text-[#00553d] flex items-center">
                        <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md mr-2">
                            <i class="fas fa-edit text-white text-xs"></i>
                        </div>
                        Edit Inventory Item
                    </h3>
                    <button onclick="document.getElementById('edit-inventory-modal').classList.add('hidden')"
                        class="text-[#90143c] hover:text-[#b01a47] transition-all duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="edit-inventory-form" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="mb-4 space-y-3">
                        <div class="relative group">
                            <label for="edit_staff_name"
                                class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Staff Name *</label>
                            <input type="text" name="staff_name" id="edit_staff_name" required
                                class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] group-hover:shadow-md transition-all duration-300">
                            <div
                                class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                <i class="fas fa-user text-xs"></i>
                            </div>
                        </div>
                        <div class="relative group">
                            <label for="edit_equipment_name"
                                class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Equipment Name
                                *</label>
                            <input type="text" name="equipment_name" id="edit_equipment_name" required
                                class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] group-hover:shadow-md transition-all duration-300">
                            <div
                                class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                <i class="fas fa-box text-xs"></i>
                            </div>
                        </div>
                        <div class="relative group">
                            <label for="edit_model_brand"
                                class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Model/Brand *</label>
                            <input type="text" name="model_brand" id="edit_model_brand" required
                                class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] group-hover:shadow-md transition-all duration-300">
                            <div
                                class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                <i class="fas fa-tag text-xs"></i>
                            </div>
                        </div>
                        <div class="relative group">
                            <label for="edit_serial_number"
                                class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Serial Number
                                *</label>
                            <input type="text" name="serial_number" id="edit_serial_number" required
                                class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] group-hover:shadow-md transition-all duration-300">
                            <div
                                class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                <i class="fas fa-barcode text-xs"></i>
                            </div>
                        </div>
                        <div class="relative group">
                            <label for="edit_pr_number"
                                class="block text-[0.65rem] font-medium text-[#00553d] mb-1">PR Number *</label>
                            <input type="text" name="pr_number" id="edit_pr_number" required
                                class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] group-hover:shadow-md transition-all duration-300">
                            <div
                                class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                <i class="fas fa-hashtag text-xs"></i>
                            </div>
                        </div>
                        <div class="relative group">
                            <label for="edit_date_issued"
                                class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Date Issued *</label>
                            <input type="date" name="date_issued" id="edit_date_issued" required
                                class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] group-hover:shadow-md transition-all duration-300">
                            <div
                                class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                <i class="fas fa-calendar-alt text-xs"></i>
                            </div>
                        </div>
                        <div>
                            <label for="edit_status"
                                class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Status *</label>
                            <select name="status" id="edit_status"
                                class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                <option value="available">Available</option>
                                <option value="in_use">In Use</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button"
                            class="px-4 py-2 text-xs font-semibold text-[#00553d] bg-gray-100 rounded-lg hover:bg-gray-200 border border-gray-200 transition-all duration-200"
                            onclick="document.getElementById('edit-inventory-modal').classList.add('hidden')">Cancel</button>
                        <button type="submit" id="edit-inventory-submit"
                            class="gradient-btn px-4 py-2 text-xs font-semibold text-white rounded-lg border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300">
                            <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                            <span class="btn-text"><i class="fas fa-save mr-2"></i>Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript Dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        window.checkDuplicatesUrl = "{{ route('inventory.check-duplicates') }}";

        document.addEventListener('DOMContentLoaded', function() {
            // Utility Functions
            function setLoadingState(button, isLoading) {
                if (isLoading) {
                    button.classList.add('btn-loading');
                    button.disabled = true;
                } else {
                    button.classList.remove('btn-loading');
                    button.disabled = false;
                }
            }

            function showAlert(message, type) {
                const alertContainer = document.getElementById('alert-container');
                const alertDiv = document.createElement('div');
                alertDiv.className = `p-3 rounded-lg text-xs flex items-center space-x-2 ${
                type === 'error' ? 'bg-red-100 text-red-700 border-red-200' : 
                type === 'success' ? 'bg-green-100 text-green-700 border-green-200' : 
                'bg-blue-100 text-blue-700 border-blue-200'
            } border fade-in`;
                alertDiv.innerHTML = `
                <i class="fas ${
                    type === 'error' ? 'fa-exclamation-circle' : 
                    type === 'success' ? 'fa-check-circle' : 
                    'fa-info-circle'
                } text-base"></i>
                <span>${message}</span>
            `;
                alertContainer.appendChild(alertDiv);
                setTimeout(() => {
                    alertDiv.classList.remove('fade-in');
                    alertDiv.classList.add('fade-out');
                    setTimeout(() => alertDiv.remove(), 300);
                }, 3000);
            }

            // Initialize Accordions
            function initializeAccordions() {
                const accordionToggles = document.querySelectorAll('.accordion-toggle');
                accordionToggles.forEach(toggle => {
                    toggle.addEventListener('click', function() {
                        const target = document.getElementById(toggle.dataset.target);
                        const icon = toggle.querySelector('.accordion-icon');
                        target.classList.toggle('open');
                        icon.classList.toggle('rotate-180');
                        if (target.classList.contains('open')) {
                            setTimeout(() => {
                                target.style.animation = 'fadeIn 0.3s ease-out';
                            }, 100);
                        }
                    });
                });
            }

            // Initialize Equipment Toggles
            function initializeEquipmentToggles() {
                const equipmentToggles = document.querySelectorAll('.equipment-toggle');
                equipmentToggles.forEach(toggle => {
                    toggle.addEventListener('click', function() {
                        const content = toggle.nextElementSibling;
                        content.classList.toggle('open');
                        const svg = toggle.querySelector('svg');
                        svg.classList.toggle('rotate-180');
                    });
                });
            }

            // Initialize Staff Count
            function initializeStaffCount() {
                console.log('Staff count initialized');
            }

            // Initialize Search and Filters
            function initializeSearchAndFilters() {
                const searchInput = document.getElementById('searchInput');
                const departmentFilter = document.getElementById('departmentFilter');
                const statusFilter = document.getElementById('statusFilter');
                const returnSearch = document.getElementById('returnSearch');
                const returnDepartmentFilter = document.getElementById('returnDepartmentFilter');
                const returnStatusFilter = document.getElementById('returnStatusFilter');

                // Log missing elements for debugging
                if (!searchInput) console.error('Element with ID "searchInput" not found');
                if (!departmentFilter) console.error('Element with ID "departmentFilter" not found');
                if (!statusFilter) console.error('Element with ID "statusFilter" not found');
                if (!returnSearch) console.error('Element with ID "returnSearch" not found');
                if (!returnDepartmentFilter) console.error(
                    'Element with ID "returnDepartmentFilter" not found');
                if (!returnStatusFilter) console.error('Element with ID "returnStatusFilter" not found');

                function filterInventory() {
                    if (!searchInput || !departmentFilter || !statusFilter) return;
                    const search = searchInput.value.toLowerCase();
                    const department = departmentFilter.value;
                    const status = statusFilter.value;
                    const rows = document.querySelectorAll('#inventoryTableBody tr');

                    rows.forEach(row => {
                        const staff = row.cells[0].textContent.toLowerCase();
                        const dept = row.cells[1].textContent;
                        const equipment = row.cells[2].textContent.toLowerCase();
                        const statusText = row.cells[7].textContent.toLowerCase();
                        const show = (
                            (search === '' || staff.includes(search) || equipment.includes(
                                search)) &&
                            (department === '' || dept === department) &&
                            (status === '' || statusText.includes(status.toLowerCase()))
                        );
                        row.style.display = show ? '' : 'none';
                    });
                }

                function filterReturnEquipment() {
                    if (!returnSearch || !returnDepartmentFilter || !returnStatusFilter) return;
                    const search = returnSearch.value.toLowerCase();
                    const department = returnDepartmentFilter.value;
                    const equipmentType = returnStatusFilter.value;
                    const accordions = document.querySelectorAll(
                        '#returnEquipmentContainer .equipment-accordion');

                    $.each(accordions, function() {
                        const equipment = this.querySelector('h3').textContent.toLowerCase();
                        const dept = this.querySelector('p').textContent.split(' • ').pop();
                        const show = (
                            (search === '' || equipment.includes(search)) &&
                            (department === '' || dept === department) &&
                            (equipmentType === '' || equipment.includes(equipmentType
                                .toLowerCase()))
                        );
                        this.style.display = show ? '' : 'none';
                    });
                }

                if (searchInput) searchInput.addEventListener('input', filterInventory);
                if (departmentFilter) departmentFilter.addEventListener('change', filterInventory);
                if (statusFilter) statusFilter.addEventListener('change', filterInventory);
                if (returnSearch) returnSearch.addEventListener('input', filterReturnEquipment);
                if (returnDepartmentFilter) returnDepartmentFilter.addEventListener('change',
                    filterReturnEquipment);
                if (returnStatusFilter) returnStatusFilter.addEventListener('change', filterReturnEquipment);
            }

            // Initialize Pagination
            function initializePagination() {
                console.log('Pagination initialized');
            }

            // Initialize Delete Functionality
            function initializeDeleteFunctionality() {
                const deleteButtons = document.querySelectorAll('.delete-inventory-btn');
                deleteButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const form = btn.closest('form');
                        const equipmentName = btn.closest('.delete-inventory-form').dataset.name;
                        Swal.fire({
                            title: 'Delete Inventory Item?',
                            html: `
                            <div class="text-left space-y-3">
                                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <i class="fas fa-exclamation-triangle text-red-500 text-base"></i>
                                        <span class="font-semibold text-red-800 text-xs">Permanent Deletion Warning</span>
                                    </div>
                                    <div class="text-xs text-red-700">
                                        You are about to permanently delete "<strong>${equipmentName}</strong>".
                                    </div>
                                </div>
                                <div class="bg-amber-50 p-2 rounded-lg border border-amber-200">
                                    <div class="text-xs text-amber-700">
                                        <strong>Impact:</strong>
                                        <ul class="mt-1 space-y-1 text-[0.6rem]">
                                            <li>• This action cannot be undone</li>
                                            <li>• Related records will be updated</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="text-center text-xs text-gray-600">
                                    Type "<strong>DELETE</strong>" to confirm:
                                </div>
                            </div>
                        `,
                            input: 'text',
                            inputPlaceholder: 'Type DELETE to confirm',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, Delete',
                            cancelButtonText: '<i class="fas fa-shield-alt mr-2"></i>Keep Safe',
                            inputValidator: function(value) {
                                if (value !== 'DELETE') {
                                    return 'Please type "DELETE" exactly to confirm';
                                }
                            },
                            customClass: {
                                title: 'text-xs',
                                content: 'text-[0.6rem]'
                            }
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            }

            // Initialize Form Submission
            function initializeFormSubmission() {
                const issueForm = document.getElementById('issueForm');
                if (issueForm) {
                    issueForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        const payload = Object.fromEntries(formData.entries());
                        const submitButton = this.querySelector('button[type="submit"]');

                        if (!payload.serial_number || !payload.pr_number) {
                            showAlert('Serial number and PR number are required.', 'error');
                            return;
                        }

                        setLoadingState(submitButton, true);

                        try {
                            const checkResponse = await fetch(window.checkDuplicatesUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    serial_number: payload.serial_number,
                                    pr_number: payload.pr_number
                                })
                            });

                            if (!checkResponse.ok) throw new Error(
                                `HTTP error! Status: ${checkResponse.status}`);

                            const checkData = await checkResponse.json();
                            if (checkData.serial_exists || checkData.pr_exists) {
                                let message = 'Potential duplicates found:<br>';
                                if (checkData.serial_exists) message +=
                                    `• Serial Number "${payload.serial_number}" exists<br>`;
                                if (checkData.pr_exists) message +=
                                    `• PR Number "${payload.pr_number}" exists<br>`;

                                const result = await Swal.fire({
                                    title: 'Duplicate Detected',
                                    html: `
                                    <div class="text-left space-y-3">
                                        <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                                            <div class="text-xs text-yellow-700">${message}</div>
                                        </div>
                                    </div>
                                `,
                                    icon: 'warning',
                                    showCancelButton: !checkData.serial_exists,
                                    confirmButtonColor: '#00553d',
                                    cancelButtonColor: '#90143c',
                                    confirmButtonText: checkData.serial_exists ? 'OK' :
                                        '<i class="fas fa-check mr-2"></i>Proceed Anyway',
                                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                                    customClass: {
                                        title: 'text-xs',
                                        content: 'text-[0.6rem]'
                                    }
                                });

                                if (result.isConfirmed && !checkData.serial_exists) {
                                    issueForm.submit();
                                } else {
                                    setLoadingState(submitButton, false);
                                }
                            } else {
                                const result = await Swal.fire({
                                    title: 'Issue Equipment?',
                                    html: `
                                    <div class="text-left space-y-3">
                                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                            <div class="font-semibold text-blue-800 mb-2">Issuance Details:</div>
                                            <div class="text-xs text-blue-700">
                                                <div class="flex justify-between items-center">
                                                    <span>Staff Name:</span>
                                                    <span class="font-medium">${payload.staff_name}</span>
                                                </div>
                                                <div class="flex justify-between items-center mt-1">
                                                    <span>Equipment:</span>
                                                    <span class="font-medium">${payload.equipment_name}</span>
                                                </div>
                                                <div class="flex justify-between items-center mt-1">
                                                    <span>Serial Number:</span>
                                                    <span class="font-medium">${payload.serial_number}</span>
                                                </div>
                                                <div class="flex justify-between items-center mt-1">
                                                    <span>PR Number:</span>
                                                    <span class="font-medium">${payload.pr_number}</span>
                                                </div>
                                                <div class="flex justify-between items-center mt-1">
                                                    <span>Status:</span>
                                                    <span class="font-medium">${payload.status.charAt(0).toUpperCase() + payload.status.slice(1)}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `,
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#00553d',
                                    cancelButtonColor: '#90143c',
                                    confirmButtonText: '<i class="fas fa-plus mr-2"></i>Issue Equipment',
                                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                                    customClass: {
                                        title: 'text-xs',
                                        content: 'text-[0.6rem]'
                                    }
                                });

                                if (result.isConfirmed) {
                                    issueForm.submit();
                                } else {
                                    setLoadingState(submitButton, false);
                                }
                            }
                        } catch (error) {
                            setLoadingState(submitButton, false);
                            showAlert('Failed to validate data. Please try again.', 'error');
                        }
                    });
                }

                const returnForms = document.querySelectorAll('#returnEquipmentContainer form');
                returnForms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        const payload = Object.fromEntries(formData.entries());
                        const submitButton = this.querySelector('button[type="submit"]');

                        Swal.fire({
                            title: 'Return Equipment?',
                            html: `
                            <div class="text-left space-y-3">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                    <div class="font-semibold text-blue-800 mb-2">Return Details:</div>
                                    <div class="text-xs text-blue-700">
                                        <div class="flex justify-between items-center">
                                            <span>Date Returned:</span>
                                            <span class="font-medium">${payload.date_returned}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>Condition:</span>
                                            <span class="font-medium">${payload.condition.charAt(0).toUpperCase() + payload.condition.slice(1)}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>Remarks:</span>
                                            <span class="font-medium">${payload.remarks || 'N/A'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#00553d',
                            cancelButtonColor: '#90143c',
                            confirmButtonText: '<i class="fas fa-undo mr-2"></i>Return Equipment',
                            cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                            customClass: {
                                title: 'text-xs',
                                content: 'text-[0.6rem]'
                            }
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                setLoadingState(submitButton, true);
                                form.submit();
                            }
                        });
                    });
                });
            }

            // Initialize Edit Functionality
            function initializeEditFunctionality() {
                const editButtons = document.querySelectorAll('.edit-inventory-btn');
                editButtons.forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const id = btn.dataset.id;
                        try {
                            const response = await fetch(`/inventory/${id}`, {
                                method: 'GET',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                }
                            });
                            if (!response.ok) throw new Error(
                                `HTTP error! Status: ${response.status}`);
                            const item = await response.json();

                            const modal = document.getElementById('edit-inventory-modal');
                            const form = document.getElementById('edit-inventory-form');
                            form.action = `{{ route('inventory.update', ':id') }}`.replace(
                                ':id', id);
                            document.getElementById('edit_staff_name').value = item
                                .staff_name || '';
                            document.getElementById('edit_equipment_name').value = item
                                .equipment_name || '';
                            document.getElementById('edit_model_brand').value = item
                                .model_brand || '';
                            document.getElementById('edit_serial_number').value = item
                                .serial_number || '';
                            document.getElementById('edit_pr_number').value = item.pr_number ||
                                '';
                            document.getElementById('edit_date_issued').value = item
                                .date_issued || '';
                            document.getElementById('edit_status').value = item.status ||
                                'available';
                            modal.classList.remove('hidden');
                        } catch (error) {
                            showAlert('Failed to load item data. Please try again.', 'error');
                        }
                    });
                });

                const editForm = document.getElementById('edit-inventory-form');
                const editSubmit = document.getElementById('edit-inventory-submit');
                if (editForm) {
                    editForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        const payload = Object.fromEntries(formData.entries());
                        if (!payload.staff_name || !payload.equipment_name || !payload.model_brand || !
                            payload.serial_number || !payload.pr_number || !payload.date_issued) {
                            showAlert('Please fill all required fields', 'error');
                            return;
                        }

                        const result = await Swal.fire({
                            title: 'Update Inventory Item?',
                            html: `
                            <div class="text-left space-y-3">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                    <div class="font-semibold text-blue-800 mb-2">Updated Inventory Details:</div>
                                    <div class="text-xs text-blue-700">
                                        <div class="flex justify-between items-center">
                                            <span>Staff Name:</span>
                                            <span class="font-medium">${payload.staff_name}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>Equipment Name:</span>
                                            <span class="font-medium">${payload.equipment_name}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>Model/Brand:</span>
                                            <span class="font-medium">${payload.model_brand}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>Serial Number:</span>
                                            <span class="font-medium">${payload.serial_number}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>PR Number:</span>
                                            <span class="font-medium">${payload.pr_number}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>Date Issued:</span>
                                            <span class="font-medium">${payload.date_issued}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>Status:</span>
                                            <span class="font-medium">${payload.status.charAt(0).toUpperCase() + payload.status.slice(1)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#00553d',
                            cancelButtonColor: '#90143c',
                            confirmButtonText: '<i class="fas fa-save mr-2"></i>Update Item',
                            cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                            customClass: {
                                title: 'text-xs',
                                content: 'text-[0.6rem]'
                            }
                        });

                        if (result.isConfirmed) {
                            setLoadingState(editSubmit, true);
                            try {
                                const checkResponse = await fetch(window.checkDuplicatesUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        serial_number: payload.serial_number,
                                        pr_number: payload.pr_number,
                                        exclude_id: editForm.action.split('/').pop()
                                    })
                                });

                                if (!checkResponse.ok) throw new Error(
                                    `HTTP error! Status: ${checkResponse.status}`);

                                const checkData = await checkResponse.json();
                                if (checkData.serial_exists || checkData.pr_exists) {
                                    let message = 'Potential duplicates found:<br>';
                                    if (checkData.serial_exists) message +=
                                        `• Serial Number "${payload.serial_number}" exists<br>`;
                                    if (checkData.pr_exists) message +=
                                        `• PR Number "${payload.pr_number}" exists<br>`;

                                    const dupeResult = await Swal.fire({
                                        title: 'Duplicate Detected',
                                        html: `
                                        <div class="text-left space-y-3">
                                            <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                                                <div class="text-xs text-yellow-700">${message}</div>
                                            </div>
                                        </div>
                                    `,
                                        icon: 'warning',
                                        showCancelButton: !checkData.serial_exists,
                                        confirmButtonColor: '#00553d',
                                        cancelButtonColor: '#90143c',
                                        confirmButtonText: checkData.serial_exists ? 'OK' :
                                            '<i class="fas fa-check mr-2"></i>Proceed Anyway',
                                        cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                                        customClass: {
                                            title: 'text-xs',
                                            content: 'text-[0.6rem]'
                                        }
                                    });

                                    if (dupeResult.isConfirmed && !checkData.serial_exists) {
                                        editForm.submit();
                                    } else {
                                        setLoadingState(editSubmit, false);
                                    }
                                } else {
                                    editForm.submit();
                                }
                            } catch (error) {
                                setLoadingState(editSubmit, false);
                                showAlert('Failed to validate data. Please try again.', 'error');
                            }
                        } else {
                            setLoadingState(editSubmit, false);
                        }
                    });
                }
            }

            // Initialize Export
            function initializeExport() {
                const exportBtn = document.getElementById('exportBtn');
                if (exportBtn) {
                    exportBtn.addEventListener('click', function() {
                        Swal.fire({
                            title: 'Export Inventory?',
                            text: 'This will generate a CSV file of the current inventory.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#00553d',
                            cancelButtonColor: '#90143c',
                            confirmButtonText: '<i class="fas fa-file-export mr-2"></i>Export CSV',
                            cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                            customClass: {
                                title: 'text-xs',
                                content: 'text-[0.6rem]'
                            }
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                setLoadingState(exportBtn, true);
                                window.location.href = "{{ route('inventory.export.csv') }}";
                                setTimeout(() => setLoadingState(exportBtn, false), 2000);
                            }
                        });
                    });
                }
            }

            // Initialize Chart
            let chartInstance = null;

            function initializeChart() {
                const ctx = document.getElementById('equipmentChart');
                if (ctx) {
                    try {
                        const equipmentDataAttr = ctx.dataset.equipment;
                        if (!equipmentDataAttr) {
                            console.log('No equipment data found in canvas dataset');
                            ctx.style.display = 'none';
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

                        chartInstance = new Chart(ctx, {
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
                                            font: {
                                                size: 10
                                            },
                                            color: '#00553d'
                                        },
                                        ticks: {
                                            color: '#00553d',
                                            font: {
                                                size: 10
                                            }
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Equipment Type',
                                            font: {
                                                size: 10
                                            },
                                            color: '#00553d'
                                        },
                                        ticks: {
                                            color: '#00553d',
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
                                            color: '#00553d'
                                        }
                                    }
                                }
                            }
                        });
                    } catch (error) {
                        console.error('Error initializing chart:', error);
                        ctx.style.display = 'none';
                    }
                }

                const chartTimeFilter = document.getElementById('chart-time-filter');
                if (chartTimeFilter) {
                    chartTimeFilter.addEventListener('change', async function() {
                        const timeFilter = chartTimeFilter.value;
                        try {
                            const response = await fetch(
                                `/inventory/chart-data?chart_time=${timeFilter}`, {
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                });
                            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                            const newData = await response.json();
                            const labels = Object.keys(newData);
                            const data = Object.values(newData);

                            if (chartInstance) {
                                chartInstance.data.labels = labels;
                                chartInstance.data.datasets[0].data = data;
                                chartInstance.update();
                            }

                            Swal.fire({
                                title: 'Chart Filter Applied',
                                text: `Chart updated to show data for: ${timeFilter.charAt(0).toUpperCase() + timeFilter.slice(1)}`,
                                icon: 'info',
                                confirmButtonColor: '#00553d',
                                confirmButtonText: '<i class="fas fa-check mr-2"></i>OK',
                                customClass: {
                                    title: 'text-xs',
                                    content: 'text-[0.6rem]'
                                }
                            });
                        } catch (error) {
                            showAlert('Failed to update chart data. Please try again.', 'error');
                        }
                    });
                }
            }

            // Update Staff Count
            function updateStaffCount() {
                console.log('Updating staff count...');
            }

            // Initialize All Components
            initializeAccordions();
            initializeEquipmentToggles();
            initializeStaffCount();
            initializeSearchAndFilters();
            initializePagination();
            initializeDeleteFunctionality();
            initializeFormSubmission();
            initializeEditFunctionality();
            initializeExport();
            initializeChart();

            // Update staff count periodically
            setInterval(updateStaffCount, 30000);
        });
    </script>
    </div>

    <x-auth-footer />
</x-app-layout>

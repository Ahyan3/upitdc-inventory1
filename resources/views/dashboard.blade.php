<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-[#00553d] leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

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

        .status-active {
            background-color: #10b981;
        }

        .status-warning {
            background-color: #f59e0b;
        }

        .status-inactive {
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

    <div class="flex min-h-screen bg-gray-50">
        <button id="toggleSidebar"
            class="md:hidden fixed top-3 left-3 z-50 bg-[#90143c] text-white p-1.5 rounded-md border border-[#ffcc34] shadow-md hover:shadow-lg transition-all duration-200">
            <i class="fas fa-bars text-xs"></i>
        </button>

        <div class="flex-1 container mx-auto px-3 py-6 max-w-5xl">
            <div class="text-center mb-8 fade-in">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-full mb-4 shadow-lg relative">
                    <i class="fas fa-tachometer-alt text-white text-xl animate-spin" style="animation-duration: 8s;"></i>
                    <div
                        class="absolute inset-0 rounded-full bg-gradient-to-br from-[#90143c] to-[#b01a47] opacity-20 animate-ping">
                    </div>
                </div>
                <h1
                    class="text-2xl font-bold bg-gradient-to-r from-[#90143c] to-[#00553d] bg-clip-text text-transparent">
                    Dashboard</h1>
                <p class="text-xs text-[#00553d] opacity-80 max-w-sm mx-auto">Welcome, {{ auth()->user()->name }}</p>
                {{--  <a href="{{ route('dashboard.export.csv') }}"
                    class="gradient-btn text-white font-semibold py-2 px-4 rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg inline-flex items-center mt-4 transition-all duration-300">
                    <i class="fas fa-file-export mr-2"></i>Export CSV
                </a>  --}}
            </div>

            <div id="alert-container" class="mb-4"></div>

            <div class="space-y-4">
                <!-- Stats Section -->
                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center p-4 gradient-btn text-white transition-all duration-500"
                        data-target="stats-section">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 bg-white/20 rounded-md">
                                <i class="fas fa-chart-bar text-base"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-semibold block">System Statistics</span>
                                <span class="text-xs opacity-80">Overview of inventory metrics</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="status-indicator status-active"></span>
                            <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="stats-section" class="accordion-content open">
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">
                                <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                                    <div class="text-base font-bold text-[#90143c]">{{ $totalStaff ?? 0 }}</div>
                                    <div class="text-xs text-gray-600">Total Staff</div>
                                    <a href="{{ route('staff.index') }}"
                                        class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] mt-1 block">View
                                        Details</a>
                                </div>
                                <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                                    <div class="text-base font-bold text-[#90143c]">{{ $totalIssuedEquipment ?? 0 }}
                                    </div>
                                    <div class="text-xs text-gray-600">Issued Equipment</div>
                                    <a href="{{ route('inventory') }}"
                                        class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] mt-1 block">View
                                        Details</a>
                                </div>
                                <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                                    <div class="text-base font-bold text-[#90143c]">{{ $totalReturnedEquipment ?? 0 }}
                                    </div>
                                    <div class="text-xs text-gray-600">Returned Equipment</div>
                                    <a href="{{ route('inventory') }}"
                                        class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] mt-1 block">View
                                        Details</a>
                                </div>
                                <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                                    <div class="text-base font-bold text-[#90143c] flex items-center">
                                        {{ $pendingRequests ?? 0 }}
                                        @if ($pendingRequests > 5)
                                            <span
                                                class="ml-2 bg-[#90143c] text-white text-[0.6rem] px-1.5 py-0.5 rounded-full">High</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-600">Pending Requests</div>
                                    <a href="{{ route('inventory') }}"
                                        class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] mt-1 block">View
                                        Details</a>
                                </div>
                                <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                                    <div class="text-base font-bold text-[#90143c]">{{ $activeIssuances ?? 0 }}</div>
                                    <div class="text-xs text-gray-600">Active Issuances</div>
                                    <a href="{{ route('inventory') }}"
                                        class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] mt-1 block">View
                                        Details</a>
                                </div>
                            </div>
                            <div
                                class="bg-gradient-to-br from-blue-50 to-purple-50 p-4 rounded-lg border border-blue-200 relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-200/30 to-purple-200/30 rounded-full -translate-y-12 translate-x-12">
                                </div>
                                <h3 class="text-xs font-bold text-[#00553d] mb-3 flex items-center relative z-10">
                                    <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md mr-2">
                                        <i class="fas fa-filter text-white text-xs"></i>
                                    </div>
                                    Filter Statistics
                                    <span
                                        class="ml-auto text-[0.6rem] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full">Quick
                                        Filter</span>
                                </h3>
                                <form id="stats-filter-form" method="GET" action="{{ route('dashboard') }}"
                                    class="flex flex-col md:flex-row gap-3 relative z-10">
                                    <input type="text" name="stats_search" value="{{ request('stats_search') }}"
                                        placeholder="Search stats..."
                                        class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full">
                                    <select name="time_filter"
                                        class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full md:w-36">
                                        <option value="all" {{ request('time_filter') == 'all' ? 'selected' : '' }}>
                                            All Time</option>
                                        <option value="day" {{ request('time_filter') == 'day' ? 'selected' : '' }}>
                                            Today</option>
                                        <option value="week"
                                            {{ request('time_filter') == 'week' ? 'selected' : '' }}>This Week</option>
                                        <option value="month"
                                            {{ request('time_filter') == 'month' ? 'selected' : '' }}>This Month
                                        </option>
                                        <option value="year"
                                            {{ request('time_filter') == 'year' ? 'selected' : '' }}>This Year</option>
                                    </select>
                                    <select name="type_filter"
                                        class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full md:w-36">
                                        <option value="total"
                                            {{ request('type_filter') == 'total' ? 'selected' : '' }}>Total</option>
                                        <option value="active"
                                            {{ request('type_filter') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="returned"
                                            {{ request('type_filter') == 'returned' ? 'selected' : '' }}>Returned
                                        </option>
                                        <option value="overdue"
                                            {{ request('type_filter') == 'overdue' ? 'selected' : '' }}>Not Returned
                                        </option>
                                        <option value="lost"
                                            {{ request('type_filter') == 'lost' ? 'selected' : '' }}>Lost</option>
                                    </select>
                                    <button type="submit"
                                        class="gradient-btn px-6 py-3 text-white font-semibold rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300">
                                        <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                        <span class="btn-text"><i class="fas fa-filter mr-2"></i>Filter</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Issuances Section -->
                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center p-4 gradient-btn text-white transition-all duration-500"
                        data-target="issuances-section">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 bg-white/20 rounded-md">
                                <i class="fas fa-list text-base"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-semibold block">Recent Issuances</span>
                                <span class="text-xs opacity-80">Track recent equipment assignments</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="bg-white bg-opacity-30 text-xs px-2 py-0.5 rounded-full font-medium">{{ $issuances->total() ?? 'N/A' }}
                                issuances</span>
                            <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300 rotate-180"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="issuances-section" class="accordion-content open">
                        <div class="p-4">
                            <div
                                class="bg-gradient-to-br from-blue-50 to-purple-50 p-4 rounded-lg border border-blue-200 mb-4 relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-200/30 to-purple-200/30 rounded-full -translate-y-12 translate-x-12">
                                </div>
                                <h3 class="text-xs font-bold text-[#00553d] mb-3 flex items-center relative z-10">
                                    <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md mr-2">
                                        <i class="fas fa-plus-circle text-white text-xs"></i>
                                    </div>
                                    Add New Issuance
                                    <span
                                        class="ml-auto text-[0.6rem] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full">Quick
                                        Add</span>
                                </h3>
                                <button id="add-issuance-btn"
                                    class="gradient-btn px-6 py-3 text-white font-semibold rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300 relative z-10">
                                    <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                    <span class="btn-text flex items-center"><i class="fas fa-plus mr-2"></i>Add
                                        Issuance</span>
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-[#ffcc34]" aria-label="Recent Issuances">
                                    <thead class="bg-[#ffcc34]">
                                        <tr>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Staff Name</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Department</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Equipment</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Model/Brand</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Serial No.</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                PR No.</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Date Issued</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Date Returned</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Status</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-[#ffcc34]">
                                        @forelse ($issuances as $issuance)
                                            <tr class="hover:bg-gray-50 slide-up">
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $issuance->staff->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $issuance->equipment->department->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $issuance->equipment->equipment_name ?? 'N/A' }}</td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $issuance->equipment->model_brand ?? 'N/A' }}</td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $issuance->equipment->serial_number ?? 'N/A' }}</td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $issuance->equipment->pr_number ?? 'N/A' }}</td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    @if ($issuance->issued_at instanceof \Carbon\Carbon)
                                                        {{ $issuance->issued_at->format('Y-m-d') }}
                                                    @elseif (is_string($issuance->issued_at) &&
                                                            !empty($issuance->issued_at) &&
                                                            \Carbon\Carbon::canBeCreatedFromFormat($issuance->issued_at, 'Y-m-d'))
                                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $issuance->issued_at)->format('Y-m-d') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    @if ($issuance->returned_at instanceof \Carbon\Carbon)
                                                        {{ $issuance->returned_at->format('Y-m-d') }}
                                                    @elseif (is_string($issuance->returned_at) &&
                                                            !empty($issuance->returned_at) &&
                                                            \Carbon\Carbon::canBeCreatedFromFormat($issuance->returned_at, 'Y-m-d'))
                                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $issuance->returned_at)->format('Y-m-d') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-xs">
                                                    <span class="flex items-center">
                                                        <span
                                                            class="status-indicator {{ $issuance->status == 'active' ? 'status-active' : ($issuance->status == 'overdue' ? 'status-inactive' : ($issuance->status == 'lost' ? 'status-warning' : 'status-active')) }}"></span>
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $issuance->status == 'active' ? 'bg-green-100 text-green-700' : ($issuance->status == 'overdue' ? 'bg-red-100 text-red-700' : ($issuance->status == 'lost' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700')) }}">
                                                            {{ $issuance->status === 'overdue' ? 'Not Returned' : ucfirst($issuance->status) }}
                                                        </span>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-xs">
                                                    <button data-id="{{ $issuance->id }}"
                                                        class="edit-issuance-btn text-[#00553d] hover:text-[#007a52] px-3 py-2 rounded-md hover:bg-blue-50 transition-all duration-200 text-[0.6rem] font-semibold border border-blue-200 hover:border-blue-300">
                                                        <i class="fas fa-edit mr-1"></i>Edit
                                                    </button>
                                                    <form action="{{ route('issuances.destroy', $issuance->id) }}"
                                                        method="POST" class="inline delete-issuance-form"
                                                        data-name="{{ $issuance->equipment->equipment_name }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="delete-issuance-btn text-[#90143c] hover:text-[#b01a47] px-3 py-2 rounded-md hover:bg-red-50 transition-all duration-200 text-[0.6rem] font-semibold border border-red-200 hover:border-red-300">
                                                            <i class="fas fa-trash mr-1"></i>Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10"
                                                    class="px-4 py-2 text-center text-xs text-[#00553d] bg-gradient-to-br from-gray-50 to-white rounded-lg border-2 border-dashed border-gray-300">
                                                    <div
                                                        class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                                        <i class="fas fa-list text-2xl text-gray-400"></i>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mb-2 font-medium">No issuances
                                                        found</p>
                                                    <p class="text-[0.6rem] text-gray-400">Create a new issuance using
                                                        the button above</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination-container mt-4">
                                {{ $issuances->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Log Section -->
                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center p-4 gradient-btn text-white transition-all duration-500"
                        data-target="inventory-section">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 bg-white/20 rounded-md">
                                <i class="fas fa-boxes text-base"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-semibold block">Inventory Log</span>
                                <span class="text-xs opacity-80">Manage equipment inventory</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="bg-white bg-opacity-30 text-xs px-2 py-0.5 rounded-full font-medium">{{ $inventory->total() ?? 'N/A' }}
                                items</span>
                            <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300 rotate-180"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="inventory-section" class="accordion-content open">
                        <div class="p-4">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
                                <h3 class="text-xs font-semibold text-[#00553d] flex items-center">
                                    <div class="p-1.5 bg-gradient-to-br from-[#00553d] to-[#007a52] rounded-md mr-2">
                                        <i class="fas fa-filter text-white text-xs"></i>
                                    </div>
                                    Filter Inventory
                                </h3>
                                <form method="GET" action="{{ route('dashboard') }}"
                                    class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <input type="text" name="inventory_search"
                                        value="{{ request('inventory_search') }}" placeholder="Search inventory..."
                                        class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-64">
                                    <select name="inventory_status"
                                        class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-36">
                                        <option value="all"
                                            {{ request('inventory_status') == 'all' ? 'selected' : '' }}>All Status
                                        </option>
                                        <option value="available"
                                            {{ request('inventory_status') == 'available' ? 'selected' : '' }}>
                                            Available</option>
                                        <option value="not_working"
                                            {{ request('inventory_status') == 'not_working' ? 'selected' : '' }}>Not
                                            Working</option>
                                        <option value="working"
                                            {{ request('inventory_status') == 'working' ? 'selected' : '' }}>Working
                                        </option>
                                        <option value="not_returned"
                                            {{ request('inventory_status') == 'not_returned' ? 'selected' : '' }}>Not
                                            Returned</option>
                                        <option value="returned"
                                            {{ request('inventory_status') == 'returned' ? 'selected' : '' }}>Returned
                                        </option>
                                    </select>
                                    <button type="submit"
                                        class="gradient-btn px-6 py-3 text-white font-semibold rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300">
                                        <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                        <span class="btn-text"><i class="fas fa-filter mr-2"></i>Filter</span>
                                    </button>
                                </form>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-[#ffcc34]" aria-label="Inventory Log">
                                    <thead class="bg-[#ffcc34]">
                                        <tr>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Staff Name</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Department</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Equipment</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Model/Brand</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Serial No.</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                PR No.</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Date Issued</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Status</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-[#ffcc34]">
                                        @forelse ($inventory as $item)
                                            <tr class="hover:bg-gray-50 slide-up">
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $item->issuances->first()->staff->name ?? ($item->issuances->isEmpty() ? 'N/A' : 'Unknown') }}
                                                </td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $item->department->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $item->equipment_name }}</td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">{{ $item->model_brand }}
                                                </td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $item->serial_number }}</td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">{{ $item->pr_number }}
                                                </td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
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
                                                <td class="px-4 py-2 text-xs">
                                                    <span class="flex items-center">
                                                        <span
                                                            class="status-indicator {{ $item->status == 'available' ? 'status-active' : ($item->status == 'not_working' ? 'status-inactive' : ($item->status == 'working' ? 'status-active' : ($item->status == 'not_returned' ? 'status-warning' : 'status-active'))) }}"></span>
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : ($item->status == 'not_working' ? 'bg-red-100 text-red-700' : ($item->status == 'working' ? 'bg-green-100 text-green-700' : ($item->status == 'not_returned' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700'))) }}">
                                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                        </span>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-xs">
                                                    <button data-id="{{ $item->id }}"
                                                        class="edit-inventory-btn text-[#00553d] hover:text-[#007a52] px-3 py-2 rounded-md hover:bg-blue-50 transition-all duration-200 text-[0.6rem] font-semibold border border-blue-200 hover:border-blue-300">
                                                        <i class="fas fa-edit mr-1"></i>Edit
                                                    </button>
                                                    <form action="{{ route('inventory.destroy', $item->id) }}"
                                                        method="POST" class="inline delete-inventory-form"
                                                        data-name="{{ $item->equipment_name }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="delete-inventory-btn text-[#90143c] hover:text-[#b01a47] px-3 py-2 rounded-md hover:bg-red-50 transition-all duration-200 text-[0.6rem] font-semibold border border-red-200 hover:border-red-300">
                                                            <i class="fas fa-trash mr-1"></i>Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9"
                                                    class="px-4 py-2 text-center text-xs text-[#00553d] bg-gradient-to-br from-gray-50 to-white rounded-lg border-2 border-dashed border-gray-300">
                                                    <div
                                                        class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                                        <i class="fas fa-boxes text-2xl text-gray-400"></i>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mb-2 font-medium">No inventory
                                                        items found</p>
                                                    <p class="text-[0.6rem] text-gray-400">Add items to the inventory
                                                        to get started</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination-container mt-4">
                                {{ $inventory->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipment Issuance Statistics Section -->
                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center p-4 gradient-btn text-white transition-all duration-500"
                        data-target="chart-section">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 bg-white/20 rounded-md">
                                <i class="fas fa-chart-bar text-base"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-semibold block">Equipment Issuance Statistics</span>
                                <span class="text-xs opacity-80">Visualize issuance trends</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="status-indicator status-active"></span>
                            <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="chart-section" class="accordion-content open">
                        <div class="p-4">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
                                <h3 class="text-xs font-semibold text-[#00553d] flex items-center">
                                    <div class="p-1.5 bg-gradient-to-br from-[#00553d] to-[#007a52] rounded-md mr-2">
                                        <i class="fas fa-filter text-white text-xs"></i>
                                    </div>
                                    Filter Chart Data
                                </h3>
                                <select name="chart_time" id="chart-time-filter"
                                    class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-36">
                                    <option value="month"
                                        {{ request('chart_time', 'month') == 'month' ? 'selected' : '' }}>This Month
                                    </option>
                                    <option value="week" {{ request('chart_time') == 'week' ? 'selected' : '' }}>
                                        This Week</option>
                                    <option value="year" {{ request('chart_time') == 'year' ? 'selected' : '' }}>
                                        This Year</option>
                                </select>
                            </div>
                            <canvas id="equipmentChart" class="w-full h-64"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Issuance Modal -->
    <div id="add-issuance-modal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md border border-[#ffcc34] shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-[#00553d] flex items-center">
                    <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md mr-2">
                        <i class="fas fa-plus-circle text-white text-xs"></i>
                    </div>
                    Add Issuance
                </h3>
                <button onclick="document.getElementById('add-issuance-modal').classList.add('hidden')"
                    class="text-[#90143c] hover:text-[#b01a47] transition-all duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="add-issuance-form" method="POST" action="{{ route('issuances.store') }}">
                @csrf
                <div class="mb-4 space-y-3">
                    <div class="relative group">
                        <input type="text" name="staff_id" placeholder="Enter Staff ID" required
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                    </div>
                    <div class="relative group">
                        <input type="text" name="equipment_id" placeholder="Enter Equipment ID" required
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-box text-xs"></i>
                        </div>
                    </div>
                    <div class="relative group">
                        <input type="text" name="issued_at" placeholder="Date Issued (YYYY-MM-DD)" required
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-calendar-alt text-xs"></i>
                        </div>
                    </div>
                    <select name="status"
                        class="issuance-status px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full">
                        <option value="active" {{ ($issuance->status ?? 'active') == 'active' ? 'selected' : '' }}>
                            Active</option>
                        <option value="returned"
                            {{ ($issuance->status ?? 'active') == 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="overdue" {{ ($issuance->status ?? 'active') == 'overdue' ? 'selected' : '' }}>
                            Not Returned</option>
                        <option value="lost" {{ ($issuance->status ?? 'active') == 'lost' ? 'selected' : '' }}>Lost
                        </option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button"
                        class="px-4 py-2 text-xs font-semibold text-[#00553d] bg-gray-100 rounded-lg hover:bg-gray-200 border border-gray-200 transition-all duration-200"
                        onclick="document.getElementById('add-issuance-modal').classList.add('hidden')">Cancel</button>
                    <button type="submit" id="add-issuance-submit"
                        class="gradient-btn px-4 py-2 text-xs font-semibold text-white rounded-lg border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300">
                        <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                        <span class="btn-text"><i class="fas fa-save mr-2"></i>Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Issuance Modal -->
    <div id="edit-issuance-modal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md border border-[#ffcc34] shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-[#00553d] flex items-center">
                    <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md mr-2">
                        <i class="fas fa-edit text-white text-xs"></i>
                    </div>
                    Edit Issuance
                </h3>
                <button onclick="document.getElementById('edit-issuance-modal').classList.add('hidden')"
                    class="text-[#90143c] hover:text-[#b01a47] transition-all duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="edit-issuance-form" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="mb-4 space-y-3">
                    <div class="relative group">
                        <input type="text" name="staff_id" id="edit_staff_id" placeholder="Enter Staff ID"
                            required
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                    </div>
                    <div class="relative group">
                        <input type="text" name="equipment_id" id="edit_equipment_id"
                            placeholder="Enter Equipment ID" required
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-box text-xs"></i>
                        </div>
                    </div>
                    <div class="relative group">
                        <input type="text" name="issued_at" id="edit_issued_at"
                            placeholder="Date Issued (YYYY-MM-DD)" required
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-calendar-alt text-xs"></i>
                        </div>
                    </div>
                    <div class adjudication="relative group">
                        <input type="text" name="returned_at" id="edit_returned_at"
                            placeholder="Date Returned (YYYY-MM-DD)"
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-calendar-alt text-xs"></i>
                        </div>
                    </div>
                    <select name="status" id="edit_status"
                        class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full">
                        <option value="active">Active</option>
                        <option value="returned">Returned</option>
                        <option value="overdue">Not Returned</option>
                        <option value="lost">Lost</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button"
                        class="px-4 py-2 text-xs font-semibold text-[#00553d] bg-gray-100 rounded-lg hover:bg-gray-200 border border-gray-200 transition-all duration-200"
                        onclick="document.getElementById('edit-issuance-modal').classList.add('hidden')">Cancel</button>
                    <button type="submit" id="edit-issuance-submit"
                        class="gradient-btn px-4 py-2 text-xs font-semibold text-white rounded-lg border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300">
                        <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                        <span class="btn-text"><i class="fas fa-save mr-2"></i>Update</span>
                    </button>
                </div>
            </form>
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
                        <input type="text" name="staff_name" id="edit_inventory_staff_name"
                            placeholder="Enter Staff Name" required
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                    </div>
                    <div class="relative group">
                        <input type="text" name="equipment_name" id="edit_equipment_name"
                            placeholder="Enter Equipment Name" required
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-box text-xs"></i>
                        </div>
                    </div>
                    <div class="relative group">
                        <input type="text" name="model_brand" id="edit_model_brand"
                            placeholder="Enter Model/Brand" required
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-tag text-xs"></i>
                        </div>
                    </div>
                    <div class="relative group">
                        <input type="text" name="serial_number" id="edit_serial_number"
                            placeholder="Enter Serial Number" required
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-barcode text-xs"></i>
                        </div>
                    </div>
                    <div class="relative group">
                        <input type="text" name="pr_number" id="edit_pr_number" placeholder="Enter PR Number"
                            required
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-hashtag text-xs"></i>
                        </div>
                    </div>
                    <div class="relative group">
                        <input type="text" name="date_issued" id="edit_date_issued"
                            placeholder="Date Issued (YYYY-MM-DD)" required
                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 group-hover:shadow-md">
                        <div class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                            <i class="fas fa-calendar-alt text-xs"></i>
                        </div>
                    </div>
                    <select name="status" id="edit_inventory_status"
                        class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full">
                        <option value="available">Available</option>
                        <option value="in_use">In Use</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="damaged">Damaged</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button"
                        class="px-4 py-2 text-xs font-semibold text-[#00553d] bg-gray-100 rounded-lg hover:bg-gray-200 border border-gray-200 transitionវ
                        transition-all duration-200"
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

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                        // Accordion Toggle
                        const accordionToggles = document.querySelectorAll('.accordion-toggle');
                        accordionToggles.forEach(function(toggle) {
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

                        // Chart Initialization
                        const ctx = document.getElementById('equipmentChart').getContext('2d');
                        const equipmentChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode(array_keys($equipmentData ?? [])) !!},
                                datasets: [{
                                    label: 'Issuances',
                                    data: {!! json_encode(array_values($equipmentData ?? [])) !!},
                                    backgroundColor: '#ffcc34',
                                    borderColor: '#90143c',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        // Chart Time Filter
                        const chartTimeFilter = document.getElementById('chart-time-filter');
                        chartTimeFilter.addEventListener('change', function() {
                            const timeFilter = chartTimeFilter.value;
                            // Note: This would typically trigger an AJAX call or form submission to update chart data
                            Swal.fire({
                                title: 'Chart Filter Applied',
                                text: `Chart updated to show data for: ${timeFilter.charAt(0).toUpperCase() + timeFilter.slice(1)}`,
                                icon: 'info',
                                confirmButtonColor: '#00553d',
                                confirmButtonText: '<i class="fas fa-check mr-2"></i>OK'
                            });
                        });

                        // Stats Filter Form
                        const statsForm = document.getElementById('stats-filter-form');
                        if (statsForm) {
                            statsForm.addEventListener('submit', function(e) {
                                e.preventDefault();
                                const statsSearch = document.querySelector('input[name="stats_search"]').value.trim();
                                const timeFilter = document.querySelector('select[name="time_filter"]').value;
                                const typeFilter = document.querySelector('select[name="type_filter"]').value;
                                Swal.fire({
                                    title: 'Apply Filters?',
                                    html: `
                                <div class="text-left space-y-3">
                                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                        <h4 class="font-semibold text-blue-800 mb-2">Filter Summary:</h4>
                                        <div class="space-y-2 text-xs text-blue-700">
                                            <div class="flex justify-between">
                                                <span>Search:</span>
                                                <span class="font-medium">${statsSearch || 'None'}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Time Period:</span>
                                                <span class="font-medium">${timeFilter.charAt(0).toUpperCase() + timeFilter.slice(1)}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Type:</span>
                                                <span class="font-medium">${typeFilter.charAt(0).toUpperCase() + typeFilter.slice(1)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `,
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#00553d',
                                    cancelButtonColor: '#90143c',
                                    confirmButtonText: '<i class="fas fa-filter mr-2"></i>Apply Filters',
                                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel'
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        statsForm.submit();
                                    }
                                });
                            });
                        }

                        // Add Issuance Button
                        const addIssuanceBtn = document.getElementById('add-issuance-btn');
                        if (addIssuanceBtn) {
                            addIssuanceBtn.addEventListener('click', function() {
                                document.getElementById('add-issuance-modal').classList.remove('hidden');
                            });
                        }

                        // Add Issuance Form
                        const addIssuanceForm = document.getElementById('add-issuance-form');
                        const addIssuanceSubmit = document.getElementById('add-issuance-submit');
                        if (addIssuanceForm) {
                            addIssuanceForm.addEventListener('submit', function(e) {
                                e.preventDefault();
                                const staffId = document.querySelector('#add-issuance-form input[name="staff_id"]')
                                    .value.trim();
                                const equipmentId = document.querySelector(
                                    '#add-issuance-form input[name="equipment_id"]').value.trim();
                                const issuedAt = document.querySelector('#add-issuance-form input[name="issued_at"]')
                                    .value.trim();
                                const status = document.querySelector('#add-issuance-form select[name="status"]').value;
                                if (!staffId || !equipmentId || !issuedAt) {
                                    showAlert('Please fill all required fields', 'error');
                                    return;
                                }
                                Swal.fire({
                                    title: 'Add New Issuance?',
                                    html: `
                                <div class="text-left space-y-3">
                                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                        <div class="font-semibold text-blue-800 mb-2">Issuance Details:</div>
                                        <div class="text-xs text-blue-700">
                                            <div class="flex justify-between items-center">
                                                <span>Staff ID:</span>
                                                <span class="font-medium">${staffId}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>Equipment ID:</span>
                                                <span class="font-medium">${equipmentId}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>Date Issued:</span>
                                                <span class="font-medium">${issuedAt}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>Status:</span>
                                                <span class="font-medium">${status.charAt(0).toUpperCase() + status.slice(1)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `,
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#00553d',
                                    cancelButtonColor: '#90143c',
                                    confirmButtonText: '<i class="fas fa-plus mr-2"></i>Create Issuance',
                                    cancelButtonText: 'Cancel'
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        setLoadingState(addIssuanceSubmit, true);
                                        addIssuanceForm.submit();
                                    }
                                });
                            });
                        }

                        // Edit Issuance Buttons
                        const editIssuanceButtons = document.querySelectorAll('.edit-issuance-btn');
                        editIssuanceButtons.forEach(function(btn) {
                            btn.addEventListener('click', function() {
                                const id = btn.dataset.id;
                                Swal.fire({
                                    title: 'Edit Issuance',
                                    html: `
                                <div class="text-left space-y-3">
                                    <div class="bg-blue-50 p-2 rounded-lg border border-blue-200">
                                        <div class="text-xs text-blue-700">
                                            <strong>Issuance ID:</strong> ${id}
                                        </div>
                                    </div>
                                </div>
                            `,
                                    input: 'text',
                                    inputLabel: 'Update fields in the form below',
                                    showCancelButton: true,
                                    confirmButtonColor: '#00553d',
                                    cancelButtonColor: '#90143c',
                                    confirmButtonText: '<i class="fas fa-edit mr-2"></i>Load Form',
                                    cancelButtonText: 'Cancel'
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        const modal = document.getElementById('edit-issuance-modal');
                                        const form = document.getElementById('edit-issuance-form');
                                        form.action = `/issuances/${id}`;
                                        modal.classList.remove('hidden');
                                    }
                                });
                            });
                        });

                        // Edit Issuance Form
                        const editIssuanceForm = document.getElementById('edit-issuance-form');
                        const editIssuanceSubmit = document.getElementById('edit-issuance-submit');
                        if (editIssuanceForm) {
                            editIssuanceForm.addEventListener('submit', function(e) {
                                e.preventDefault();
                                const staffId = document.getElementById('edit_staff_id').value.trim();
                                const equipmentId = document.getElementById('edit_equipment_id').value.trim();
                                const issuedAt = document.getElementById('edit_issued_at').value.trim();
                                const returnedAt = document.getElementById('edit_returned_at').value.trim();
                                const status = document.getElementById('edit_status').value;
                                if (!staffId || !equipmentId || !issuedAt) {
                                    showAlert('Please fill all required fields', 'error');
                                    return;
                                }
                                Swal.fire({
                                    title: 'Update Issuance?',
                                    html: `
                                <div class="text-left space-y-3">
                                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                        <div class="font-semibold text-blue-800 mb-2">Updated Issuance Details:</div>
                                        <div class="text-xs text-blue-700">
                                            <div class="flex justify-between items-center">
                                                <span>Staff ID:</span>
                                                <span class="font-medium">${staffId}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>Equipment ID:</span>
                                                <span class="font-medium">${equipmentId}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>Date Issued:</span>
                                                <span class="font-medium">${issuedAt}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>Date Returned:</span>
                                                <span class="font-medium">${returnedAt || 'N/A'}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>Status:</span>
                                                <span class="font-medium">${status.charAt(0).toUpperCase() + status.slice(1)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `,
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#00553d',
                                    cancelButtonColor: '#90143c',
                                    confirmButtonText: '<i class="fas fa-save mr-2"></i>Update Issuance',
                                    cancelButtonText: 'Cancel'
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        setLoadingState(editIssuanceSubmit, true);
                                        editIssuanceForm.submit();
                                    }
                                });
                            });
                        }

                        // Edit Inventory Buttons
                        const editInventoryButtons = document.querySelectorAll('.edit-inventory-btn');
                        editInventoryButtons.forEach(function(btn) {
                            btn.addEventListener('click', function() {
                                const id = btn.dataset.id;
                                Swal.fire({
                                    title: 'Edit Inventory Item',
                                    html: `
                                <div class="text-left space-y-3">
                                    <div class="bg-blue-50 p-2 rounded-lg border border-blue-200">
                                        <div class="text-xs text-blue-700">
                                            <strong>Inventory ID:</strong> ${id}
                                        </div>
                                    </div>
                                </div>
                            `,
                                    input: 'text',
                                    inputLabel: 'Update fields in the form below',
                                    showCancelButton: true,
                                    confirmButtonColor: '#00553d',
                                    cancelButtonColor: '#90143c',
                                    confirmButtonText: '<i class="fas fa-edit mr-2"></i>Load Form',
                                    cancelButtonText: 'Cancel'
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        const modal = document.getElementById('edit-inventory-modal');
                                        const form = document.getElementById('edit-inventory-form');
                                        form.action = `/inventory/${id}`;
                                        modal.classList.remove('hidden');
                                    }
                                });
                            });
                        });

                        // Edit Inventory Form
                        const editInventoryForm = document.getElementById('edit-inventory-form');
                        const editInventorySubmit = document.getElementById('edit-inventory-submit');
                        if (editInventoryForm) {
                            editInventoryForm.addEventListener('submit', function(e) {
                                e.preventDefault();
                                const staffName = document.getElementById('edit_inventory_staff_name').value.trim();
                                const equipmentName = document.getElementById('edit_equipment_name').value.trim();
                                const modelBrand = document.getElementById('edit_model_brand').value.trim();
                                const serialNumber = document.getElementById('edit_serial_number').value.trim();
                                const prNumber = document.getElementById('edit_pr_number').value.trim();
                                const dateIssued = document.getElementById('edit_date_issued').value.trim();
                                const status = document.getElementById('edit_inventory_status').value;
                                if (!staffName || !equipmentName || !modelBrand || !serialNumber || !prNumber || !
                                    dateIssued) {
                                    showAlert('Please fill all required fields', 'error');
                                    return;
                                }
                                Swal.fire({
                                    title: 'Update Inventory Item?',
                                    html: `
                                <div class="text-left space-y-3">
                                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                        <div class="font-semibold text-blue-800 mb-2">Updated Inventory Details:</div>
                                        <div class="text-xs text-blue-700">
                                            <div class="flex justify-between items-center">
                                                <span>Staff Name:</span>
                                                <span class="font-medium">${staffName}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>Equipment Name:</span>
                                                <span class="font-medium">${equipmentName}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>Model/Brand:</span>
                                                <span class="font-medium">${modelBrand}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>Serial Number:</span>
                                                <span class="font-medium">${serialNumber}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>PR Number:</span>
                                                <span class="font-medium">${prNumber}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>Date Issued:</span>
                                                <span class="font-medium">${dateIssued}</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span>Status:</span>
                                                <span class="font-medium">${status.charAt(0).toUpperCase() + status.slice(1)}</span>
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
                                    cancelButtonText: 'Cancel'
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        setLoadingState(editInventorySubmit, true);
                                        editInventoryForm.submit();
                                    }
                                });
                            });
                        }

                        // Delete Issuance Buttons
                        const deleteIssuanceButtons = document.querySelectorAll('.delete-issuance-btn');
                        deleteIssuanceButtons.forEach(function(btn) {
                            btn.addEventListener('click', function() {
                                const form = btn.closest('form');
                                const equipmentName = btn.closest('.delete-issuance-form').dataset.name;
                                Swal.fire({
                                    title: 'Delete Issuance?',
                                    html: `
                                <div class="text-left space-y-3">
                                    <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <i class="fas fa-exclamation-triangle text-red-500 text-base"></i>
                                            <span class="font-semibold text-red-800 text-xs">Permanent Deletion Warning</span>
                                        </div>
                                        <div class="text-xs text-red-700">
                                            You are about to permanently delete the issuance for "<strong>${equipmentName}</strong>".
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
                                    }
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        form.submit();
                                    }
                                });
                            });
                        });

                        // Delete Inventory Buttons
                        const deleteInventoryButtons = document.querySelectorAll('.delete-inventory-btn');
                        deleteInventoryButtons.forEach(function(btn) {
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
                                    }
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        form.submit();
                                    }
                                });
                            });
                        });

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

                        // Sidebar Toggle
                        const toggleSidebar = document.getElementById('toggleSidebar');
                        const sidebar = document.querySelector('.sidebar');
                        if (toggleSidebar && sidebar) {
                            toggleSidebar.addEventListener('click', function() {
                                sidebar.classList.toggle('hidden');
                            });
                        }

                        // Handle Session Alerts
                        @if (session('success'))
                            showAlert('{{ session('success') }}', 'success');
                        @endif
                        @if (session('error'))
                            showAlert('{{ session('error') }}', 'error');
                        @endif
                        @if ($errors->any())
                            showAlert('{{ $errors->first() }}', 'error');
                        @endif

                        const equipmentData = @json($equipmentData ?? []);
        </script>
    @endsection
</x-app-layout>

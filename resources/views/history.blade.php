<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-[#00553d] leading-tight">
            {{ $pageTitle }}
        </h2>
    </x-slot>

    <style>
        .accordion-content {
            transition: max-height 0.3s ease-in-out, padding 0.3s ease-in-out;
            overflow: hidden;
            max-height: 0;
            padding: 0 1rem;
        }

        .accordion-content.open {
            padding: 1rem 1rem 1rem 1rem;
        }

        .card {
            transition: all 0.2s ease-in-out;
        }

        .card:hover {
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

        .table-header {
            background: linear-gradient(135deg, #ffcc34, #ffdb66);
        }

        .export-btn {
            background: linear-gradient(90deg, #90143c, #b01a47);
        }

        .export-btn:hover {
            background: linear-gradient(90deg, #6b102d, #8e1539);
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            margin-bottom: 1rem;
            /* Reduced bottom margin to 1rem */
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

        .page-jump-input {
            width: 4rem;
            padding: 0.5rem;
            border: 1px solid #ffcc34;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            color: #00553d;
            text-align: center;
        }

        .page-jump-input:focus {
            outline: none;
            ring: 2px;
            ring-color: #00553d;
        }

        .filter-loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .stats-container {
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: #ffcc34 #f1f5f9;
        }

        .stats-container::-webkit-scrollbar {
            height: 8px;
        }

        .stats-container::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .stats-container::-webkit-scrollbar-thumb {
            background-color: #ffcc34;
            border-radius: 4px;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .filter-form input,
        .filter-form select,
        .filter-form button {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }
    </style>

    <div class="flex min-h-screen bg-gray-50">
        <button id="toggleSidebar"
            class="md:hidden fixed top-3 left-3 z-50 bg-[#90143c] text-white p-1.5 rounded-md border border-[#ffcc34] shadow-md hover:shadow-lg transition-all duration-200">
            <i class="fas fa-bars text-xs"></i>
        </button>

        <div class="flex-1 container mx-auto px-4 py-8 max-w-full" style="padding-left: 2rem; padding-right: 2rem;">
            <div class="text-center mb-8 fade-in">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-full mb-4 shadow-lg relative">
                    <i class="fas {{ $headerIcon }} text-white text-xl animate-spin"
                        style="animation-duration: 8s;"></i>
                    <div
                        class="absolute inset-0 rounded-full bg-gradient-to-br from-[#90143c] to-[#b01a47] opacity-20 animate-ping">
                    </div>
                </div>

                <div class="stats-container flex flex-row gap-3 mt-6 max-w-full mx-auto justify-center">
                    @foreach ($overviewStats as $index => $stat)
                        <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30 min-w-[120px]">
                            <div class="text-base font-bold text-[#90143c]">{{ $stat['value'] }}</div>
                            <div class="text-xs text-gray-600">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="alert-container" class="mb-4"></div>

            <div class="space-y-4">
                <!-- History Logs Accordion -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden card border border-[#ffcc34] slide-up">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center p-4 bg-gradient-to-r from-[#90143c] to-[#b01a47] text-white hover:from-[#6b102d] hover:to-[#8e1539] transition-all duration-500"
                        data-target="history-logs">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 bg-white/20 rounded-md">
                                <i class="fas fa-list text-base"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-semibold block">History Logs</span>
                                <span class="text-xs opacity-80">View all system actions</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="bg-white bg-opacity-30 text-xs px-2 py-0.5 rounded-full font-medium">{{ $history_logs->total() }}
                                logs</span>
                            <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="history-logs" class="accordion-content open">
                        <div class="p-4">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
                                <form id="history-filter-form" class="filter-form w-full sm:w-auto" method="GET"
                                    action="{{ route('history') }}">
                                    <input type="text" name="log_search" id="log-search"
                                        placeholder="Search description..."
                                        class="border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-56"
                                        value="{{ request('log_search') }}">
                                    <select name="log_action" id="log-action-filter"
                                        class="bg-white border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                        <option value="all" {{ request('log_action') == 'all' ? 'selected' : '' }}>
                                            All Actions</option>
                                        <option value="created"
                                            {{ request('log_action') == 'created' ? 'selected' : '' }}>Created</option>
                                        <option value="updated"
                                            {{ request('log_action') == 'updated' ? 'selected' : '' }}>Updated</option>
                                        <option value="deleted"
                                            {{ request('log_action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                        <option value="issued"
                                            {{ request('log_action') == 'issued' ? 'selected' : '' }}>Issued</option>
                                        <option value="returned"
                                            {{ request('log_action') == 'returned' ? 'selected' : '' }}>Returned
                                        </option>
                                    </select>
                                    <select name="log_user" id="log-user-filter"
                                        class="bg-white border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                        <option value="all" {{ request('log_user') == 'all' ? 'selected' : '' }}>All
                                            Users</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request('log_user') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="date" name="log_date_from" id="log-date-from"
                                        placeholder="Action Date From"
                                        class="border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-32"
                                        value="{{ request('log_date_from') }}">
                                    <input type="date" name="log_date_to" id="log-date-to"
                                        placeholder="Action Date To"
                                        class="border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-32"
                                        value="{{ request('log_date_to') }}">
                                    <select name="per_page" id="per-page"
                                        class="bg-white border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </form>
                                <div class="w-full sm:w-auto flex justify-end">
                                    <button type="button" id="log-export-btn"
                                        class="bg-[#00553d] hover:bg-[#007a5a] text-white text-sm font-medium px-4 py-2 rounded-lg border border-[#ffcc34] shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-2">
                                        <i class="fas fa-spinner fa-spin hidden" id="export-spinner"></i>
                                        <i class="fas fa-download"></i>
                                        <span>Export CSV</span>
                                    </button>
                                </div>
                            </div>
                            @include('partials.history_logs')
                        </div>
                    </div>
                </div>

                <!-- Inventory Logs Accordion -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden card border border-[#ffcc34] slide-up">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center p-4 bg-gradient-to-r from-[#90143c] to-[#b01a47] text-white hover:from-[#6b102d] hover:to-[#8e1539] transition-all duration-500"
                        data-target="inventory-log">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 bg-white/20 rounded-md">
                                <i class="fas fa-boxes text-base"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-semibold block">Inventory Logs</span>
                                <span class="text-xs opacity-80">View current inventory items</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="bg-white bg-opacity-30 text-xs px-2 py-0.5 rounded-full font-medium">{{ $inventory->total() }}
                                items</span>
                            <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="inventory-log" class="accordion-content open">
                        <div class="p-4">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
                                <form id="inventory-filter-form" class="filter-form w-full sm:w-auto" method="GET"
                                    action="{{ route('history') }}">
                                    <input type="text" name="inventory_search" id="inventory-search"
                                        placeholder="Search staff or equipment..."
                                        class="border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-56"
                                        value="{{ request('inventory_search') }}">
                                    <select name="inventory_status" id="inventory-status-filter"
                                        class="bg-white border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                        <option value="all"
                                            {{ request('inventory_status') == 'all' ? 'selected' : '' }}>All Status
                                        </option>
                                        <option value="available"
                                            {{ request('inventory_status') == 'available' ? 'selected' : '' }}>
                                            Available</option>
                                        <option value="in_use"
                                            {{ request('inventory_status') == 'in_use' ? 'selected' : '' }}>In Use
                                        </option>
                                        <option value="maintenance"
                                            {{ request('inventory_status') == 'maintenance' ? 'selected' : '' }}>
                                            Maintenance</option>
                                        <option value="damaged"
                                            {{ request('inventory_status') == 'damaged' ? 'selected' : '' }}>Damaged
                                        </option>
                                    </select>
                                    <select name="inventory_department" id="inventory-department-filter"
                                        class="bg-white border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                        <option value="all"
                                            {{ request('inventory_department') == 'all' ? 'selected' : '' }}>All
                                            Departments</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ request('inventory_department') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="date" name="inventory_date_from" id="inventory-date-from"
                                        placeholder="Date Issued From"
                                        class="border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-32"
                                        value="{{ request('inventory_date_from') }}">
                                    <input type="date" name="inventory_date_to" id="inventory-date-to"
                                        placeholder="Date Issued To"
                                        class="border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-32"
                                        value="{{ request('inventory_date_to') }}">
                                    <select name="inventory_per_page" id="inventory-per-page"
                                        class="bg-white border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                        <option value="20" {{ $inventoryPerPage == 20 ? 'selected' : '' }}>20
                                        </option>
                                        <option value="50" {{ $inventoryPerPage == 50 ? 'selected' : '' }}>50
                                        </option>
                                        <option value="100" {{ $inventoryPerPage == 100 ? 'selected' : '' }}>100
                                        </option>
                                    </select>
                                </form>
                                <div class="w-full sm:w-auto flex justify-end">
                                    <button type="button" id="log-export-btn"
                                        class="bg-[#00553d] hover:bg-[#007a5a] text-white text-sm font-medium px-4 py-2 rounded-lg border border-[#ffcc34] shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-2">
                                        <i class="fas fa-spinner fa-spin hidden" id="export-spinner"></i>
                                        <i class="fas fa-download"></i>
                                        <span>Export CSV</span>
                                    </button>
                                </div>
                            </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full table-auto divide-y divide-[#ffcc34]"
                                    aria-label="Inventory Logs">
                                    <thead class="table-header">
                                        <tr>
                                            <th scope="col"
                                                class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[150px]">
                                                Staff Name</th>
                                            <th scope="col"
                                                class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[100px]">
                                                Department</th>
                                            <th scope="col"
                                                class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[150px]">
                                                Equipment</th>
                                            <th scope="col"
                                                class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[120px]">
                                                Model/Brand</th>
                                            <th scope="col"
                                                class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[100px]">
                                                Serial No.</th>
                                            <th scope="col"
                                                class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[100px]">
                                                PR No.</th>
                                            <th scope="col"
                                                class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[100px]">
                                                Date Issued</th>
                                            <th scope="col"
                                                class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[100px]">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="inventoryTableBody" class="bg-white divide-y divide-[#ffcc34]">
                                        @if ($inventory->isEmpty())
                                            <tr>
                                                <td colspan="8"
                                                    class="px-4 py-4 text-center text-xs text-black bg-gradient-to-br from-gray-50 to-white rounded-lg border-2 border-dashed border-gray-300">
                                                    <div
                                                        class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                                        <i class="fas fa-boxes text-2xl text-gray-400"></i>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mb-2 font-medium">No inventory
                                                        items found</p>
                                                    <p class="text-[0.6rem] text-gray-400">Items will appear here once
                                                        added</p>
                                                </td>
                                            </tr>
                                        @else
                                            @foreach ($inventory as $item)
                                                <tr class="hover:bg-gray-50 transition-colors slide-up">
                                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[150px] truncate max-w-xs"
                                                        title="{{ $item->staff_name ?? 'N/A' }}">
                                                        {{ $item->staff_name ?? 'N/A' }}</td>
                                                    <td
                                                        class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[100px]">
                                                        {{ $item->department->name ?? 'N/A' }}</td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[150px] truncate max-w-xs"
                                                        title="{{ $item->equipment_name }}">
                                                        {{ $item->equipment_name }}</td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[120px] truncate max-w-xs"
                                                        title="{{ $item->model_brand ?? 'N/A' }}">
                                                        {{ $item->model_brand ?? 'N/A' }}</td>
                                                    <td
                                                        class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[100px]">
                                                        {{ $item->serial_number }}</td>
                                                    <td
                                                        class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[100px]">
                                                        {{ $item->pr_number }}</td>
                                                    <td
                                                        class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[100px]">
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
                                                    <td class="px-4 py-3 whitespace-nowrap min-w-[100px]">
                                                        <span
                                                            class="status-indicator {{ $item->status == 'available' ? 'status-active' : ($item->status == 'in_use' ? 'status-warning' : ($item->status == 'maintenance' ? 'status-warning' : 'status-inactive')) }}"></span>
                                                        <span
                                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status == 'available' ? 'bg-green-100 text-green-800' : ($item->status == 'in_use' ? 'bg-blue-100 text-blue-800' : ($item->status == 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div id="inventoryPagination" class="pagination-container mt-4">
                                <select id="inventory-per-page-display"
                                    class="bg-white border border-[#ffcc34] rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#00553d] mr-2">
                                    <option value="20" {{ $inventoryPerPage == 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ $inventoryPerPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $inventoryPerPage == 100 ? 'selected' : '' }}>100
                                    </option>
                                </select>
                                <span class="pagination-info">
                                    Page {{ $inventory->currentPage() }} to {{ $inventory->currentPage() }} of
                                    {{ $inventory->total() }} results
                                </span>
                                @if ($inventory->onFirstPage())
                                    <span class="pagination-btn opacity-50 cursor-not-allowed">Previous</span>
                                @else
                                    <a href="{{ $inventory->previousPageUrl() }}" class="pagination-btn">Previous</a>
                                @endif
                                @foreach ($inventory->getUrlRange(1, $inventory->lastPage()) as $page => $url)
                                    <a href="{{ $url }}"
                                        class="pagination-btn {{ $inventory->currentPage() == $page ? 'current' : '' }}">{{ $page }}</a>
                                @endforeach
                                @if ($inventory->hasMorePages())
                                    <a href="{{ $inventory->nextPageUrl() }}" class="pagination-btn">Next</a>
                                @else
                                    <span class="pagination-btn opacity-50 cursor-not-allowed">Next</span>
                                @endif
                                <input type="number" id="inventoryPageJump" class="page-jump-input"
                                    placeholder="Page" min="1" max="{{ $inventory->lastPage() }}"
                                    value="{{ $inventory->currentPage() }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Utility Functions
            function setLoadingState(element, isLoading) {
                if (isLoading) {
                    element.classList.add('filter-loading');
                } else {
                    element.classList.remove('filter-loading');
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
                    <button onclick="this.parentElement.remove()" class="ml-auto hover:scale-110 transition-transform">
                        <i class="fas fa-times hover:text-gray-800"></i>
                    </button>
                `;
                alertContainer.appendChild(alertDiv);
                setTimeout(() => {
                    alertDiv.classList.remove('fade-in');
                    alertDiv.classList.add('fade-out');
                    setTimeout(() => alertDiv.remove(), 300);
                }, 4000);
            }

            // Accordion Functionality
            const accordionToggles = document.querySelectorAll('.accordion-toggle');
            accordionToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const target = document.getElementById(toggle.dataset.target);
                    const icon = toggle.querySelector('.accordion-icon');
                    const isOpen = target.classList.contains('open');
                    target.classList.toggle('open');
                    icon.classList.toggle('rotate-180');
                    if (!isOpen) {
                        target.style.maxHeight = (target.scrollHeight + 30) +
                            'px'; // Reduced to 30px for pagination
                    } else {
                        target.style.maxHeight = '0';
                    }
                });
            });

            // Initialize accordions as open
            const historyLogsContent = document.getElementById('history-logs');
            const historyLogsIcon = document.querySelector('[data-target="history-logs"] .accordion-icon');
            const inventoryLogContent = document.getElementById('inventory-log');
            const inventoryLogIcon = document.querySelector('[data-target="inventory-log"] .accordion-icon');
            historyLogsContent.classList.add('open');
            historyLogsContent.style.maxHeight = (historyLogsContent.scrollHeight + 30) + 'px';
            historyLogsIcon.classList.add('rotate-180');
            inventoryLogContent.classList.add('open');
            inventoryLogContent.style.maxHeight = (inventoryLogContent.scrollHeight + 30) + 'px';
            inventoryLogIcon.classList.add('rotate-180');

            // Pagination
            function initializePagination() {
                const historyPageJump = document.getElementById('historyPageJump');
                const inventoryPageJump = document.getElementById('inventoryPageJump');
                const perPageSelect = document.getElementById('per-page');
                const perPageDisplay = document.getElementById('per-page-display');
                const inventoryPerPageSelect = document.getElementById('inventory-per-page');
                const inventoryPerPageDisplay = document.getElementById('inventory-per-page-display');

                if (historyPageJump) {
                    historyPageJump.addEventListener('change', function() {
                        const page = parseInt(this.value);
                        const maxPage = parseInt(this.max);
                        if (isNaN(page) || page < 1 || page > maxPage) {
                            showAlert(`Please enter a page number between 1 and ${maxPage}.`, 'error');
                            this.value = this.defaultValue;
                            return;
                        }
                        const form = document.getElementById('history-filter-form');
                        const queryParams = new URLSearchParams(new FormData(form));
                        queryParams.set('history_page', page);
                        window.location.href = `{{ route('history') }}?${queryParams.toString()}`;
                    });
                }

                if (inventoryPageJump) {
                    inventoryPageJump.addEventListener('change', function() {
                        const page = parseInt(this.value);
                        const maxPage = parseInt(this.max);
                        if (isNaN(page) || page < 1 || page > maxPage) {
                            showAlert(`Please enter a page number between 1 and ${maxPage}.`, 'error');
                            this.value = this.defaultValue;
                            return;
                        }
                        const form = document.getElementById('inventory-filter-form');
                        const queryParams = new URLSearchParams(new FormData(form));
                        queryParams.set('inventory_page', page);
                        window.location.href = `{{ route('history') }}?${queryParams.toString()}`;
                    });
                }

                if (perPageSelect && perPageDisplay) {
                    const syncPerPage = () => {
                        perPageDisplay.value = perPageSelect.value;
                        const form = document.getElementById('history-filter-form');
                        const queryParams = new URLSearchParams(new FormData(form));
                        queryParams.set('history_page', '1');
                        queryParams.set('per_page', perPageSelect.value);
                        window.location.href = `{{ route('history') }}?${queryParams.toString()}`;
                    };
                    perPageSelect.addEventListener('change', syncPerPage);
                    perPageDisplay.addEventListener('change', () => {
                        perPageSelect.value = perPageDisplay.value;
                        syncPerPage();
                    });
                }

                if (inventoryPerPageSelect && inventoryPerPageDisplay) {
                    const syncInventoryPerPage = () => {
                        inventoryPerPageDisplay.value = inventoryPerPageSelect.value;
                        const form = document.getElementById('inventory-filter-form');
                        const queryParams = new URLSearchParams(new FormData(form));
                        queryParams.set('inventory_page', '1');
                        queryParams.set('inventory_per_page', inventoryPerPageSelect.value);
                        window.location.href = `{{ route('history') }}?${queryParams.toString()}`;
                    };
                    inventoryPerPageSelect.addEventListener('change', syncInventoryPerPage);
                    inventoryPerPageDisplay.addEventListener('change', () => {
                        inventoryPerPageSelect.value = inventoryPerPageDisplay.value;
                        syncInventoryPerPage();
                    });
                }
            }

            // Live Filtering
            const debounce = _.debounce((func) => func(), 300);

            function initializeLiveFiltering() {
                const historyForm = document.getElementById('history-filter-form');
                const inventoryForm = document.getElementById('inventory-filter-form');

                if (historyForm) {
                    const inputs = historyForm.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        const eventType = input.type === 'text' || input.type === 'date' ? 'input' :
                            'change';
                        input.addEventListener(eventType, () => {
                            debounce(() => {
                                const queryParams = new URLSearchParams(new FormData(
                                    historyForm));
                                queryParams.set('history_page', '1');
                                window.location.href =
                                    `{{ route('history') }}?${queryParams.toString()}`;
                            });
                        });
                    });
                }

                if (inventoryForm) {
                    const inputs = inventoryForm.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        const eventType = input.type === 'text' || input.type === 'date' ? 'input' :
                            'change';
                        input.addEventListener(eventType, () => {
                            debounce(() => {
                                const queryParams = new URLSearchParams(new FormData(
                                    inventoryForm));
                                queryParams.set('inventory_page', '1');
                                window.location.href =
                                    `{{ route('history') }}?${queryParams.toString()}`;
                            });
                        });
                    });
                }
            }

            // Export Functionality
            function initializeExportFunctionality() {
                const logExportBtn = document.getElementById('log-export-btn');
                const inventoryExportBtn = document.getElementById('inventory-export-btn');

                if (logExportBtn) {
                    logExportBtn.addEventListener('click', async function() {
                        setLoadingState(logExportBtn, true);
                        try {
                            const queryParams = new URLSearchParams(new FormData(document
                                .getElementById('history-filter-form')));
                            const response = await fetch(
                                `{{ route('history.export.csv') }}?${queryParams.toString()}`, {
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                });
                            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                            const blob = await response.blob();
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'history_logs_export.csv';
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                            window.URL.revokeObjectURL(url);
                            showAlert('History logs exported successfully.', 'success');
                        } catch (error) {
                            console.error('Error exporting history logs:', error);
                            showAlert('Failed to export history logs. Please try again.', 'error');
                        } finally {
                            setLoadingState(logExportBtn, false);
                        }
                    });
                }

                if (inventoryExportBtn) {
                    inventoryExportBtn.addEventListener('click', async function() {
                        setLoadingState(inventoryExportBtn, true);
                        try {
                            const queryParams = new URLSearchParams(new FormData(document
                                .getElementById('inventory-filter-form')));
                            const response = await fetch(
                                `{{ route('history.inventory.export.csv') }}?${queryParams.toString()}`, {
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                });
                            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                            const blob = await response.blob();
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'inventory_logs_export.csv';
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                            window.URL.revokeObjectURL(url);
                            showAlert('Inventory logs exported successfully.', 'success');
                        } catch (error) {
                            console.error('Error exporting inventory logs:', error);
                            showAlert('Failed to export inventory logs. Please try again.', 'error');
                        } finally {
                            setLoadingState(inventoryExportBtn, false);
                        }
                    });
                }
            }

            // Initialize functionalities
            initializePagination();
            initializeLiveFiltering();
            initializeExportFunctionality();

            // Handle session messages
            @if (session('success'))
                showAlert('{{ session('success') }}', 'success');
            @endif
            @if (session('error'))
                showAlert('{{ session('error') }}', 'error');
            @endif
        });
    </script>

    <x-auth-footer />
</x-app-layout>

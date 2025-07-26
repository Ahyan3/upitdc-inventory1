<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-[#00553d] leading-tight">
            {{ __('History') }}
        </h2>
    </x-slot>

    <style>
        .accordion-content {        
            transition: max-height 0.3s ease-in-out, padding 0.3s ease-in-out;
            max-height: 0;
            overflow: hidden;
            padding: 0 1rem;
        }
        .accordion-content.open {
            max-height: 800px;
            padding: 1rem;
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
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .status-indicator {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            margin-right: 6px;
            animation: pulse 2s infinite;
        }
        .status-active { background-color: #10b981; }
        .status-warning { background-color: #f59e0b; }
        .status-inactive { background-color: #ef4444; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .table-header {
            background: linear-gradient(135deg, #ffcc34, #ffdb66);
        }
        .filter-btn {
            background: linear-gradient(90deg, #90143c, #b01a47);
        }
        .filter-btn:hover {
            background: linear-gradient(90deg, #6b102d, #8e1539);
        }
    </style>

    <div class="flex min-h-screen bg-gray-50">
        <button id="toggleSidebar" class="md:hidden fixed top-3 left-3 z-50 bg-[#90143c] text-white p-1.5 rounded-md border border-[#ffcc34] shadow-md hover:shadow-lg transition-all duration-200">
            <i class="fas fa-bars text-xs"></i>
        </button>

        <div class="flex-1 container mx-auto px-3 py-6 max-w-3xl">
            <div class="text-center mb-8 fade-in">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-full mb-4 shadow-lg relative">
                    <i class="fas fa-history text-white text-xl animate-spin" style="animation-duration: 8s;"></i>
                    <div class="absolute inset-0 rounded-full bg-gradient-to-br from-[#90143c] to-[#b01a47] opacity-20 animate-ping"></div>
                </div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-[#90143c] to-[#00553d] bg-clip-text text-transparent">History</h1>
                <p class="text-xs text-[#00553d] opacity-80 max-w-sm mx-auto">View all inventory actions and current inventory</p>

                <div class="grid grid-cols-3 gap-3 mt-6 max-w-sm mx-auto">
                    <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                        <div class="text-base font-bold text-[#90143c]">{{ $history_logs->count() }}</div>
                        <div class="text-xs text-gray-600">Total Logs</div>
                    </div>
                    <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                        <div class="text-base font-bold text-[#00553d]">{{ $inventory->count() }}</div>
                        <div class="text-xs text-gray-600">Inventory Items</div>
                    </div>
                    <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                        <div class="text-base font-bold text-[#b01a47]">
                            <span class="status-indicator status-active"></span>Active
                        </div>
                        <div class="text-xs text-gray-600">System Status</div>
                    </div>
                </div>
            </div>

            <div id="alert-container" class="mb-4"></div>

            <div class="space-y-4">
                <!-- History Logs Accordion -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden card border border-[#ffcc34] slide-up">
                    <button class="accordion-toggle w-full flex justify-between items-center p-4 bg-gradient-to-r from-[#90143c] to-[#b01a47] text-white hover:from-[#6b102d] hover:to-[#8e1539] transition-all duration-500" data-target="history-logs">
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
                            <span class="bg-white bg-opacity-30 text-xs px-2 py-0.5 rounded-full font-medium">{{ $history_logs->count() }} logs</span>
                            <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="history-logs" class="accordion-content">
                        <div class="p-4">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
                                <h3 class="text-xs font-semibold text-[#00553d]">Filter Logs</h3>
                                <form method="GET" action="{{ route('history') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <input type="text" name="log_search" id="log-search" placeholder="Search logs..."
                                           class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-64"
                                           value="{{ request('log_search') }}">
                                    <select name="log_action" id="log-action-filter"
                                            class="bg-white border border-[#ffcc34] rounded-lg px-3 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                        <option value="all" {{ request('log_action') == 'all' ? 'selected' : '' }}>All Actions</option>
                                        <option value="created" {{ request('log_action') == 'created' ? 'selected' : '' }}>Created</option>
                                        <option value="updated" {{ request('log_action') == 'updated' ? 'selected' : '' }}>Updated</option>
                                        <option value="deleted" {{ request('log_action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                    </select>
                                    <button type="submit" id="log-filter-btn" class="filter-btn px-6 py-3 text-white font-semibold rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center">
                                        <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                        <span class="btn-text"><i class="fas fa-filter mr-2"></i>Filter</span>
                                    </button>
                                </form>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full table-auto divide-y divide-[#ffcc34]" aria-label="History Logs">
                                    <thead class="table-header">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">Action</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">Model/Brand</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">Description</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">Action Date</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">IP Address</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">User Agent</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-[#ffcc34]">
                                        @if ($history_logs->isEmpty())
                                            <tr>
                                                <td colspan="6" class="px-4 py-4 text-center text-xs text-[#00553d]">No logs found</td>
                                            </tr>
                                        @else
                                            @foreach ($history_logs as $log)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-[#00553d]">
                                                        <span class="status-indicator {{ $log->action == 'created' ? 'status-active' : ($log->action == 'updated' ? 'status-warning' : 'status-inactive') }}"></span>
                                                        {{ ucfirst($log->action) }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->model_brand }} (ID: {{ $log->model_id }})</td>
                                                    <td class="px-4 py-3 text-xs text-[#00553d]">{{ $log->description ?? 'N/A' }}</td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->action_date }}</td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->ip_address ?? 'N/A' }}</td>
                                                    <td class="px-4 py-3 text-xs text-[#00553d] truncate max-w-xs">{{ $log->user_agent ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @if ($history_logs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <div class="mt-4">
                                    {{ $history_logs->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Inventory Log Accordion -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden card border border-[#ffcc34] slide-up">
                    <button class="accordion-toggle w-full flex justify-between items-center p-4 bg-gradient-to-r from-[#90143c] to-[#b01a47] text-white hover:from-[#6b102d] hover:to-[#8e1539] transition-all duration-500" data-target="inventory-log">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 bg-white/20 rounded-md">
                                <i class="fas fa-boxes text-base"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-semibold block">Inventory Log</span>
                                <span class="text-xs opacity-80">View current inventory items</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="bg-white bg-opacity-30 text-xs px-2 py-0.5 rounded-full font-medium">{{ $inventory->count() }} items</span>
                            <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="inventory-log" class="accordion-content open">
                        <div class="p-4">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
                                <h3 class="text-xs font-semibold text-[#00553d]">Filter Inventory</h3>
                                <form method="GET" action="{{ route('history') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <input type="text" name="inventory_search" id="inventory-search" placeholder="Search inventory..."
                                           class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-64"
                                           value="{{ request('inventory_search') }}">
                                    <select name="inventory_status" id="inventory-status-filter"
                                            class="bg-white border border-[#ffcc34] rounded-lg px-3 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                        <option value="all" {{ request('inventory_status') == 'all' ? 'selected' : '' }}>All Status</option>
                                        <option value="available" {{ request('inventory_status') == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="in_use" {{ request('inventory_status') == 'in_use' ? 'selected' : '' }}>In Use</option>
                                        <option value="maintenance" {{ request('inventory_status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="damaged" {{ request('inventory_status') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                    </select>
                                    <button type="submit" id="inventory-filter-btn" class="filter-btn px-6 py-3 text-white font-semibold rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center">
                                        <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                        <span class="btn-text"><i class="fas fa-filter mr-2"></i>Filter</span>
                                    </button>
                                </form>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full table-auto divide-y divide-[#ffcc34]" aria-label="Current Inventory">
                                    <thead class="table-header">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">Staff Name</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">Department</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">Equipment</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">Model/Brand</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">Serial No.</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">PR No.</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">Date Issued</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-[#ffcc34]">
                                        @forelse($inventory as $item)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->staff_name }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->department->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->equipment_name }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->model_brand ?? 'N/A' }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->serial_number }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->pr_number }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->date_issued }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <span class="status-indicator {{ $item->status == 'available' ? 'status-active' : ($item->status == 'in_use' ? 'status-warning' : ($item->status == 'maintenance' ? 'status-warning' : 'status-inactive')) }}"></span>
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status == 'available' ? 'bg-green-100 text-green-800' : ($item->status == 'in_use' ? 'bg-blue-100 text-blue-800' : ($item->status == 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="px-4 py-4 text-center text-xs text-[#00553d]">No inventory items found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if ($inventory instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <div class="mt-4">
                                    {{ $inventory->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Success!',
                    text: '<?php echo session('success'); ?>',
                    icon: 'success',
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Oops! Something went wrong',
                    text: '<?php echo session('error'); ?>',
                    icon: 'error',
                    confirmButtonColor: '#90143c',
                    confirmButtonText: 'Try Again'
                });
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Accordion functionality
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

            // Ensure only Inventory Log is open by default
            const historyLogsContent = document.getElementById('history-logs');
            const historyLogsIcon = document.querySelector('[data-target="history-logs"] .accordion-icon');
            const inventoryLogContent = document.getElementById('inventory-log');
            const inventoryLogIcon = document.querySelector('[data-target="inventory-log"] .accordion-icon');
            historyLogsContent.classList.remove('open');
            historyLogsIcon.classList.remove('rotate-180');
            inventoryLogContent.classList.add('open');
            inventoryLogIcon.classList.add('rotate-180');

            // Debounced search
            const debounce = (func, wait) => {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            };

            // Filter form submission with SweetAlert2
            const filterForms = document.querySelectorAll('form');
            filterForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const searchInput = form.querySelector('input[name="inventory_search"]') || form.querySelector('input[name="log_search"]');
                    const statusInput = form.querySelector('select[name="inventory_status"]') || form.querySelector('select[name="log_action"]');
                    const button = form.querySelector('button[type="submit"]');
                    const isInventory = form.querySelector('input[name="inventory_search"]') !== null;

                    Swal.fire({
                        title: `Filter ${isInventory ? 'Inventory' : 'Logs'}?`,
                        html: `
                            <div class="text-left space-y-3">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                    <div class="font-semibold text-blue-800 mb-2">Filter Details:</div>
                                    <div class="text-xs text-blue-700">
                                        <div class="flex justify-between items-center">
                                            <span>Search Term:</span>
                                            <span class="font-medium">"${searchInput ? searchInput.value : 'None'}"</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>${isInventory ? 'Status' : 'Action'}:</span>
                                            <span class="font-medium">${statusInput.value === 'all' ? 'All' : ucFirst(statusInput.value)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#00553d',
                        cancelButtonColor: '#90143c',
                        confirmButtonText: '<i class="fas fa-filter mr-2"></i>Apply Filter',
                        cancelButtonText: 'Cancel'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            setLoadingState(button, true);
                            Swal.fire({
                                title: 'Processing...',
                                html: 'Applying your filter...',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            form.submit();
                        }
                    });
                });
            });

            // Utility functions
            function setLoadingState(button, loading) {
                if (loading) {
                    button.classList.add('btn-loading');
                    button.disabled = true;
                } else {
                    button.classList.remove('btn-loading');
                    button.disabled = false;
                }
            }

            function showAlert(message, type) {
                const alertContainer = document.getElementById('alert-container');
                const alertClass = type === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 'bg-blue-100 border-blue-400 text-blue-700';
                const iconClass = type === 'error' ? 'fas fa-exclamation-triangle' : 'fas fa-info-circle';
                
                alertContainer.innerHTML = '<div class="alert ' + alertClass + ' border px-3 py-2 rounded-lg flex items-center space-x-2 animate-pulse">' +
                    '<i class="' + iconClass + '"></i>' +
                    '<span class="text-xs">' + message + '</span>' +
                    '<button onclick="this.parentElement.remove()" class="ml-auto hover:scale-110 transition-transform">' +
                    '<i class="fas fa-times hover:text-gray-800"></i>' +
                    '</button>' +
                    '</div>';
                
                setTimeout(() => {
                    const alert = alertContainer.querySelector('.alert');
                    if (alert) {
                        alert.style.animation = 'fadeOut 0.3s ease-out forwards';
                        setTimeout(() => alert.remove(), 300);
                    }
                }, 4000);
            }

            function ucFirst(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }
        });
    </script>

    <x-auth-footer />
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8 w-full">
            <div class="text-center mb-10 animate-fade-in w-full">
                <h2 class="text-lg font-bold text-red-600">UPITDC - Inventory System</h2>
                <p class="text-xs text-gray-600">Welcome to your dashboard</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8 w-full" id="statsContainer">
                <a href="{{ route('staff.index') }}" class="bg-white rounded-xl shadow-md p-6 hover:bg-gray-100 transition duration-200 block stat-item" data-name="Total Staff">
                    <h3 class="text-xs font-semibold text-gray-700">Total Staff</h3>
                    <p class="text-lg font-bold text-green-600">{{ $totalStaff ?? 'N/A' }}</p>
                </a>
                <a href="{{ route('inventory') }}" class="bg-white rounded-xl shadow-md p-6 hover:bg-gray-100 transition duration-200 block stat-item" data-name="Total Issued Equipment">
                    <h3 class="text-xs font-semibold text-gray-700">Total Issued Equipment</h3>
                    <p class="text-lg font-bold text-green-600">{{ $totalIssuedEquipment ?? 'N/A' }}</p>
                </a>
                <a href="{{ route('inventory') }}" class="bg-white rounded-xl shadow-md p-6 hover:bg-gray-100 transition duration-200 block stat-item" data-name="Total Returned Equipment">
                    <h3 class="text-xs font-semibold text-gray-700">Total Returned Equipment</h3>
                    <p class="text-lg font-bold text-green-600">{{ $totalReturnedEquipment ?? 'N/A' }}</p>
                </a>
                <a href="{{ route('inventory') }}" class="bg-white rounded-xl shadow-md p-6 hover:bg-gray-100 transition duration-200 block stat-item" data-name="Pending Requests">
                    <h3 class="text-xs font-semibold text-gray-700">Pending Requests</h3>
                    <p class="text-lg font-bold text-green-600">{{ $pendingRequests ?? 'N/A' }}</p>
                </a>
                <a href="{{ route('inventory') }}" class="bg-white rounded-xl shadow-md p-6 hover:bg-gray-100 transition duration-200 block stat-item" data-name="Active Issuances">
                    <h3 class="text-xs font-semibold text-gray-700">Active Issuances</h3>
                    <p class="text-lg font-bold text-green-600">{{ $activeIssuances ?? 'N/A' }}</p>
                </a>
            </div>

            <!-- Filter for Stats with Search Bar -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8 w-full">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <form id="stats-filter-form" method="GET" action="{{ route('dashboard') }}" class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2 w-full md:w-auto">
                        <div class="relative">
                            <input type="text" name="stats_search" id="stats-search" placeholder="Search stats..."
                                class="w-full pl-8 pr-3 py-1 border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-green-500"
                                value="{{ request('stats_search') }}">
                            <div class="absolute left-2 top-2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                        <div class="relative">
                            <select name="time_filter" id="time-filter" class="appearance-none bg-gray-100 border border-gray-300 rounded-md px-3 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-green-500">
                                <option value="all" {{ request('time_filter') == 'all' ? 'selected' : '' }}>All Time</option>
                                <option value="day" {{ request('time_filter') == 'day' ? 'selected' : '' }}>Today</option>
                                <option value="week" {{ request('time_filter') == 'week' ? 'selected' : '' }}>This Week</option>
                                <option value="month" {{ request('time_filter') == 'month' ? 'selected' : '' }}>This Month</option>
                                <option value="year" {{ request('time_filter') == 'year' ? 'selected' : '' }}>This Year</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                </svg>
                            </div>
                        </div>
                        <div class="relative">
                            <select name="type_filter" id="type-filter" class="appearance-none bg-gray-100 border border-gray-300 rounded-md px-3 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-green-500">
                                <option value="total" {{ request('type_filter') == 'total' ? 'selected' : '' }}>Total</option>
                                <option value="available" {{ request('type_filter') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="not_working" {{ request('type_filter') == 'not_working' ? 'selected' : '' }}>Not Working</option>
                                <option value="working" {{ request('type_filter') == 'working' ? 'selected' : '' }}>Working</option>
                                <option value="not_returned" {{ request('type_filter') == 'not_returned' ? 'selected' : '' }}>Not Returned</option>
                                <option value="returned" {{ request('type_filter') == 'returned' ? 'selected' : '' }}>Returned</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                </svg>
                            </div>
                        </div>
                        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded-md text-xs hover:bg-green-600">Filter</button>
                    </form>
                </div>
            </div>

            <!-- Recent Issuances -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 w-full">
                <div class="bg-gray-800 px-6 py-4">
                    <h2 class="text-sm font-semibold text-white">Recent Issuances</h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto w-full">
                        <table class="min-w-full table-auto divide-y divide-gray-200" aria-label="Recent Issuances">
                            <thead class="bg-green-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Staff</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Equipment</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Model/Brand</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Date Issued</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if ($issuances->isEmpty())
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 text-xs">No Current Record</td>
                                </tr>
                                @else
                                @foreach ($issuances as $issuance)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $issuance->staff->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $issuance->equipment->department->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $issuance->equipment->equipment_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $issuance->equipment->model_brand ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $issuance->issued_at }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $issuance->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($issuance->status) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Inventory Log -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 w-full">
                <div class="bg-gray-800 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-sm font-semibold text-white">Inventory Log</h2>
                    <form method="GET" action="{{ route('dashboard') }}" class="flex space-x-2">
                        <input type="text" name="inventory_search" id="inventory-search" placeholder="Search inventory..."
                            class="px-3 py-1 rounded-md text-xs border border-gray-300 focus:outline-none focus:ring-1 focus:ring-green-500" value="{{ request('inventory_search') }}">
                        <select name="inventory_status" id="inventory-status-filter"
                            class="bg-gray-100 border border-gray-300 rounded-md px-3 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-green-500">
                            <option value="all" {{ request('inventory_status') == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="available" {{ request('inventory_status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="not_working" {{ request('inventory_status') == 'not_working' ? 'selected' : '' }}>Not Working</option>
                            <option value="working" {{ request('inventory_status') == 'working' ? 'selected' : '' }}>Working</option>
                            <option value="not_returned" {{ request('inventory_status') == 'not_returned' ? 'selected' : '' }}>Not Returned</option>
                            <option value="returned" {{ request('inventory_status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        </select>
                        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded-md text-xs hover:bg-green-600">Filter</button>
                    </form>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto w-full">
                        <table class="min-w-full table-auto divide-y divide-gray-200" aria-label="Current Inventory">
                            <thead class="bg-blue-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Equipment</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Model/Brand</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Serial No.</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($inventory as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $item->equipment_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $item->model_brand }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $item->serial_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $item->department->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                        $statusClasses = [
                                        'available' => 'bg-green-100 text-green-800',
                                        'not_working' => 'bg-red-100 text-red-800',
                                        'working' => 'bg-blue-100 text-blue-800',
                                        'not_returned' => 'bg-yellow-100 text-yellow-800',
                                        'returned' => 'bg-purple-100 text-purple-800'
                                        ];
                                        $statusClass = $statusClasses[$item->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 text-xs">No inventory items found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Equipment Issuance Graph -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in mb-8 w-full">
                <div class="bg-gray-800 px-6 py-4">
                    <h2 class="text-sm font-semibold text-white">Equipment Issuance Statistics</h2>
                </div>
                <div class="p-6">
                    <canvas id="equipmentChart" class="w-full h-64" data-equipment="{{ json_encode($equipmentData ?? []) }}"></canvas>
                </div>
            </div>
        </div>
    </div>
    <x-auth-footer />

     @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Add Font Awesome if not already included -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <script>
        // Auto-submit forms and other existing functionality
        document.getElementById('time-filter')?.addEventListener('change', function() {
            document.getElementById('stats-filter-form').submit();
        });
        document.getElementById('type-filter')?.addEventListener('change', function() {
            document.getElementById('stats-filter-form').submit();
        });

        document.getElementById('inventory-status-filter')?.addEventListener('change', function() {
            this.closest('form').submit();
        });

        document.getElementById('stats-search')?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const statItems = document.querySelectorAll('#statsContainer .stat-item');
            statItems.forEach(item => {
                const name = item.dataset.name.toLowerCase();
                item.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        });

        // Equipment Chart - keep your existing implementation
        document.addEventListener('DOMContentLoaded', function() {
            // Chart initialization code...
            
            // Dashboard counts functionality
            function updateCountElement(selector, value) {
                const element = document.querySelector(selector);
                if (element) {
                    element.textContent = value;
                    element.classList.add('animate-pulse');
                    setTimeout(() => {
                        element.classList.remove('animate-pulse');
                    }, 500);
                }
            }

            function showErrorNotification(message) {
                console.error(message);
                // toastr.error(message, 'Error', {timeOut: 5000});
            }

            function updateDashboardCounts() {
                // Show loading state
                document.querySelectorAll('[data-name] .text-green-600').forEach(el => {
                    const originalText = el.dataset.originalText || el.textContent;
                    el.dataset.originalText = originalText;
                    el.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                });

                fetch('/api/dashboard-counts', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    console.log('Dashboard counts updated at:', data.lastUpdated);
                    
                    updateCountElement('[data-name="Total Staff"] .text-green-600', data.totalStaff);
                    updateCountElement('[data-name="Total Issued Equipment"] .text-green-600', data.totalIssuedEquipment);
                    updateCountElement('[data-name="Total Returned Equipment"] .text-green-600', data.totalReturnedEquipment);
                    updateCountElement('[data-name="Pending Requests"] .text-green-600', data.pendingRequests);
                })
                .catch(error => {
                    console.error('Error fetching dashboard counts:', error);
                    document.querySelectorAll('[data-name] .text-green-600').forEach(el => {
                        if (el.dataset.originalText) {
                            el.textContent = el.dataset.originalText;
                        }
                    });
                    showErrorNotification('Failed to update dashboard counts. Showing cached data.');
                });
            }

            // Initial load
            updateDashboardCounts();
            // Refresh counts periodically
            setInterval(updateDashboardCounts, 30000);
        });
    </script>
    @endsection
</x-app-layout>
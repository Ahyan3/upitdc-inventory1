<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-[#00553d] leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8 w-full">
            <div class="text-center mb-10 animate-fade-in w-full">
                <h2 class="text-base font-bold text-[#90143c]">UPITDC - Inventory System</h2>
                <p class="text-[0.65rem] text-[#00553d]">Welcome to your dashboard</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8 w-full" id="statsContainer">
                <a href="{{ route('staff.index') }}" class="bg-white rounded-xl shadow-md p-5 hover:bg-gray-100 transition duration-200 block stat-item border border-[#ffcc34]" data-name="Total Staff">
                    <h3 class="text-[0.65rem] font-semibold text-[#00553d]">Total Staff</h3>
                    <p class="text-base font-bold text-[#00553d]">{{ $totalStaff ?? 'N/A' }}</p>
                </a>
                <a href="{{ route('inventory') }}" class="bg-white rounded-xl shadow-md p-5 hover:bg-gray-100 transition duration-200 block stat-item border border-[#ffcc34]" data-name="Total Issued Equipment">
                    <h3 class="text-[0.65rem] font-semibold text-[#00553d]">Total Issued Equipment</h3>
                    <p class="text-base font-bold text-[#00553d]">{{ $totalIssuedEquipment ?? 'N/A' }}</p>
                </a>
                <a href="{{ route('inventory') }}" class="bg-white rounded-xl shadow-md p-5 hover:bg-gray-100 transition duration-200 block stat-item border border-[#ffcc34]" data-name="Total Returned Equipment">
                    <h3 class="text-[0.65rem] font-semibold text-[#00553d]">Total Returned Equipment</h3>
                    <p class="text-base font-bold text-[#00553d]">{{ $totalReturnedEquipment ?? 'N/A' }}</p>
                </a>
                <a href="{{ route('inventory') }}" class="bg-white rounded-xl shadow-md p-5 hover:bg-gray-100 transition duration-200 block stat-item border border-[#ffcc34]" data-name="Pending Requests">
                    <h3 class="text-[0.65rem] font-semibold text-[#00553d]">Pending Requests</h3>
                    <p class="text-base font-bold text-[#00553d]">{{ $pendingRequests ?? 'N/A' }}</p>
                </a>
                <a href="{{ route('inventory') }}" class="bg-white rounded-xl shadow-md p-5 hover:bg-gray-100 transition duration-200 block stat-item border border-[#ffcc34]" data-name="Active Issuances">
                    <h3 class="text-[0.65rem] font-semibold text-[#00553d]">Active Issuances</h3>
                    <p class="text-base font-bold text-[#00553d]">{{ $activeIssuances ?? 'N/A' }}</p>
                </a>
            </div>

            <!-- Filter for Stats with Search Bar -->
            <div class="bg-white rounded-xl shadow-md p-5 mb-8 w-full border border-[#ffcc34]">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <form id="stats-filter-form" method="GET" action="{{ route('dashboard') }}" class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2 w-full md:w-auto">
                        <div class="relative">
                            <input type="text" name="stats_search" id="stats-search" placeholder="Search stats..."
                                class="w-full pl-8 pr-3 py-1 border border-[#ffcc34] rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-[#00553d]"
                                value="{{ request('stats_search') }}">
                            <div class="absolute left-2 top-2 text-[#00553d]">
                                <i class="fas fa-search text-xs"></i>
                            </div>
                        </div>
                        <div class="relative">
                            <select name="time_filter" id="time-filter" class="appearance-none bg-gray-100 border border-[#ffcc34] rounded-md px-3 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#00553d]">
                                <option value="all" {{ request('time_filter') == 'all' ? 'selected' : '' }}>All Time</option>
                                <option value="day" {{ request('time_filter') == 'day' ? 'selected' : '' }}>Today</option>
                                <option value="week" {{ request('time_filter') == 'week' ? 'selected' : '' }}>This Week</option>
                                <option value="month" {{ request('time_filter') == 'month' ? 'selected' : '' }}>This Month</option>
                                <option value="year" {{ request('time_filter') == 'year' ? 'selected' : '' }}>This Year</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#00553d]">
                                <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                </svg>
                            </div>
                        </div>
                        <div class="relative">
                            <select name="type_filter" id="type-filter" class="appearance-none bg-gray-100 border border-[#ffcc34] rounded-md px-3 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#00553d]">
                                <option value="total" {{ request('type_filter') == 'total' ? 'selected' : '' }}>Total</option>
                                <option value="available" {{ request('type_filter') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="not_working" {{ request('type_filter') == 'not_working' ? 'selected' : '' }}>Not Working</option>
                                <option value="working" {{ request('type_filter') == 'working' ? 'selected' : '' }}>Working</option>
                                <option value="not_returned" {{ request('type_filter') == 'not_returned' ? 'selected' : '' }}>Not Returned</option>
                                <option value="returned" {{ request('type_filter') == 'returned' ? 'selected' : '' }}>Returned</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#00553d]">
                                <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                </svg>
                            </div>
                        </div>
                        <button type="submit" class="bg-[#90143c] text-white px-3 py-1 rounded-md text-xs hover:bg-[#003d2b] border border-[#ffcc34]">Filter</button>
                    </form>
                </div>
            </div>

            <!-- Recent Issuances -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 w-full border border-[#ffcc34]">
                <div class="bg-[#90143c] px-5 py-3">
                    <h2 class="text-xs font-semibold text-white">Recent Issuances</h2>
                </div>
                <div class="p-5">
                    <div class="overflow-x-auto w-full">
                        <table class="min-w-full table-auto divide-y divide-[#ffcc34]" aria-label="Recent Issuances">
                            <thead class="bg-[#ffcc34]">
                                <tr>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Staff Name</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Equipment</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Model/Brand</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Serial No.</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">PR No.</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Date Issued</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#ffcc34]">
                                @if ($issuances->isEmpty())
                                <tr>
                                    <td colspan="6" class="px-5 py-3 text-center text-[#00553d] text-xs">No Current Record</td>
                                </tr>
                                @else
                                @foreach ($issuances as $issuance)
                                <tr>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $issuance->staff->name ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $issuance->equipment->department->name ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $issuance->equipment->equipment_name ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $issuance->equipment->model_brand ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $issuance->equipment->serial_number ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $issuance->equipment->pr_number ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $issuance->issued_at }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $issuance->status == 'active' ? 'bg-[#ffcc34] text-[#00553d]' : 'bg-gray-100 text-[#00553d]' }}">{{ ucfirst($issuance->status) }}</span>
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
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 w-full border border-[#ffcc34]">
                <div class="bg-[#90143c] px-5 py-3 flex justify-between items-center">
                    <h2 class="text-xs font-semibold text-white">Inventory Log</h2>
                    <form method="GET" action="{{ route('dashboard') }}" class="flex space-x-2">
                        <input type="text" name="inventory_search" id="inventory-search" placeholder="Search inventory..."
                            class="px-3 py-1 rounded-md text-xs border border-[#ffcc34] focus:outline-none focus:ring-1 focus:ring-[#00553d]" value="{{ request('inventory_search') }}">
                        <select name="inventory_status" id="inventory-status-filter"
                            class="bg-gray-100 border border-[#ffcc34] rounded-md px-3 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#00553d]">
                            <option value="all" {{ request('inventory_status') == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="available" {{ request('inventory_status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="issued" {{ request('inventory_status') == 'issued' ? 'selected' : '' }}>Issued</option>
                            <option value="not_working" {{ request('inventory_status') == 'not_working' ? 'selected' : '' }}>Not Working</option>
                            <option value="working" {{ request('inventory_status') == 'working' ? 'selected' : '' }}>Working</option>
                            <option value="not_returned" {{ request('inventory_status') == 'not_returned' ? 'selected' : '' }}>Not Returned</option>
                            <option value="returned" {{ request('inventory_status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        </select>
                        <button type="submit" class="bg-[#90143c] text-white px-3 py-1 rounded-md text-xs hover:bg-[#003d2b] border border-[#ffcc34]">Filter</button>
                    </form>
                </div>
                <div class="p-5">
                    <div class="overflow-x-auto w-full">
                        <table class="min-w-full table-auto divide-y divide-[#ffcc34]" aria-label="Current Inventory">
                            <thead class="bg-[#ffcc34]">
                                <tr>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Staff Name</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Equipment</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Model/Brand</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Serial No.</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">PR No.</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Date Issued</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Status</th>
                                    <!-- <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Remarks</th>                                </tr> -->
                            </thead>
                            <tbody class="bg-white divide-y divide-[#ffcc34]">
                                @forelse($inventory as $item)
                                <tr>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->staff_name  }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->department->name ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->equipment_name }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->model_brand }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->serial_number }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->pr_number}}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->date_issued}}</td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        @php
                                        $statusClasses = [
                                        'available' => 'bg-[#ffcc34] text-[#00553d]',
                                        'not_working' => 'bg-[#90143c] text-white',
                                        'working' => 'bg-[#00553d] text-white',
                                        'not_returned' => 'bg-yellow-100 text-[#00553d]',
                                        'returned' => 'bg-gray-100 text-[#00553d]'
                                        ];
                                        $statusClass = $statusClasses[$item->status] ?? 'bg-gray-100 text-[#00553d]';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                <!-- <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $item->remarks}}</td> -->
                                @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-3 text-center text-[#00553d] text-xs">No inventory items found</td>
                                </tr>
                                @endforelse
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
                    <canvas id="equipmentChart" class="w-full h-64" data-equipment="{{ json_encode($equipmentData ?? []) }}"></canvas>
                </div>
            </div>
        </div>
    </div>
    <x-auth-footer />

    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    {{-- <script src="{{ asset('js/inventory.js') }}"></script> --}}

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

         // Chart initialization - FIXED VERSION
            function initializeChart() {
                const ctx = document.getElementById('equipmentChart');
                if (!ctx) {
                    console.log('Chart canvas not found');
                    return;
                }

                try {
                    const equipmentDataAttr = ctx.dataset.equipment;
                    if (!equipmentDataAttr) {
                        console.log('No equipment data found in canvas dataset');
                        return;
                    }

                    const equipmentData = JSON.parse(equipmentDataAttr);
                    if (!equipmentData || equipmentData.length === 0) {
                        console.log('Equipment data is empty');
                        return;
                    }

                    const labels = equipmentData.map(item => item.equipment_name || 'Unknown');
                    const data = equipmentData.map(item => item.issuance_count || 0);

                    // Destroy existing chart if it exists
                    if (window.equipmentChart) {
                        window.equipmentChart.destroy();
                    }

                    // Create new chart with proper Chart.js syntax
                    window.equipmentChart = new Chart(ctx, {
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
                            maintainAspectRatio: false,
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
                    });

                    console.log('Chart initialized successfully');
                } catch (error) {
                    console.error('Error initializing chart:', error);
                }
            }

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
                document.querySelectorAll('[data-name] .text-[#00553d]').forEach(el => {
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

                        updateCountElement('[data-name="Total Staff"] .text-[#00553d]', data.totalStaff);
                        updateCountElement('[data-name="Total Issued Equipment"] .text-[#00553d]', data.totalIssuedEquipment);
                        updateCountElement('[data-name="Total Returned Equipment"] .text-[#00553d]', data.totalReturnedEquipment);
                        updateCountElement('[data-name="Pending Requests"] .text-[#00553d]', data.pendingRequests);
                    })
                    .catch(error => {
                        console.error('Error fetching dashboard counts:', error);
                        document.querySelectorAll('[data-name] .text-[#00553d]').forEach(el => {
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
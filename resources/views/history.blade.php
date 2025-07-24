<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-brand-green leading-tight">
            {{ __('History') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8 py-12">
            <div class="text-center mb-10 animate-fade-in w-full">
                <h2 class="text-base font-bold text-brand-maroon">History</h2>
                <p class="text-[0.65rem] text-brand-green">View all inventory actions and current inventory</p>
            </div>

            <!-- History Logs Accordion -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden form-card mb-8 w-full border border-brand-yellow">
                <button class="accordion-toggle w-full flex justify-between items-center p-5 bg-brand-maroon text-white text-xs font-semibold rounded-t-xl hover:bg-[#6b102d] transition duration-200">
                    <span>History Logs</span>
                    <svg class="w-3 h-3 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="accordion-content p-6 hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto divide-y divide-brand-green" aria-label="History Logs">
                            <thead class="bg-brand-yellow">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">Action</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">Model</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">Action Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">IP Address</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">User Agent</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-brand-green">
                                @if ($history_logs->isEmpty())
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-xs text-brand-green">No Current Record</td>
                                    </tr>
                                @else
                                    @foreach ($history_logs as $log)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-brand-green">{{ $log->action }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-brand-green">{{ $log->model }} (ID: {{ $log->model_id }})</td>
                                            <td class="px-6 py-4 text-xs text-brand-green">{{ $log->description ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-brand-green">{{ $log->action_date }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-brand-green">{{ $log->ip_address ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 text-xs text-brand-green truncate max-w-xs">{{ $log->user_agent ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Links for History Logs -->
                    @if ($history_logs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="mt-6">
                            {{ $history_logs->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Inventory Log Accordion -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden form-card w-full border border-brand-yellow">
                <button class="accordion-toggle w-full flex justify-between items-center p-5 bg-brand-maroon text-white text-xs font-semibold rounded-t-xl hover:bg-[#6b102d] transition duration-200">
                    <span>Inventory Log</span>
                    <svg class="w-3 h-3 transform transition-transform duration-200 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="accordion-content p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                        <h3 class="text-xs font-semibold text-brand-green"></h3>
                        <form method="GET" action="{{ route('history') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <input type="text" name="inventory_search" id="inventory-search" placeholder="Search inventory..."
                                   class="px-4 py-2 rounded-md text-xs border border-brand-green focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-brand-green w-full sm:w-64"
                                   value="{{ request('inventory_search') }}">
                            <select name="inventory_status" id="inventory-status-filter"
                                    class="bg-white border border-brand-green rounded-md px-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-brand-green">
                                <option value="all" {{ request('inventory_status') == 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="available" {{ request('inventory_status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="in_use" {{ request('inventory_status') == 'in_use' ? 'selected' : '' }}>In Use</option>
                                <option value="maintenance" {{ request('inventory_status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="damaged" {{ request('inventory_status') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                            </select>
                            <button type="submit" class="text-white px-4 py-2 rounded-md text-xs hover:bg-[#6b102d] transition duration-200 border border-brand-yellow focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-brand-green"
                                    style="background-color: #90143c;">
                                Filter
                            </button>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto divide-y divide-brand-green" aria-label="Current Inventory">
                            <thead class="bg-brand-yellow">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">Staff Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">Equipment</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">Model/Brand</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">Serial No.</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">PR No.</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">Date Issued</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[0.65rem] font-medium text-brand-green uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-brand-green">
                                @forelse($inventory as $item)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-brand-green">{{ $item->staff_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-brand-green">{{ $item->department->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-brand-green">{{ $item->equipment_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-brand-green">{{ $item->model_brand ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-brand-green">{{ $item->serial_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-brand-green">{{ $item->pr_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-brand-green">{{ $item->date_issued }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusClasses = [
                                                    'available' => 'bg-green-100 text-green-800',
                                                    'in_use' => 'bg-blue-100 text-blue-800',
                                                    'maintenance' => 'bg-yellow-100 text-yellow-800',
                                                    'damaged' => 'bg-red-100 text-red-800'
                                                ];
                                                $statusClass = $statusClasses[$item->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-3 py-1 inline-flex text-[0.6rem] leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-xs text-brand-green">No inventory items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Links for Inventory Log -->
                    @if ($inventory instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="mt-6">
                            {{ $inventory->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <x-auth-footer />
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Accordion functionality
            const accordionToggles = document.querySelectorAll('.accordion-toggle');
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

            // Ensure only "Inventory Log" is open by default
            const historyLogsContent = document.querySelector('.accordion-content:first-of-type');
            const historyLogsIcon = document.querySelector('.accordion-toggle:first-of-type svg');
            const inventoryLogContent = document.querySelector('.accordion-content:last-of-type');
            const inventoryLogIcon = document.querySelector('.accordion-toggle:last-of-type svg');

            // Explicitly ensure History Logs is closed
            if (historyLogsContent && historyLogsIcon) {
                historyLogsContent.classList.add('hidden');
                historyLogsIcon.classList.remove('rotate-180');
            }

            // Explicitly ensure Inventory Log is open
            if (inventoryLogContent && inventoryLogIcon) {
                inventoryLogContent.classList.remove('hidden');
                inventoryLogIcon.classList.add('rotate-180');
            }
        });
    </script>
</x-app-layout>
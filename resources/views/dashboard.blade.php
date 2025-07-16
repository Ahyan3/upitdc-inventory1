<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center mb-10 animate-fade-in">
                <h2 class="text-2xl font-bold text-red-600">UPITDC - Inventory System</h2>
                <p class="text-lg text-gray-600">Welcome to your dashboard</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-700">Total Equipment</h3>
                        <div class="relative">
                            <form id="equipment-filter-form" method="GET" action="{{ route('dashboard') }}">
                                <select name="equipment_filter" id="equipment-filter" class="appearance-none bg-gray-100 border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-green-500">
                                    <option value="all" {{ request('equipment_filter') == 'all' ? 'selected' : '' }}>All Equipment</option>
                                    <option value="month" {{ request('equipment_filter') == 'month' ? 'selected' : '' }}>This Month</option>
                                    <option value="year" {{ request('equipment_filter') == 'year' ? 'selected' : '' }}>This Year</option>
                                    <option value="quarter" {{ request('equipment_filter') == 'quarter' ? 'selected' : '' }}>This Quarter</option>
                                </select>
                            </form>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-green-600">{{ $totalEquipment }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-700">Active Issuances</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $activeIssuances }}</p>
                </div>
            </div>

            <!-- Recent Issuances -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
                <div class="bg-gray-800 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Recent Issuances</h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" aria-label="Recent Issuances">
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
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No Current Record</td>
                                    </tr>
                                @else
                                    @foreach ($issuances as $issuance)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->staff->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->equipment->department->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->equipment->equipment_name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->equipment->model_brand ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->issued_at }}</td>
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
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gray-800 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-white">Inventory Log</h2>
                    <form method="GET" action="{{ route('dashboard') }}" class="flex space-x-2">
                        <input type="text" name="inventory_search" id="inventory-search" placeholder="Search inventory..." 
                               class="px-3 py-1 rounded-md text-sm" value="{{ request('inventory_search') }}">
                        <select name="inventory_status" id="inventory-status-filter" 
                                class="bg-gray-100 border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-green-500">
                            <option value="all" {{ request('inventory_status') == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="available" {{ request('inventory_status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="not_working" {{ request('inventory_status') == 'not_working' ? 'selected' : '' }}>Not Working</option>
                            <option value="working" {{ request('inventory_status') == 'working' ? 'selected' : '' }}>Working</option>
                            <option value="not_returned" {{ request('inventory_status') == 'not_returned' ? 'selected' : '' }}>Not Returned</option>
                            <option value="returned" {{ request('inventory_status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        </select>
                        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded-md text-sm hover:bg-green-600">Filter</button>
                    </form>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" aria-label="Current Inventory">
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
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->equipment_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->model_brand }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->serial_no }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->department->name ?? 'N/A' }}</td>
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
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No inventory items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    <script>
        // Auto-submit equipment filter form when selection changes
        document.getElementById('equipment-filter').addEventListener('change', function() {
            document.getElementById('equipment-filter-form').submit();
        });
    </script>
    @endsection
</x-app-layout>
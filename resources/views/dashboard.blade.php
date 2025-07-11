<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="flex min-h-screen bg-gray-50">
      
        <button id="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 bg-gray-800 text-white p-2 rounded-lg">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Main Content -->
        <div class="flex-1 container mx-auto px-4 py-8">
            <div class="text-center mb-10 animate-fade-in">
                <h2 class="text-2xl font-bold text-red-600">Dashboard</h2>
                <p class="text-lg text-black-600">Overview of your inventory system</p>
            </div>

            <!-- Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-700">Total Equipment</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $totalEquipment }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-700">Active Issuances</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $activeIssuances }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-700">Pending Requests</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $pendingRequests }}</p>
                </div>
            </div>

            <!-- Issuances -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in">
                <div class="bg-gray-800 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Current Issuances</h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" aria-label="Issuance Records">
                            <thead class="bg-green-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Staff</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Equipment</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Model</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Serial</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Issued</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">PR Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody id="issuanceTableBody" class="bg-white divide-y divide-gray-200">
                                @if ($issuances->whereNull('date_returned')->isEmpty())
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No Current Record</td>
                                    </tr>
                                @else
                                    @foreach ($issuances->whereNull('date_returned') as $issuance)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->staff_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->department }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->equipment->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->equipment->model_brand }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->equipment->serial_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->date_issued }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $issuance->pr_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
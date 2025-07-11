<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Dashboard Title and Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold text-red-600">Dashboard</h1>
                    <p class="mt-2 text-lg text-grey-600 font-medium">Overview of inventory system metrics</p>
                </div>
            </div>

            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                <div class="bg-red-100 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-red-700">Total Equipment</h3>
                    <p class="text-2xl font-bold text-red-800">5</p>
                </div>
                <div class="bg-green-100 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-green-700">Active Issuances</h3>
                    <p class="text-2xl font-bold text-green-800">5</p>
                </div>
                <div class="bg-yellow-100 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-yellow-700">Pending Requests</h3>
                    <p class="text-2xl font-bold text-yellow-800">0</p>
                </div>
            </div>

            <!-- Equipment Issuance Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-red-600 mb-4">Equipment Issuance Details</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Name of Staff</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Equipment Issued</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Model/Brand</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Date Issued</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Serial Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Date Returned</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">PR Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">John Doe</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">IT</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Laptop</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Dell XPS 13</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2025-06-01</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">SN123456</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">PR001</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">In use</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Jane Smith</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">HR</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Monitor</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">LG 27UK850</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2025-05-15</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">SN789012</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2025-07-01</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">PR002</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Returned</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- History Log Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-red-600 mb-4">History Log Summary</h3>
                    <p class="text-gray-600">Recent activity in the inventory system:</p>
                    <ul class="mt-2 space-y-2">
                        <li class="text-sm text-gray-900">2025-07-09: Equipment issued to John Doe (Laptop, SN123456)</li>
                        <li class="text-sm text-gray-900">2025-07-01: Equipment returned by Jane Smith (Monitor, SN789012)</li>
                        <li class="text-sm text-gray-900">2025-06-15: Pending request for new equipment (PR003)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
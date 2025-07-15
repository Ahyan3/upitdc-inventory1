    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('History') }}
            </h2>
        </x-slot>

        <div class="flex min-h-screen bg-gray-50">
            
            <button id="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 bg-gray-800 text-white p-2 rounded-lg">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Main Content -->
            <div class="flex-1 container mx-auto px-4 py-8">
                <div class="text-center mb-10 animate-fade-in">
                    <h2 class="text-2xl font-bold text-red-600">History</h2>
                    <p class="text-lg text-black-600">Equipment issuance and return logs</p>
                </div>

                <!-- History Listing -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in">
                    <div class="bg-gray-800 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white">Transaction History</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <div class="relative w-64">
                                <input type="text" id="searchInput" placeholder="Search history..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="Search history">
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <i class="fas fa-search"></i>
                                </div>
                            </div>
                            <button id="exportBtn" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center" aria-label="Export to CSV">
                                <i class="fas fa-file-export mr-2"></i> Export CSV
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" aria-label="History Records">
                                <thead class="bg-green-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Action</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Staff</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Department</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Model/Brand</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Details</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody id="historyTableBody" class="bg-white divide-y divide-gray-200">
                                    @if ($history_logs->isEmpty())
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No Current Record</td>
                                        </tr>
                                    @else
                                        @foreach ($history_logs as $log)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $log->action === 'Issued' ? 'bg-green-100 text-green-800' : ($log->action === 'Returned' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                                        {{ $log->action }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $log->staff_name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $log->department }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $log->model_brand }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $log->details }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $log->action_date }}</td>
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
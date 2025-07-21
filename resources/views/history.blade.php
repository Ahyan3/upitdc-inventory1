<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-gray-800 leading-tight">
            {{ __('History') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8 w-full">
            <div class="text-center mb-10 animate-fade-in w-full">
                <h2 class="text-lg font-bold text-red-600">History Logs</h2>
                <p class="text-xs text-gray-600">View all inventory actions</p>
            </div>

            <div class="bg-white rounded-xl shadow-md overflow-hidden w-full">
                <div class="bg-gray-800 px-6 py-4">
                    <h2 class="text-sm font-semibold text-white">Inventory History</h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto w-full">
                        <table class="min-w-full table-auto divide-y divide-gray-200" aria-label="History Logs">
                            <thead class="bg-green-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Action</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Model</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Action Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">IP Address</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">User Agent</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if ($history_logs->isEmpty())
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 text-xs">No Current Record</td>
                                </tr>
                                @else
                                @foreach ($history_logs as $log)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $log->action }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $log->model }} (ID: {{ $log->model_id }})</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $log->description ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $log->action_date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $log->ip_address ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">{{ $log->user_agent ?? 'N/A' }}</td>
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
    <x-auth-footer />
</x-app-layout>
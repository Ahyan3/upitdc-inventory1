<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-[#00553d] leading-tight">
            {{ __('History') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8 w-full">
            <div class="text-center mb-10 animate-fade-in w-full">
                <h2 class="text-base font-bold text-[#90143c]">History Logs</h2>
                <p class="text-[0.65rem] text-[#00553d]">View all inventory actions</p>
            </div>

            <div class="bg-white rounded-xl shadow-md overflow-hidden w-full border border-[#ffcc34]">
                <div class="bg-[#00553d] px-5 py-3">
                    <h2 class="text-xs font-semibold text-white">Inventory History</h2>
                </div>
                <div class="p-5">
                    <div class="overflow-x-auto w-full">
                        <table class="min-w-full table-auto divide-y divide-[#ffcc34]" aria-label="History Logs">
                            <thead class="bg-[#ffcc34]">
                                <tr>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Action</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Model</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">Action Date</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">IP Address</th>
                                    <th scope="col" class="px-5 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase tracking-wider">User Agent</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#ffcc34]">
                                @if ($history_logs->isEmpty())
                                <tr>
                                    <td colspan="6" class="px-5 py-3 text-center text-[#00553d] text-xs">No Current Record</td>
                                </tr>
                                @else
                                @foreach ($history_logs as $log)
                                <tr>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->action }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->model }} (ID: {{ $log->model_id }})</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->description ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->action_date }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->ip_address ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">{{ $log->user_agent ?? 'N/A' }}</td>
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
<div class="overflow-x-auto">
    <table class="min-w-full table-auto divide-y divide-[#ffcc34]" aria-label="History Logs">
        <thead class="table-header">
            <tr>
                <th scope="col"
                    class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[150px]">
                    Staff Name</th>
                <th scope="col"
                    class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[100px]">
                    Action</th>
                <th scope="col"
                    class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[100px]">
                    Equipment</th>
                <th scope="col"
                    class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[100px]">
                    Status</th>
                <th scope="col"
                    class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[120px]">
                    Model/Brand</th>
                <th scope="col"
                    class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[200px]">
                    Description</th>
                <th scope="col"
                    class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[120px]">
                    Action Date</th>
                <th class="text-center px-4 py-2">
                    <input type="checkbox" id="select-all-logs" title="Select All Logs"
                        class="form-checkbox h-4 w-4 text-[#00553d]">
                </th>

            </tr>
        </thead>
        <tbody id="historyTableBody" class="bg-white divide-y divide-[#ffcc34]">
            @if ($history_logs->isEmpty())
                <tr>
                    <td colspan="7"
                        class="px-4 py-4 text-center text-xs text-black bg-gradient-to-br from-gray-50 to-white rounded-lg border-2 border-dashed border-gray-300">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-history text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-xs text-gray-500 mb-2 font-medium">No history records found</p>
                        <p class="text-[0.6rem] text-gray-400">Actions will appear here once performed</p>
                    </td>
                </tr>
            @else
                @foreach ($history_logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors slide-up">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[150px]">
                            {{ $log->staff->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[120px]">
                            <span
                                class="status-indicator {{ $log->action == 'created' ? 'status-active' : ($log->action == 'updated' || $log->action == 'issued' || $log->action == 'returned' ? 'status-warning' : 'status-inactive') }}"></span>
                            {{ ucfirst($log->action) }}
                        </td>

                        <!-- Equipment Name -->
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[150px] truncate max-w-xs"
                            title="{{ $log->equipment?->equipment_name }}">
                            {{ $log->equipment?->equipment_name ?? 'N/A' }}
                        </td>

                       <!-- Equipment Status -->
                        <td class="px-4 py-3 whitespace-nowrap min-w-[100px]">
                            @if ($log->action === 'Created' || !$log->equipment)
                                <span class="text-gray-500 text-sm italic">N/A</span>
                            @else
                                <span
                                    class="status-indicator {{ $log->equipment->status == 'available' ? 'status-active' : ($log->equipment->status == 'in_use' ? 'status-warning' : ($log->equipment->status == 'maintenance' ? 'status-warning' : ($log->equipment->status == 'damaged' ? 'status-warning' : 'status-inactive'))) }}"></span>
                                <span
                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $log->equipment->status == 'available'
                                        ? 'bg-green-100 text-green-800'
                                        : ($log->equipment->status == 'in_use'
                                            ? 'bg-blue-100 text-blue-800'
                                            : ($log->equipment->status == 'maintenance'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : ($log->equipment->status == 'damaged'
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-gray-100 text-gray-800'))) }}">
                                    {{ ucfirst(str_replace('_', ' ', $log->equipment->status)) }}
                                </span>
                            @endif
                        </td>


                        <td class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[120px]">{{ $log->model_brand }}</td>
                        <td class="px-4 py-3 text-xs text-black min-w-[200px]">{{ $log->description ?? 'N/A' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[120px]">
                            @if ($log->action_date instanceof \Carbon\Carbon)
                                {{ $log->action_date->format('Y-m-d H:i:s') }}
                            @elseif (is_string($log->action_date) &&
                                    !empty($log->action_date) &&
                                    \Carbon\Carbon::canBeCreatedFromFormat($log->action_date, 'Y-m-d H:i:s'))
                                {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $log->action_date)->format('Y-m-d H:i:s') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="selected_logs[]" value="{{ $log->id }}"
                                class="log-checkbox">
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<div id="historyPagination" class="pagination-container mt-4">
    <select id="per-page-display"
        class="bg-white border border-[#ffcc34] rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#00553d] mr-2">
        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
    </select>
    <span class="pagination-info">
        Page {{ $history_logs->currentPage() }} to {{ $history_logs->currentPage() }} of {{ $history_logs->total() }}
        results
    </span>
    @if ($history_logs->onFirstPage())
        <span class="pagination-btn opacity-50 cursor-not-allowed">Previous</span>
    @else
        <a href="{{ $history_logs->previousPageUrl() }}" class="pagination-btn">Previous</a>
    @endif
    @foreach ($history_logs->getUrlRange(1, $history_logs->lastPage()) as $page => $url)
        <a href="{{ $url }}"
            class="pagination-btn {{ $history_logs->currentPage() == $page ? 'current' : '' }}">{{ $page }}</a>
    @endforeach
    @if ($history_logs->hasMorePages())
        <a href="{{ $history_logs->nextPageUrl() }}" class="pagination-btn">Next</a>
    @else
        <span class="pagination-btn opacity-50 cursor-not-allowed">Next</span>
    @endif
    <input type="number" id="historyPageJump" class="page-jump-input" placeholder="Page" min="1"
        max="{{ $history_logs->lastPage() }}" value="{{ $history_logs->currentPage() }}">
</div>

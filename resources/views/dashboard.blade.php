<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-[#00553d] leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <style>
        .accordion-content {
            transition: max-height 0.3s ease-in-out, padding 0.3s ease-in-out;
            max-height: 0;
            overflow: hidden;
            padding: 0 1rem;
        }

        .accordion-content.open {
            max-height: 2000px;
            padding: 1rem;
        }

        .setting-card {
            transition: all 0.2s ease-in-out;
        }

        .setting-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 85, 61, 0.12);
        }

        .btn-loading .spinner {
            display: inline-block;
        }

        .btn-loading .btn-text {
            display: none;
        }

        .spinner {
            display: none;
        }

        .fade-in {
            animation: fadeIn 0.4s ease-in;
        }

        .slide-up {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .status-indicator {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            margin-right: 6px;
            animation: pulse 2s infinite;
        }

        .status-active {
            background-color: #10b981;
        }

        .status-warning {
            background-color: #f59e0b;
        }

        .status-inactive {
            background-color: #ef4444;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .gradient-btn {
            background: linear-gradient(90deg, #90143c, #b01a47);
        }

        .gradient-btn:hover {
            background: linear-gradient(90deg, #6b102d, #8e1539);
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .pagination-container a,
        .pagination-container span {
            padding: 0.5rem 1rem;
            border: 1px solid #ffcc34;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            color: #00553d;
            transition: all 0.2s ease-in-out;
        }

        .pagination-container a:hover {
            background-color: #00553d;
            color: white;
            border-color: #00553d;
        }

        .pagination-container .current {
            background-color: #90143c;
            color: white;
            border-color: #90143c;
        }

        .page-input {
            width: 3rem;
            padding: 0.5rem;
            border: 1px solid #ffcc34;
            border-radius: 0.375rem;
            text-align: center;
            font-size: 0.75rem;
            color: #00553d;
        }

        .page-input:focus {
            outline: none;
            border-color: #00553d;
            box-shadow: 0 0 0 2px rgba(0, 85, 61, 0.2);
        }

        /* Card Icon Styles */
        .card-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #90143c, #b01a47);
            color: white;
            flex-shrink: 0;
        }

        .overview-card {
            transition: all 0.3s ease;
        }

        .overview-card:hover {
            transform: scale(1.02);
            background: linear-gradient(135deg, rgba(255, 204, 52, 0.05), rgba(0, 85, 61, 0.05));
        }

        .overview-card a:hover {
            text-decoration: underline;
        }

        .card-content {
            flex: 1;
            text-align: left;
        }

        .card-number {
            margin-bottom: 0.5rem;
        }

        /* System Overview Gradient Background */
        .system-overview {
            background: linear-gradient(135deg, #ffcc34, #00553d, #90143c);
        }

        @media (max-width: 768px) {
            .overview-card {
                text-align: center;
            }

            .overview-card .flex {
                flex-direction: column;
                align-items: center;
            }

            .card-content {
                text-align: center;
                margin-bottom: 0.5rem;
            }

            .card-icon {
                margin-top: 0.5rem;
            }

            .card-number {
                margin-bottom: 0.5rem;
            }
        }
    </style>

    <div class="flex min-h-screen bg-gray-50">
        <button id="toggleSidebar"
            class="md:hidden fixed top-3 left-3 z-50 bg-[#90143c] text-white p-1.5 rounded-md border border-[#ffcc34] shadow-md hover:shadow-lg transition-all duration-200">
            <i class="fas fa-bars text-xs"></i>
        </button>

        <div class="flex-1 container mx-auto px-4 py-8 max-w-full" style="padding-left: 2rem; padding-right: 2rem;">
            <div class="text-center mb-8 fade-in">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-full mb-4 shadow-lg relative">
                    <i class="fas fa-tachometer-alt text-white text-xl animate-spin" style="animation-duration: 8s;"></i>
                    <div
                        class="absolute inset-0 rounded-full bg-gradient-to-br from-[#90143c] to-[#b01a47] opacity-20 animate-ping">
                    </div>
                </div>
                <h1
                    class="text-2xl font-bold bg-gradient-to-r from-[#90143c] to-[#00553d] bg-clip-text text-transparent">
                    Dashboard</h1>
                <p class="text-xs text-[#00553d] opacity-80 max-w-sm mx-auto">Welcome, {{ auth()->user()->name }}</p>
            </div>

            <div id="alert-container" class="mb-4"></div>

            <!-- System Overview Section -->
            <div class="system-overview rounded-lg shadow-md border border-[#ffcc34] slide-up p-4 mb-8">
                <h3 class="text-xs font-bold text-white mb-3 flex items-center">
                    <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md mr-2">
                        <i class="fas fa-chart-bar text-white text-xs"></i>
                    </div>
                    System Overview
                </h3>

                {{-- Row 1 --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                    <div class="p-4 border border-[#ffcc34]/30 rounded bg-white shadow-sm overview-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-bold text-[#90143c] card-number">{{ $totalEquipment ?? 0 }}</div>
                                <div class="card-content">
                                    <div class="text-xs text-gray-600">Total Equipment</div>
                                    <a href="{{ route('inventory') }}" class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] block mt-1">View Details</a>
                                </div>
                            </div>
                            <span class="card-icon"><i class="fas fa-boxes text-xs"></i></span>
                        </div>
                    </div>
                    <div class="p-4 border border-[#ffcc34]/30 rounded bg-white shadow-sm overview-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-bold text-[#90143c] card-number">{{ $totalIssuedEquipment ?? 0 }}</div>
                                <div class="card-content">
                                    <div class="text-xs text-gray-600">Issued Equipment</div>
                                    <a href="{{ route('inventory') }}" class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] block mt-1">View Details</a>
                                </div>
                            </div>
                            <span class="card-icon"><i class="fas fa-handshake text-xs"></i></span>
                        </div>
                    </div>
                    <div class="p-4 border border-[#ffcc34]/30 rounded bg-white shadow-sm overview-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-bold text-[#90143c] card-number">{{ $totalReturnedEquipment ?? 0 }}</div>
                                <div class="card-content">
                                    <div class="text-xs text-gray-600">Returned Equipment</div>
                                    <a href="{{ route('inventory') }}" class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] block mt-1">View Details</a>
                                </div>
                            </div>
                            <span class="card-icon"><i class="fas fa-undo-alt text-xs"></i></span>
                        </div>
                    </div>
                    <div class="p-4 border border-[#ffcc34]/30 rounded bg-white shadow-sm overview-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-bold text-[#90143c] card-number">{{ $departmentsWithItems ?? 0 }}</div>
                                <div class="card-content">
                                    <div class="text-xs text-gray-600">Departments with Item</div>
                                    <a href="{{ route('inventory') }}" class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] block mt-1">View Details</a>
                                </div>
                            </div>
                            <span class="card-icon"><i class="fas fa-building text-xs"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Row 2 --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                    <div class="p-4 border border-[#ffcc34]/30 rounded bg-white shadow-sm overview-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-bold text-[#90143c] card-number">{{ $in_use ?? 0 }}</div>
                                <div class="card-content">
                                    <div class="text-xs text-gray-600">In Use Equipment</div>
                                </div>
                            </div>
                            <span class="card-icon"><i class="fas fa-cog text-xs"></i></span>
                        </div>
                    </div>
                    <div class="p-4 border border-[#ffcc34]/30 rounded bg-white shadow-sm overview-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-bold text-[#90143c] card-number">{{ $available ?? 0 }}</div>
                                <div class="card-content">
                                    <div class="text-xs text-gray-600">Available Equipment</div>
                                </div>
                            </div>
                            <span class="card-icon"><i class="fas fa-check-circle text-xs"></i></span>
                        </div>
                    </div>
                    <div class="p-4 border border-[#ffcc34]/30 rounded bg-white shadow-sm overview-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-bold text-[#90143c] card-number">{{ $maintenance ?? 0 }}</div>
                                <div class="card-content">
                                    <div class="text-xs text-gray-600">Under Maintenance</div>
                                </div>
                            </div>
                            <span class="card-icon"><i class="fas fa-wrench text-xs"></i></span>
                        </div>
                    </div>
                    <div class="p-4 border border-[#ffcc34]/30 rounded bg-white shadow-sm overview-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-bold text-[#90143c] card-number">{{ $damaged ?? 0 }}</div>
                                <div class="card-content">
                                    <div class="text-xs text-gray-600">Damaged Equipment</div>
                                </div>
                            </div>
                            <span class="card-icon"><i class="fas fa-exclamation-triangle text-xs"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Row 3 --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="p-4 border border-[#ffcc34]/30 rounded bg-white shadow-sm overview-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-bold text-[#90143c] card-number">{{ $totalDepartments ?? 0 }}</div>
                                <div class="card-content">
                                    <div class="text-xs text-gray-600">Total Departments</div>
                                    <a href="{{ route('settings') }}" class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] block mt-1">View Details</a>
                                </div>
                            </div>
                            <span class="card-icon"><i class="fas fa-sitemap text-xs"></i></span>
                        </div>
                    </div>
                    <div class="p-4 border border-[#ffcc34]/30 rounded bg-white shadow-sm overview-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-bold text-[#90143c] card-number">{{ $totalStaff ?? 0 }}</div>
                                <div class="card-content">
                                    <div class="text-xs text-gray-600">Total Staff</div>
                                    <a href="{{ route('staff.index') }}" class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] block mt-1">View Details</a>
                                </div>
                            </div>
                            <span class="card-icon"><i class="fas fa-users text-xs"></i></span>
                        </div>
                    </div>
                    <div class="p-4 border border-[#ffcc34]/30 rounded bg-white shadow-sm overview-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-bold text-[#90143c] card-number">{{ $activeStaff ?? 0 }}</div>
                                <div class="card-content">
                                    <div class="text-xs text-gray-600">Active Staff</div>
                                    <a href="{{ route('staff.index', ['status' => 'Active']) }}" class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] block mt-1">View Details</a>
                                </div>
                            </div>
                            <span class="card-icon"><i class="fas fa-user-check text-xs"></i></span>
                        </div>
                    </div>
                    <div class="p-4 border border-[#ffcc34]/30 rounded bg-white shadow-sm overview-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-bold text-[#90143c] card-number">{{ $resignedStaff ?? 0 }}</div>
                                <div class="card-content">
                                    <div class="text-xs text-gray-600">Resigned Staff</div>
                                    <a href="{{ route('staff.index', ['status' => 'Resigned']) }}" class="text-[0.6rem] text-[#00553d] hover:text-[#007a52] block mt-1">View Details</a>
                                </div>
                            </div>
                            <span class="card-icon"><i class="fas fa-user-times text-xs"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <!-- Inventory Log Section -->
                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center p-4 gradient-btn text-white transition-all duration-500"
                        data-target="inventory-section">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 bg-white/20 rounded-md">
                                <i class="fas fa-boxes text-base"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-semibold block">Inventory Log</span>
                                <span class="text-xs opacity-80">Manage equipment inventory</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="bg-white bg-opacity-30 text-xs px-2 py-0.5 rounded-full font-medium">{{ $inventory->total() ?? 'N/A' }}
                                items</span>
                            <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300 rotate-180"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="inventory-section" class="accordion-content open">
                        <div class="p-4">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
                                <h3 class="text-xs font-semibold text-[#00553d] flex items-center">
                                    <div class="p-1.5 bg-gradient-to-br from-[#00553d] to-[#007a52] rounded-md mr-2">
                                        <i class="fas fa-filter text-white text-xs"></i>
                                    </div>
                                    Filter Inventory
                                </h3>
                                <form method="GET" action="{{ route('dashboard') }}"
                                    class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <input type="text" name="inventory_search"
                                        value="{{ request('inventory_search') }}" placeholder="Search inventory..."
                                        class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-64">
                                    <button type="submit"
                                        class="gradient-btn px-6 py-3 text-white font-semibold rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300">
                                        <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                        <span class="btn-text"><i class="fas fa-filter mr-2"></i>Filter</span>
                                    </button>
                                </form>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-[#ffcc34]" aria-label="Inventory Log">
                                    <thead class="bg-[#ffcc34]">
                                        <tr>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Staff Name</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Department</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Equipment</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Model/Brand</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Serial No.</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                PR No.</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Date Issued</th>
                                            <th
                                                class="px-4 py-2 text-left text-[0.65rem] font-medium text-[#00553d] uppercase">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-[#ffcc34]">
                                        @forelse ($inventory as $item)
                                            <tr class="hover:bg-gray-50 slide-up">
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    @php
                                                        $firstIssuance = $item->issuances->first();
                                                    @endphp
                                                    {{ $firstIssuance
                                                        ? ($firstIssuance->staff->name ?? 'Unknown Staff')
                                                        : 'N/A' }}
                                                </td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $item->department->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $item->equipment_name }}</td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">{{ $item->model_brand }}
                                                </td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    {{ $item->serial_number }}</td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">{{ $item->pr_number }}
                                                </td>
                                                <td class="px-4 py-2 text-xs text-[#00553d]">
                                                    @if ($item->date_issued instanceof \Carbon\Carbon)
                                                        {{ $item->date_issued->format('Y-m-d H:i') }}
                                                    @elseif (is_string($item->date_issued) &&
                                                            !empty($item->date_issued) &&
                                                            \Carbon\Carbon::canBeCreatedFromFormat($item->date_issued, 'Y-m-d H:i'))
                                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:s', $item->date_issued)->format('Y-m-d H:i') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-xs">
                                                    <span class="flex items-center">
                                                        <span
                                                            class="status-indicator {{ $item->status == 'available' ? 'status-active' : ($item->status == 'not_working' ? 'status-inactive' : ($item->status == 'working' ? 'status-active' : ($item->status == 'not_returned' ? 'status-warning' : 'status-active'))) }}"></span>
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : ($item->status == 'not_working' ? 'bg-red-100 text-red-700' : ($item->status == 'working' ? 'bg-green-100 text-green-700' : ($item->status == 'not_returned' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700'))) }}">
                                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                        </span>
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8"
                                                    class="px-4 py-2 text-center text-xs text-[#00553d] bg-gradient-to-br from-gray-50 to-white rounded-lg border-2 border-dashed border-gray-300">
                                                    <div
                                                        class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                                        <i class="fas fa-boxes text-2xl text-gray-400"></i>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mb-2 font-medium">No inventory
                                                        items found</p>
                                                    <p class="text-[0.6rem] text-gray-400">Add items to the inventory
                                                        to get started</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination-container mt-4">
                                <span class="text-xs text-[#00553d]">
                                    Page {{ $inventory->currentPage() }} of {{ $inventory->lastPage() }}
                                </span>
                                {{ $inventory->appends(request()->query())->links() }}
                                <form method="GET" action="{{ route('dashboard') }}" class="inline-flex items-center gap-2">
                                    @foreach (request()->query() as $key => $value)
                                        @if ($key !== 'page')
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endif
                                    @endforeach
                                    <input type="number" name="page" min="1" max="{{ $inventory->lastPage() }}"
                                        value="{{ $inventory->currentPage() }}" class="page-input"
                                        placeholder="Page">
                                    <button type="submit"
                                        class="gradient-btn px-3 py-1 text-white font-semibold rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg">
                                        Go
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
        <script>
            // Accordion Toggle with Retry Mechanism
            function initializeAccordions() {
                const accordionToggles = document.querySelectorAll('.accordion-toggle');
                accordionToggles.forEach(toggle => {
                    toggle.addEventListener('click', function() {
                        const target = document.getElementById(toggle.dataset.target);
                        const icon = toggle.querySelector('.accordion-icon');
                        if (!target || !icon) {
                            console.error('Accordion target or icon not found for toggle:', toggle.dataset.target);
                            return;
                        }
                        const isOpen = target.classList.contains('open');
                        target.classList.toggle('open');
                        icon.classList.toggle('rotate-180');
                        target.style.maxHeight = isOpen ? '0' : (target.scrollHeight + 30 || 2000) + 'px';
                        if (target.classList.contains('open')) {
                            setTimeout(() => {
                                target.style.animation = 'fadeIn 0.3s ease-out';
                            }, 100);
                        }
                    });
                });

                // Initialize inventory log accordion as open with retry mechanism
                function initializeInventoryLog() {
                    const inventoryLogContent = document.getElementById('inventory-section');
                    const inventoryLogIcon = document.querySelector('[data-target="inventory-section"] .accordion-icon');
                    if (!inventoryLogContent || !inventoryLogIcon) {
                        console.warn('Inventory log elements not found. Retrying...');
                        setTimeout(initializeInventoryLog, 100);
                        return;
                    }

                    inventoryLogContent.classList.add('open');
                    inventoryLogIcon.classList.add('rotate-180');
                    const maxHeight = inventoryLogContent.scrollHeight > 0 ? (inventoryLogContent.scrollHeight + 30) : 2000;
                    inventoryLogContent.style.maxHeight = maxHeight + 'px';

                    // Update max-height on window resize
                    window.addEventListener('resize', () => {
                        if (inventoryLogContent.classList.contains('open')) {
                            const newMaxHeight = inventoryLogContent.scrollHeight > 0 ? (inventoryLogContent.scrollHeight + 30) : 2000;
                            inventoryLogContent.style.maxHeight = newMaxHeight + 'px';
                        }
                    });
                }

                initializeInventoryLog();
            }

            // Utility Functions
            function showAlert(message, type) {
                const alertContainer = document.getElementById('alert-container');
                const alertDiv = document.createElement('div');
                alertDiv.className = `p-3 rounded-lg text-xs flex items-center space-x-2 ${
                    type === 'error' ? 'bg-red-100 text-red-700 border-red-200' : 
                    type === 'success' ? 'bg-green-100 text-green-700 border-green-200' : 
                    'bg-blue-100 text-blue-700 border-blue-200'
                } border fade-in`;
                alertDiv.innerHTML = `
                    <i class="fas ${
                        type === 'error' ? 'fa-exclamation-circle' : 
                        type === 'success' ? 'fa-check-circle' : 
                        'fa-info-circle'
                    } text-base"></i>
                    <span>${message}</span>
                `;
                alertContainer.appendChild(alertDiv);
                setTimeout(() => {
                    alertDiv.classList.remove('fade-in');
                    alertDiv.classList.add('fade-out');
                    setTimeout(() => alertDiv.remove(), 300);
                }, 3000);
            }

            // Sidebar Toggle
            const toggleSidebar = document.getElementById('toggleSidebar');
            const sidebar = document.querySelector('.sidebar');
            if (toggleSidebar && sidebar) {
                toggleSidebar.addEventListener('click', function() {
                    sidebar.classList.toggle('hidden');
                });
            }

            // Initialize Accordions
            initializeAccordions();

            // Handle Session Alerts
            @if (session('success'))
                showAlert('{{ session('success') }}', 'success');
            @endif
            @if (session('error'))
                showAlert('{{ session('error') }}', 'error');
            @endif
            @if ($errors->any())
                showAlert('{{ $errors->first() }}', 'error');
            @endif
        </script>
    @endsection
    <x-auth-footer />
</x-app-layout>
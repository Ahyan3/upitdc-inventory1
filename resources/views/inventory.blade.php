<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-[#00553d] leading-tight">{{ __('Inventory') }}</h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8 max-w-full" style="padding-left: 2rem; padding-right: 2rem;">
            <!-- CSRF Token -->
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <!-- Styles -->
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                .accordion-content {
                    transition: max-height 0.3s ease-in-out, padding 0.3s ease-in-out;
                    max-height: 0;
                    overflow: hidden;
                    padding: 0 1rem;
                }

                .accordion-content.open {
                    padding: 1rem 1rem 1.5rem 1rem;
                    max-height: 2000px;
                }

                .equipment-content {
                    transition: max-height 0.3s ease-in-out, padding 0.3s ease-in-out;
                    max-height: 0;
                    overflow: hidden;
                    padding: 0 1rem;
                }

                .equipment-content.open {
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

                .status-available,
                .status-working,
                .status-in_use {
                    background-color: #10b981;
                }

                .status-maintenance {
                    background-color: #f59e0b;
                }

                .status-damaged {
                    background-color: #ef4444;
                }

                @keyframes pulse {

                    0%,
                    100% {
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
                    margin-bottom: 1rem;
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

                .page-jump-input {
                    width: 4rem;
                    padding: 0.5rem;
                    border: 1px solid #ffcc34;
                    border-radius: 0.375rem;
                    font-size: 0.75rem;
                    color: #00553d;
                    text-align: center;
                }

                .page-jump-input:focus {
                    outline: none;
                    ring: 2px;
                    ring-color: #00553d;
                }

                .chart-container {
                    position: relative;
                    height: 16rem;
                    overflow: hidden;
                }

                .stats-container {
                    overflow-x: auto;
                    scrollbar-width: thin;
                    scrollbar-color: #ffcc34 #f1f5f9;
                }

                .stats-container::-webkit-scrollbar {
                    height: 8px;
                }

                .stats-container::-webkit-scrollbar-track {
                    background: #f1f5f9;
                }

                .stats-container::-webkit-scrollbar-thumb {
                    background-color: #ffcc34;
                    border-radius: 4px;
                }

                .table-header {
                    background: linear-gradient(135deg, #ffcc34, #ffdb66);
                }

                .filter-form {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 0.5rem;
                }

                .filter-form input,
                .filter-form select,
                .filter-form button {
                    padding: 0.5rem 0.75rem;
                    font-size: 0.75rem;
                }

                .filter-loading {
                    opacity: 0.7;
                    pointer-events: none;
                }

                .modal {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.6);
                    z-index: 50;
                    justify-content: center;
                    align-items: center;
                    backdrop-filter: blur(5px);
                    animation: fadeIn 0.3s ease-out;
                }

                .modal.show {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .modal-content {
                    background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
                    {{--  background-color: white;  --}} border-radius: 16px;
                    padding: 1.5rem;
                    width: 100%;
                    max-width: 800px;
                    overflow-y: auto;
                    border: 1px solid #ffcc34;
                    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
                    position: relative;
                    animation: slideIn 0.3s ease-out;
                    border: 1px solid #e0e0e0;
                }

                .modal-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 1rem;
                }

                @media (max-width: 640px) {
                    .modal-grid {
                        grid-template-columns: 1fr;
                    }
                }

                .close-btn {
                    background: none;
                    border: none;
                    color: rgba(255, 255, 255, 0.8);
                    font-size: 20px;
                    cursor: pointer;
                    padding: 8px;
                    border-radius: 6px;
                    transition: all 0.2s ease;
                    width: 36px;
                    height: 36px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .close-btn:hover {
                    background-color: rgba(255, 255, 255, 0.1);
                    color: white;
                    transform: scale(1.1);
                }

                /* Equipment Grid */
                .equipment-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 20px;
                }

                .equipment-card {
                    background: white;
                    border-radius: 12px;
                    padding: 20px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                    border: 1px solid #e8e8e8;
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                }

                .equipment-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
                }

                .equipment-header {
                    display: flex;
                    align-items: center;
                    margin-bottom: 16px;
                    padding-bottom: 12px;
                    border-bottom: 1px solid #f0f0f0;
                }

                .equipment-image {
                    width: 60px;
                    height: 60px;
                    background: linear-gradient(135deg, #00553d 0%, #004d35 100%);
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 16px;
                    box-shadow: 0 4px 12px rgba(0, 85, 61, 0.2);
                }

                .equipment-image i {
                    color: white;
                    font-size: 24px;
                }

                .equipment-info h4 {
                    color: #00553d;
                    font-size: 16px;
                    font-weight: 600;
                    margin-bottom: 4px;
                }

                .equipment-code {
                    color: #666;
                    font-size: 12px;
                    background: #f8f8f8;
                    padding: 2px 8px;
                    border-radius: 4px;
                    display: inline-block;
                }

                /* Equipment Details */
                .equipment-details {
                    display: grid;
                    gap: 12px;
                }

                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 8px 0;
                    border-bottom: 1px solid #f5f5f5;
                }

                .detail-row:last-child {
                    border-bottom: none;
                }

                .detail-label {
                    font-weight: 500;
                    color: #333;
                    font-size: 14px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }

                .detail-label i {
                    color: #90143c;
                    width: 16px;
                }

                .detail-value {
                    color: #666;
                    font-size: 14px;
                    text-align: right;
                }

                /* Status Badge */
                .status-badge {
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 500;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }

                .status-available {
                    background-color: #e8f5e8;
                    color: #2d5a2d;
                }

                .status-maintenance {
                    background-color: #fff3cd;
                    color: #856404;
                }

                .status-unavailable {
                    background-color: #f8d7da;
                    color: #721c24;
                }

                /* Loading State */
                .loading {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 40px;
                    color: #666;
                }

                .spinner {
                    width: 12px;
                    height: 12px;
                    border: 3px solid #f3f3f3;
                    border-top: 3px solid #00553d;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin-right: 12px;
                }

                /* Error State */
                .error-message {
                    background: #f8d7da;
                    color: #721c24;
                    padding: 16px;
                    border-radius: 8px;
                    text-align: center;
                    border: 1px solid #f5c6cb;
                }

                /* Animations */
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                    }

                    to {
                        opacity: 1;
                    }
                }

                @keyframes slideIn {
                    from {
                        opacity: 0;
                        transform: scale(0.9) translateY(-20px);
                    }

                    to {
                        opacity: 1;
                        transform: scale(1) translateY(0);
                    }
                }

                @keyframes spin {
                    0% {
                        transform: rotate(0deg);
                    }

                    100% {
                        transform: rotate(360deg);
                    }
                }

                /* Responsive Design */
                @media (max-width: 768px) {
                    .modal-content {
                        width: 95%;
                        margin: 10px;
                        max-height: 95vh;
                    }

                    .equipment-grid {
                        grid-template-columns: 1fr;
                    }

                    .modal-header {
                        padding: 20px 20px 16px 20px;
                    }

                    .modal-body {
                        padding: 20px;
                    }

                    .modal-title {
                        font-size: 16px;
                    }
                }
            </style>

            <!-- Header -->
            <div class="text-center mb-8 fade-in">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-full mb-4 shadow-lg relative">
                    <i class="fas fa-boxes text-white text-xl animate-spin" style="animation-duration: 8s;"></i>
                    <div
                        class="absolute inset-0 rounded-full bg-gradient-to-br from-[#90143c] to-[#b01a47] opacity-20 animate-ping">
                    </div>
                </div>
            </div>

            <!-- Overview Stats -->
            <div
                class="stats-container flex flex-row gap-3 max-w-full mx-auto justify-center items-center mb-6 slide-up">
                @php
                    $totalItems = isset($inventory) ? $inventory->total() : 0;
                    $inUseItems = isset($inventory) ? $inventory->where('status', 'in_use')->count() : 0;
                    $availableItems = isset($inventory) ? $inventory->where('status', 'available')->count() : 0;
                    $maintenanceItems = isset($inventory) ? $inventory->where('status', 'maintenance')->count() : 0;
                    $damagedItems = isset($inventory) ? $inventory->where('status', 'damaged')->count() : 0;
                    $overviewStats = [
                        ['label' => 'Total Items', 'value' => $totalItems],
                        ['label' => 'In Use', 'value' => $inUseItems],
                        ['label' => 'Available', 'value' => $availableItems],
                        ['label' => 'Maintenance', 'value' => $maintenanceItems],
                        ['label' => 'Damaged', 'value' => $damagedItems],
                    ];
                @endphp
                @foreach ($overviewStats as $index => $stat)
                    <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30 min-w-[150px]">
                        <div class="text-base font-bold text-[#90143c]">{{ $stat['value'] }}</div>
                        <div class="text-xs text-gray-600">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Alert Container -->
            <div id="alert-container" class="mb-4"></div>

            <!-- Action Buttons -->
            <div class="flex justify-between gap-4 mb-6">
                <button id="issue-equipment-btn"
                    class="gradient-btn flex items-center justify-center space-x-3 px-4 py-2 text-xs font-semibold text-white rounded-lg border border-[#ffcc34] shadow-md hover:shadow-lg transition-all duration-300 w-1/2">
                    <div class="p-1.5 bg-white/20 rounded-md">
                        <i class="fas fa-plus-circle text-base"></i>
                    </div>
                    <div class="text-left">
                        <span class="text-xs font-semibold block">Issue Equipment</span>
                        <span class="text-xs opacity-80">Assign equipment to staff</span>
                    </div>
                </button>
                <button id="return-equipment-btn"
                    class="gradient-btn flex items-center justify-center space-x-3 px-4 py-2 text-xs font-semibold text-white rounded-lg border border-[#ffcc34] shadow-md hover:shadow-lg transition-all duration-300 w-1/2">
                    <div class="flex items-center space-x-2">
                        <div class="p-1.5 bg-white/20 rounded-md">
                            <i class="fas fa-undo text-base"></i>
                        </div>
                        <div class="text-left">
                            <span class="text-xs font-semibold block">Return Equipment</span>
                            <span class="text-xs opacity-80">{{ $issuances->count() }}
                                {{ $issuances->count() === 1 ? 'item' : 'items' }}</span>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Issue Equipment Modal -->
            <div id="issue-equipment-modal" class="modal">
                <div class="modal-content">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-semibold text-[#00553d] flex items-center">
                            <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md mr-2">
                                <i class="fas fa-plus-circle text-white text-xs"></i>
                            </div>
                            Issue Equipment
                        </h3>
                        <button id="close-issue-modal"
                            class="text-[#90143c] hover:text-[#b01a47] transition-all duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form id="issueForm" action="{{ route('inventory.issue') }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="modal-grid">
                            <div class="relative group">
                                <label for="staff_name"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Staff Name *</label>
                                <select name="staff_name" id="staff_name" required
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                    <option value="">Select Staff</option>
                                    @foreach ($activeStaff as $staffMember)
                                        <option value="{{ $staffMember->name }}"
                                            data-department="{{ $staffMember->department }}">
                                            {{ $staffMember->name }} ({{ $staffMember->department }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="department_id"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Department *</label>
                                <select name="department_id" id="department_id" required
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">

                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative group">
                                <label for="equipment_name"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Equipment Name
                                    *</label>
                                <input type="text" name="equipment_name" id="equipment_name" required
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                                <div
                                    class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                    <i class="fas fa-box text-xs"></i>
                                </div>
                            </div>
                            <div class="relative group">
                                <label for="model_brand"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Model/Brand *</label>
                                <input type="text" name="model_brand" id="model_brand" required
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                                <div
                                    class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                    <i class="fas fa-tag text-xs"></i>
                                </div>
                            </div>
                            <div class="relative group">
                                <label for="date_issued"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Date Issued *</label>
                                <input type="datetime-local" name="date_issued" id="date_issued"
                                    value="{{ now()->format('Y-m-d\TH:i') }}" required
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                            </div>
                            <div class="relative group">
                                <label for="serial_number"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Serial Number *</label>
                                <input type="text" name="serial_number" id="serial_number" required
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                                <div
                                    class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                    <i class="fas fa-barcode text-xs"></i>
                                </div>
                            </div>
                            <div class="relative group">
                                <label for="pr_number" class="block text-[0.65rem] font-medium text-[#00553d] mb-1">PR
                                    Number *</label>
                                <input type="text" name="pr_number" id="pr_number" required
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                                <div
                                    class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                    <i class="fas fa-hashtag text-xs"></i>
                                </div>
                            </div>
                            <div>
                                <label for="status"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Status *</label>
                                <select name="status" id="status" required
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                    <option value="in_use">In Use</option>
                                    <option value="available">Available</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="damaged">Damaged</option>
                                </select>
                                {{--  <small class="form-text text-muted">
                                 Note: Only equipment marked as "In Use" will appear in the Return Equipment section.
                                </small>  --}}
                            </div>
                            <div class="col-span-2">
                                <label for="remarks"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Remarks</label>
                                <textarea name="remarks" id="remarks"
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" id="cancel-issue-modal"
                                class="px-4 py-2 text-xs font-semibold text-[#00553d] bg-gray-100 rounded-lg hover:bg-gray-200 border border-gray-200 transition-all duration-200">Cancel</button>
                            <button type="submit"
                                class="gradient-btn px-4 py-2 text-xs font-semibold text-white rounded-lg border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300">
                                <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                <span class="btn-text"><i class="fas fa-plus mr-2"></i>Issue Equipment</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Return Equipment Modal -->
            <div id="return-equipment-modal" class="modal">
                <div class="modal-content max-h-[80vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-semibold text-[#00553d] flex items-center">
                            <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md mr-2">
                                <i class="fas fa-undo text-white text-xs"></i>
                            </div>
                            Return Equipment
                        </h3>
                        <button id="close-return-modal"
                            class="text-[#90143c] hover:text-[#b01a47] transition-all duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        @if ($issuances->isEmpty())
                            <div
                                class="text-center text-[#00553d] py-4 text-xs bg-gradient-to-br from-gray-50 to-white rounded-lg border-2 border-dashed border-gray-300">
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-undo text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-xs text-gray-500 mb-2 font-medium">No equipment currently issued out</p>
                                <p class="text-[0.6rem] text-gray-400">Issue equipment to start tracking returns</p>
                            </div>
                        @else
                            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="relative">
                                    <input type="text" id="returnSearch" placeholder="Search equipment..."
                                        class="w-full pl-8 pr-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                    <div class="absolute left-2 top-2.5 text-[#00553d]">
                                        <i class="fas fa-search text-xs"></i>
                                    </div>
                                </div>
                                <div>
                                    <select id="returnDepartmentFilter"
                                        class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                        <option value="">All Departments</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->name }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{--  <div>
                                    <select id="returnStatusFilter"
                                        class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                        <option value="">All Equipment</option>
                                        <option value="laptop">Laptops</option>
                                        <option value="desktop">Desktops</option>
                                        <option value="monitor">Monitors</option>
                                    </select>
                                </div>  --}}
                            </div>
                            <div class="space-y-4" id="returnEquipmentContainer">

                                @foreach ($issuances as $issuance)
                                    <div
                                        class="equipment-accordion bg-gray-50 rounded-lg overflow-hidden shadow-sm border border-[#ffcc34] slide-up">
                                        <button
                                            class="equipment-toggle w-full flex justify-between items-center p-4 bg-[#ffcc34] hover:bg-[#e6b82f] transition duration-200"
                                            data-target="equipment-content-{{ $issuance->id }}">
                                            <div class="text-left">
                                                <h3 class="font-medium text-xs text-black">
                                                    {{ $issuance->equipment->equipment_name ?? 'N/A' }} •
                                                    {{ $issuance->equipment->model_brand ?? 'N/A' }} •
                                                    {{ $issuance->equipment->serial_number ?? 'N/A' }} •
                                                    {{ $issuance->equipment->pr_number ?? 'N/A' }}</h3>
                                                <p class="text-[0.65rem] text-[#00553d]">
                                                    {{ $issuance->staff->name ?? 'N/A' }} •
                                                    {{ $issuance->equipment->department->name ?? 'N/A' }} •
                                                    {{ $issuance->equipment->date_issued ?? 'N/A' }}</p>
                                            </div>
                                            <svg class="w-4 h-4 transform transition-transform duration-300"
                                                fill="none" stroke="#00553d" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        <div id="equipment-content-{{ $issuance->id }}" class="equipment-content">
                                            <div class="p-4">
                                                <form action="{{ route('inventory.return', $issuance) }}"
                                                    method="POST" class="space-y-3">
                                                    @csrf
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label
                                                                class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Equipment</label>
                                                            <input type="text"
                                                                value="{{ $issuance->equipment->equipment_name ?? 'N/A' }}"
                                                                disabled
                                                                class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg bg-gray-100 text-xs">
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Staff</label>
                                                            <input type="text"
                                                                value="{{ $issuance->staff->name ?? 'N/A' }}" disabled
                                                                class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg bg-gray-100 text-xs">
                                                        </div>
                                                        <div class="relative group">
                                                            <label for="date_returned_{{ $issuance->id }}"
                                                                class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Date
                                                                Returned *</label>
                                                            <input type="date" name="date_returned"
                                                                id="date_returned_{{ $issuance->id }}"
                                                                value="{{ now()->format('Y-m-d') }}" required
                                                                class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs group-hover:shadow-md transition-all duration-300">
                                                            <div
                                                                class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label for="returned_condition{{ $issuance->id }}"
                                                                class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Condition</label>
                                                            <select name="returned_condition"
                                                                id="returned_condition{{ $issuance->id }}"
                                                                class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs"
                                                                required>
                                                                <option value="" disabled
                                                                    {{ old('returned_condition') ? '' : 'selected' }}>
                                                                    Select Condition</option>
                                                                <option value="good"
                                                                    {{ old('returned_condition') == 'good' ? 'selected' : '' }}>
                                                                    Good</option>
                                                                <option value="damaged"
                                                                    {{ old('returned_condition') == 'damaged' ? 'selected' : '' }}>
                                                                    Damaged</option>
                                                                <option value="lost"
                                                                    {{ old('returned_condition') == 'lost' ? 'selected' : '' }}>
                                                                    Lost</option>
                                                            </select>

                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <label for="remarks_{{ $issuance->id }}"
                                                                class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Remarks</label>
                                                            <textarea name="remarks" id="remarks_{{ $issuance->id }}"
                                                                class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">{{ $issuance->return_notes ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                    <button type="submit"
                                                        class="gradient-btn w-full mt-4 px-4 py-2 text-xs font-semibold text-white rounded-lg border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300">
                                                        <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                                        <span class="btn-text"><i class="fas fa-undo mr-2"></i>Return
                                                            Equipment</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="pagination-container mt-6">
                                <span class="pagination-info">
                                    Page {{ $issuances->currentPage() }} to {{ $issuances->currentPage() }} of
                                    {{ $issuances->total() }} results
                                </span>
                                <a href="{{ $issuances->previousPageUrl() ? $issuances->previousPageUrl() . '&' . http_build_query(request()->query()) : '#' }}"
                                    class="pagination-btn {{ $issuances->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $issuances->onFirstPage() ? 'disabled' : '' }}>
                                    Previous
                                </a>
                                @foreach ($issuances->getUrlRange(1, $issuances->lastPage()) as $page => $url)
                                    <a href="{{ $url . '&' . http_build_query(request()->query()) }}"
                                        class="pagination-btn {{ $issuances->currentPage() == $page ? 'current' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                                <a href="{{ $issuances->nextPageUrl() ? $issuances->nextPageUrl() . '&' . http_build_query(request()->query()) : '#' }}"
                                    class="pagination-btn {{ $issuances->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}"
                                    {{ $issuances->hasMorePages() ? '' : 'disabled' }}>
                                    Next
                                </a>
                                <input type="number" id="returnPageJump" class="page-jump-input" placeholder="Page"
                                    min="1" max="{{ $issuances->lastPage() }}"
                                    value="{{ $issuances->currentPage() }}">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Equipment Details Modal -->
            <div id="equipment-details-modal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title">
                            <div class="modal-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            Equipment Details
                        </div>
                        <button id="close-details-modal" class="close-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="equipment-details-content">
                            <!-- Content will be loaded via AJAX -->
                        </div>
                    </div>
                </div>
            </div>


            <!-- Inventory Logs Accordion -->
            <div
                class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up my-6">
                <button
                    class="accordion-toggle w-full flex justify-between items-center p-4 gradient-btn text-white transition-all duration-500"
                    data-target="inventory-log">
                    <div class="flex items-center space-x-3">
                        <div class="p-1.5 bg-white/20 rounded-md">
                            <i class="fas fa-boxes text-base"></i>
                        </div>
                        <div class="text-left">
                            <span class="text-xs font-semibold block">Inventory Logs</span>
                            <span class="text-xs opacity-80">View current inventory items</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span
                            class="bg-white bg-opacity-30 text-xs px-2 py-0.5 rounded-full font-medium">{{ isset($inventory) ? $inventory->total() : 0 }}
                            items</span>
                        <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300 rotate-180"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div id="inventory-log" class="accordion-content open">
                    <div class="p-4">
                        <!-- Filter Form -->
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
                            <form id="inventory-filter-form" class="filter-form w-full sm:w-auto" method="GET"
                                action="{{ route('inventory') }}">
                                <input type="text" name="inventory_search" id="inventory-search"
                                    placeholder="Search staff or equipment..."
                                    class="border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-56"
                                    value="{{ request('inventory_search') }}">
                                <select name="inventory_status" id="inventory-status-filter"
                                    class="bg-white border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                    <option value="all"
                                        {{ request('inventory_status') == 'all' ? 'selected' : '' }}>All Status
                                    </option>
                                    <option value="in_use"
                                        {{ request('inventory_status') == 'in_use' ? 'selected' : '' }}>In Use
                                    </option>
                                    <option value="available"
                                        {{ request('inventory_status') == 'available' ? 'selected' : '' }}>Available
                                    </option>
                                    <option value="maintenance"
                                        {{ request('inventory_status') == 'maintenance' ? 'selected' : '' }}>
                                        Maintenance
                                    </option>
                                    <option value="damaged"
                                        {{ request('inventory_status') == 'damaged' ? 'selected' : '' }}>Damaged
                                    </option>
                                </select>
                                <select name="inventory_department" id="inventory-department-filter"
                                    class="bg-white border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                    <option value="all"
                                        {{ request('inventory_department') == 'all' ? 'selected' : '' }}>All
                                        Departments</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ request('inventory_department') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}</option>
                                    @endforeach
                                </select>
                                <select name="inventory_user" id="inventory-user-filter"
                                    class="bg-white border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d]">
                                    @if ($activeStaff->isEmpty())
                                        <option value="all" selected>No staff available</option>
                                    @else
                                        <option value="all"
                                            {{ request('inventory_user') == 'all' ? 'selected' : '' }}>All Staff
                                        </option>
                                        @foreach ($activeStaff as $staff)
                                            <option value="{{ $staff->id }}"
                                                {{ request('inventory_user') == $staff->id ? 'selected' : '' }}>
                                                {{ $staff->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>

                                <input type="date" name="inventory_date_from" id="inventory-date-from"
                                    placeholder="Date Issued"
                                    class="border border-[#ffcc34] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-32"
                                    value="{{ request('inventory_date_from') }}">

                            </form>
                            <div class="w-full sm:w-auto flex justify-end">
                                <button type="button" id="inventory-export-btn"
                                    class="bg-[#00553d] hover:bg-[#007a5a] text-white text-sm font-medium px-4 py-2 rounded-lg border border-[#ffcc34] shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-2">
                                    <i class="fas fa-spinner fa-spin hidden" id="export-spinner"></i>
                                    <i class="fas fa-download"></i>
                                    <span>Export CSV</span>
                                </button>
                            </div>

                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto divide-y divide-[#ffcc34]"
                                aria-label="Inventory Logs">
                                <thead class="table-header">
                                    <tr>
                                        <th scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[200px]">
                                            Staff Name</th>
                                        <th scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[150px]">
                                            Department</th>
                                        <th scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[200px]">
                                            Equipment</th>
                                        <th scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[150px]">
                                            Model/Brand</th>
                                        <th scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[150px]">
                                            Serial Number</th>
                                        <th scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[150px]">
                                            PR Number</th>
                                        <th scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[150px]">
                                            Date Issued</th>
                                        <th scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[150px]">
                                            Status</th>
                                        <th scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium text-[#00553d] uppercase tracking-wider min-w-[150px]">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="inventoryTableBody" class="bg-white divide-y divide-[#ffcc34]">
                                    @if (!isset($inventory) || $inventory->isEmpty())
                                        <tr>
                                            <td colspan="9"
                                                class="px-4 py-4 text-center text-xs text-black bg-gradient-to-br from-gray-50 to-white rounded-lg border-2 border-dashed border-gray-300">
                                                <div
                                                    class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                                    <i class="fas fa-boxes text-2xl text-gray-400"></i>
                                                </div>
                                                <p class="text-xs text-gray-500 mb-2 font-medium">No inventory items
                                                    found</p>
                                                <p class="text-[0.6rem] text-gray-400">Items will appear here once
                                                    added</p>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($inventory as $item)
                                            <tr class="hover:bg-gray-50 transition-colors slide-up">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[200px] truncate max-w-xs"
                                                    title="{{ $item->staff_name ?? 'N/A' }}">
                                                    {{ $item->staff_name ?? 'N/A' }}</td>
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[150px]">
                                                    {{ $item->department->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[200px] truncate max-w-xs"
                                                    title="{{ $item->equipment_name }}">{{ $item->equipment_name }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[150px] truncate max-w-xs"
                                                    title="{{ $item->model_brand ?? 'N/A' }}">
                                                    {{ $item->model_brand ?? 'N/A' }}</td>
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[150px]">
                                                    {{ $item->serial_number }}</td>
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[150px]">
                                                    {{ $item->pr_number }}</td>
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-xs text-black min-w-[150px]">
                                                    @if ($item->date_issued instanceof \Carbon\Carbon)
                                                        {{ $item->date_issued->format('Y-m-d H:i:s') }}
                                                    @elseif (is_string($item->date_issued) &&
                                                            !empty($item->date_issued) &&
                                                            \Carbon\Carbon::canBeCreatedFromFormat($item->date_issued, 'Y-m-d H:i:s'))
                                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i', $item->date_issued)->format('Y-m-d H:i:s') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap min-w-[150px]">
                                                    <span class="status-indicator status-{{ $item->status }}"></span>
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $item->status == 'available' ? 'bg-green-100 text-green-800' : ($item->status == 'in_use' ? 'bg-blue-100 text-blue-800' : ($item->status == 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap flex space-x-2">
                                                    <a href="{{ route('inventory.show', $item->id) }}"
                                                        class="text-[#00553d] hover:text-[#007a52] px-2 py-1 rounded-md hover:bg-blue-50 transition-all duration-200 text-[0.6rem] font-semibold border border-blue-200 hover:border-blue-300"
                                                        title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button data-id="{{ $item->id }}"
                                                        class="edit-inventory-btn text-[#00553d] hover:text-[#007a52] px-2 py-1 rounded-md hover:bg-blue-50 transition-all duration-200 text-[0.6rem] font-semibold border border-blue-200 hover:border-blue-300"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('inventory.destroy', $item->id) }}"
                                                        method="POST" class="delete-inventory-form inline-block"
                                                        data-name="{{ $item->equipment_name }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="delete-inventory-btn text-[#90143c] hover:text-[#b01a47] px-2 py-1 rounded-md hover:bg-red-50 transition-all duration-200 text-[0.6rem] font-semibold border border-red-200 hover:border-red-300"
                                                            title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div id="inventoryPagination" class="pagination-container mt-4">
                            {{--  <select id="inventory-per-page-display" 
                                class="bg-white border border-[#ffcc34] rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#00553d] mr-2">
                                <option value="20" {{ $inventoryPerPage == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ $inventoryPerPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $inventoryPerPage == 100 ? 'selected' : '' }}>100</option>
                            </select>  --}}
                            <span class="pagination-info">
                                Page {{ isset($inventory) ? $inventory->currentPage() : 1 }} to
                                {{ isset($inventory) ? $inventory->currentPage() : 1 }} of
                                {{ isset($inventory) ? $inventory->total() : 0 }} results
                            </span>
                            @if (!isset($inventory) || $inventory->onFirstPage())
                                <span class="pagination-btn opacity-50 cursor-not-allowed">Previous</span>
                            @else
                                <a href="{{ $inventory->previousPageUrl() }}" class="pagination-btn">Previous</a>
                            @endif
                            @if (isset($inventory))
                                @foreach ($inventory->getUrlRange(1, $inventory->lastPage()) as $page => $url)
                                    <a href="{{ $url }}"
                                        class="pagination-btn {{ $inventory->currentPage() == $page ? 'current' : '' }}">{{ $page }}</a>
                                @endforeach
                            @endif
                            @if (!isset($inventory) || !$inventory->hasMorePages())
                                <span class="pagination-btn opacity-50 cursor-not-allowed">Next</span>
                            @else
                                <a href="{{ $inventory->nextPageUrl() }}" class="pagination-btn">Next</a>
                            @endif
                            <input type="number" id="inventoryPageJump" class="page-jump-input" placeholder="Page"
                                min="1" max="{{ isset($inventory) ? $inventory->lastPage() : 1 }}"
                                value="{{ isset($inventory) ? $inventory->currentPage() : 1 }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipment Issuance Statistics -->
            <div
                class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up my-6">
                <div class="p-4 bg-gradient-to-r from-[#90143c] to-[#b01a47]">
                    <h2 class="text-xs font-semibold text-white">Equipment Issuance Statistics</h2>
                </div>
                <div class="p-4">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
                        <h3 class="text-xs font-semibold text-[#00553d] flex items-center">
                            <div class="p-1.5 bg-gradient-to-br from-[#00553d] to-[#007a52] rounded-md mr-2">
                                <i class="fas fa-filter text-white text-xs"></i>
                            </div>
                            Filter Chart Data
                        </h3>
                        <select id="chart-time-filter"
                            class="px-3 py-2 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-36">
                            <option value="month" {{ request('chart_time', 'month') == 'month' ? 'selected' : '' }}>
                                This Month</option>
                            <option value="week" {{ request('chart_time') == 'week' ? 'selected' : '' }}>This Week
                            </option>
                            <option value="year" {{ request('chart_time') == 'year' ? 'selected' : '' }}>This Year
                            </option>
                            <option value="past_year" {{ request('chart_time') == 'past_year' ? 'selected' : '' }}>
                                Past Year</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="equipmentChart" class="w-full"
                            data-equipment="{{ json_encode($equipmentData) }}"></canvas>
                    </div>
                </div>
            </div>

            <!-- Edit Inventory Modal -->
            <div id="edit-inventory-modal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white rounded-xl p-6 w-full max-w-md border border-[#ffcc34] shadow-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-semibold text-[#00553d] flex items-center">
                            <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md mr-2">
                                <i class="fas fa-edit text-white text-xs"></i>
                            </div>
                            Edit Inventory Item
                        </h3>
                        <button onclick="document.getElementById('edit-inventory-modal').classList.add('hidden')"
                            class="text-[#90143c] hover:text-[#b01a47] transition-all duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form id="edit-inventory-form" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="equipment_id" id="edit_equipment_id">
                        <div class="mb-4 space-y-3">
                            <div class="relative group">
                                <label for="edit_staff_name"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Staff Name</label>
                                <input type="text" name="staff_name" id="edit_staff_name" readonly
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs bg-gray-100 text-gray-500 cursor-not-allowed">
                            </div>
                            <div class="relative group">
                                <label for="edit_department_id"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Department</label>
                                <input type="text" name="department_name" id="edit_department_name" readonly
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs bg-gray-100 text-gray-500 cursor-not-allowed">
                                <input type="hidden" name="department_id" id="edit_department_id">
                            </div>
                            <div class="relative group">
                                <label for="edit_equipment_name"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Equipment Name
                                    *</label>
                                <input type="text" name="equipment_name" id="edit_equipment_name" required
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] group-hover:shadow-md transition-all duration-300">
                                <div
                                    class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                    <i class="fas fa-box text-xs"></i>
                                </div>
                            </div>
                            <div class="relative group">
                                <label for="edit_model_brand"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Model/Brand *</label>
                                <input type="text" name="model_brand" id="edit_model_brand" required
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] group-hover:shadow-md transition-all duration-300">
                                <div
                                    class="absolute right-3 top-7 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                    <i class="fas fa-tag text-xs"></i>
                                </div>
                            </div>
                            <div class="relative group">
                                <label for="edit_serial_number"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Serial Number
                                    *</label>
                                <input type="text" name="serial_number" id="edit_serial_number" readonly
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs bg-gray-100 text-gray-500 cursor-not-allowed">
                            </div>
                            <div class="relative group">
                                <label for="edit_pr_number"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">PR Number *</label>
                                <input type="text" name="pr_number" id="edit_pr_number" readonly
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs bg-gray-100 text-gray-500 cursor-not-allowed">
                            </div>
                            <div class="relative group">
                                <label for="edit_date_issued"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Date Issued *</label>
                                <input type="date" name="date_issued" id="edit_date_issued" required
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] group-hover:shadow-md transition-all duration-300">
                            </div>
                            <div>
                                <label for="edit_status"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Status *</label>
                                <select name="status" id="edit_status"
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg focus:ring-2 focus:ring-[#00553d] text-xs">
                                    <option value="in_use">In Use</option>
                                    <option value="available">Available</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="damaged">Damaged</option>
                                </select>
                            </div>
                            <div class="relative group">
                                <label for="edit_remarks"
                                    class="block text-[0.65rem] font-medium text-[#00553d] mb-1">Remarks</label>
                                <textarea name="remarks" id="edit_remarks"
                                    class="w-full px-3 py-2 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] group-hover:shadow-md transition-all duration-300"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button"
                                class="px-4 py-2 text-xs font-semibold text-[#00553d] bg-gray-100 rounded-lg hover:bg-gray-200 border border-gray-200 transition-all duration-200"
                                onclick="document.getElementById('edit-inventory-modal').classList.add('hidden')">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 text-xs font-semibold text-white bg-gradient-to-br from-[#00553d] to-[#007a58] rounded-lg hover:from-[#004934] hover:to-[#006248] transition-all duration-200 submit-button">
                                <i class="spinner fas fa-spinner fa-spin mr-2 hidden"></i>
                                <span class="btn-text"><i class="fas fa-save mr-2"></i>Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

            <script>
                window.checkDuplicatesUrl = "{{ route('inventory.check-duplicates') }}";
                let chartInstance = null;

                document.addEventListener('DOMContentLoaded', function() {
                    // Utility Functions
                    function setLoadingState(button, isLoading) {
                        if (isLoading) {
                            button.classList.add('btn-loading');
                            button.disabled = true;
                        } else {
                            button.classList.remove('btn-loading');
                            button.disabled = false;
                        }
                    }

                    function showAlert(message, type) {
                        const alertContainer = document.getElementById('alert-container');
                        const alertDiv = document.createElement('div');
                        alertDiv.className =
                            `p-3 rounded-lg text-xs flex items-center space-x-2 ${type === 'error' ? 'bg-red-100 text-red-700 border-red-200' : type === 'success' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-blue-100 text-blue-700 border-blue-200'} border fade-in`;
                        alertDiv.innerHTML = `
                <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : type === 'success' ? 'fa-check-circle' : 'fa-info-circle'} text-base"></i>
                <span>${message}</span>
            `;
                        alertContainer.appendChild(alertDiv);
                        setTimeout(() => {
                            alertDiv.classList.remove('fade-in');
                            alertDiv.classList.add('fade-out');
                            setTimeout(() => alertDiv.remove(), 300);
                        }, 3000);
                    }

                    // Initialize Edit Functionality
                    function initializeEditFunctionality() {
                        const editButtons = document.querySelectorAll('.edit-inventory-btn');
                        const editModal = document.getElementById('edit-inventory-modal');
                        const editForm = document.getElementById('edit-inventory-form');

                        editButtons.forEach(button => {
                            button.addEventListener('click', async function() {
                                const itemId = this.dataset.id;
                                if (!itemId) {
                                    showAlert('Equipment ID is missing.', 'error');
                                    return;
                                }

                                const submitButton = editForm.querySelector('.submit-button');
                                if (submitButton) {
                                    submitButton.disabled = true;
                                    const spinner = submitButton.querySelector('.spinner');
                                    const btnText = submitButton.querySelector('.btn-text');
                                    if (spinner) spinner.classList.remove('hidden');
                                    if (btnText) btnText.classList.add('hidden');
                                }

                                try {
                                    const response = await fetch(`/inventory/${itemId}`, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]').content,
                                            'Accept': 'application/json'
                                        }
                                    });

                                    if (!response.ok) {
                                        const errorData = await response.json().catch(() => ({}));
                                        throw new Error(errorData.error ||
                                            `Failed to fetch equipment data (Status: ${response.status})`
                                            );
                                    }

                                    const {
                                        data
                                    } = await response.json();

                                    if (!data) {
                                        throw new Error('No equipment data returned from server');
                                    }

                                    // Populate form fields
                                    document.getElementById('edit_equipment_id').value = data.id || '';
                                    document.getElementById('edit_equipment_name').value = data
                                        .equipment_name || '';
                                    document.getElementById('edit_model_brand').value = data
                                        .model_brand || '';
                                    document.getElementById('edit_serial_number').value = data
                                        .serial_number || '';
                                    document.getElementById('edit_pr_number').value = data.pr_number ||
                                        '';
                                    document.getElementById('edit_date_issued').value = data
                                        .date_issued ? data.date_issued.split(' ')[0] : '';
                                    document.getElementById('edit_status').value = data.status ||
                                        'available';
                                    document.getElementById('edit_staff_name').value = data
                                        .staff_name || '';
                                    document.getElementById('edit_department_id').value = data
                                        .department_id || '';
                                    document.getElementById('edit_department_name').value = data
                                        .department_name || '';
                                    document.getElementById('edit_remarks').value = data.remarks || '';

                                    // Show modal
                                    editModal.classList.remove('hidden');
                                } catch (error) {
                                    console.error('Error fetching equipment:', error);
                                    showAlert(`Failed to load equipment data: ${error.message}`,
                                        'error');
                                } finally {
                                    if (submitButton) {
                                        submitButton.disabled = false;
                                        const spinner = submitButton.querySelector('.spinner');
                                        const btnText = submitButton.querySelector('.btn-text');
                                        if (spinner) spinner.classList.add('hidden');
                                        if (btnText) btnText.classList.remove('hidden');
                                    }
                                }
                            });
                        });
                    }

                    // Form submission handler
                    document.getElementById('edit-inventory-form').addEventListener('submit', async function(e) {
                        e.preventDefault();

                        const id = document.getElementById('edit_equipment_id').value;
                        if (!id) {
                            showAlert('Equipment ID is missing!', 'error');
                            return;
                        }

                        const updateButton = this.querySelector('.submit-button');
                        if (updateButton) {
                            updateButton.disabled = true;
                            const spinner = updateButton.querySelector('.spinner');
                            const btnText = updateButton.querySelector('.btn-text');
                            if (spinner) spinner.classList.remove('hidden');
                            if (btnText) btnText.classList.add('hidden');
                        }

                        const formData = new FormData(this);

                        // Explicitly add values from disabled or non-standard inputs
                        formData.append('staff_name', document.getElementById('edit_staff_name').value);
                        formData.append('department_id', document.getElementById('edit_department_id').value);
                        formData.append('equipment_name', document.getElementById('edit_equipment_name').value);
                        formData.append('model_brand', document.getElementById('edit_model_brand').value);
                        formData.append('serial_number', document.getElementById('edit_serial_number').value);
                        formData.append('date_issued', document.getElementById('edit_date_issued').value);
                        formData.append('pr_number', document.getElementById('edit_pr_number').value);
                        formData.append('status', document.getElementById('edit_status').value);
                        formData.append('remarks', document.getElementById('edit_remarks').value);

                        try {
                            formData.append('_method', 'PUT'); // Laravel spoofing

                            const response = await fetch(`/inventory/${id}/update`, {
                                method: 'POST', // important
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content,
                                    'Accept': 'application/json',
                                },
                                body: formData
                            });



                            const result = await response.json();

                            if (response.ok) {
                                showAlert('Equipment updated successfully!', 'success');
                                document.getElementById('edit-inventory-modal').classList.add('hidden');
                                setTimeout(() => window.location.reload(), 1000);
                            } else {
                                showAlert(result.error || 'Failed to update equipment: ' + JSON.stringify(result
                                    .messages || {}), 'error');
                                console.log("Server response:", result);
                            }
                        } catch (err) {
                            console.error('Error:', err);
                            showAlert('An error occurred while updating: ' + err.message, 'error');
                        } finally {
                            if (updateButton) {
                                updateButton.disabled = false;
                                const spinner = updateButton.querySelector('.spinner');
                                const btnText = updateButton.querySelector('.btn-text');
                                if (spinner) spinner.classList.add('hidden');
                                if (btnText) btnText.classList.remove('hidden');
                            }
                        }
                    });


                    // Initialize Chart
                    function initializeChart() {
                        const ctx = document.getElementById('equipmentChart');
                        if (!ctx) {
                            console.error('Canvas element with ID "equipmentChart" not found');
                            return;
                        }

                        try {
                            const equipmentDataAttr = ctx.dataset.equipment;
                            if (!equipmentDataAttr) {
                                console.log('No equipment data found in canvas dataset');
                                ctx.style.display = 'none';
                                return;
                            }

                            const equipmentData = JSON.parse(equipmentDataAttr);
                            const labels = Object.keys(equipmentData);
                            const data = Object.values(equipmentData);

                            if (labels.length === 0) {
                                console.log('Equipment data is empty');
                                ctx.style.display = 'none';
                                return;
                            }

                            if (chartInstance) {
                                chartInstance.destroy();
                            }

                            chartInstance = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Issuance Count',
                                        data: data,
                                        backgroundColor: '#ffcc34',
                                        borderColor: '#00553d',
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
                                                text: 'Number of Issuances',
                                                color: '#00553d',
                                                font: {
                                                    size: 10,
                                                    family: 'Inter'
                                                }
                                            },
                                            ticks: {
                                                color: '#00553d',
                                                font: {
                                                    size: 10,
                                                    family: 'Inter'
                                                },
                                                stepSize: 1
                                            },
                                            grid: {
                                                color: 'rgba(0, 85, 61, 0.1)'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Equipment',
                                                color: '#00553d',
                                                font: {
                                                    size: 10,
                                                    family: 'Inter'
                                                }
                                            },
                                            ticks: {
                                                color: '#00553d',
                                                font: {
                                                    size: 10,
                                                    family: 'Inter'
                                                },
                                                autoSkip: true,
                                                maxRotation: 45,
                                                minRotation: 0
                                            },
                                            grid: {
                                                display: false
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            labels: {
                                                color: '#00553d',
                                                font: {
                                                    size: 10,
                                                    family: 'Inter'
                                                }
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: '#90143c',
                                            titleColor: '#ffffff',
                                            bodyColor: '#ffffff',
                                            titleFont: {
                                                size: 10,
                                                family: 'Inter'
                                            },
                                            bodyFont: {
                                                size: 10,
                                                family: 'Inter'
                                            }
                                        }
                                    }
                                }
                            });
                        } catch (error) {
                            console.error('Error initializing chart:', error);
                            ctx.style.display = 'none';
                        }
                    }

                    // Chart Filter Logic
                    const chartFilter = document.getElementById('chart-time-filter');
                    if (chartFilter) {
                        chartFilter.addEventListener('change', function() {
                            const timeRange = this.value;
                            $.ajax({
                                url: "{{ route('inventory.chart-data') }}",
                                method: 'GET',
                                data: {
                                    time_range: timeRange
                                },
                                success: function(response) {
                                    if (response.equipmentData) {
                                        const ctx = document.getElementById('equipmentChart');
                                        ctx.dataset.equipment = JSON.stringify(response.equipmentData);
                                        initializeChart();
                                    } else {
                                        showAlert('No data available for the selected time range.',
                                            'info');
                                        const ctx = document.getElementById('equipmentChart');
                                        ctx.style.display = 'none';
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Error fetching chart data:', xhr);
                                    showAlert('Failed to load chart data. Please try again.', 'error');
                                    const ctx = document.getElementById('equipmentChart');
                                    ctx.style.display = 'none';
                                }
                            });
                        });
                    }

                    // Initialize Accordions
                    function initializeAccordions() {
                        const accordionToggles = document.querySelectorAll('.accordion-toggle');
                        accordionToggles.forEach(toggle => {
                            toggle.addEventListener('click', function() {
                                const target = document.getElementById(toggle.dataset.target);
                                const icon = toggle.querySelector('.accordion-icon');
                                if (!target || !icon) return;
                                const isOpen = target.classList.contains('open');
                                target.classList.toggle('open');
                                icon.classList.toggle('rotate-180');
                                target.style.maxHeight = isOpen ? '0' : (target.scrollHeight + 30 || 2000) +
                                    'px';
                            });

                            const inventoryLogContent = document.getElementById('inventory-log');
                            const inventoryLogIcon = document.querySelector(
                                '[data-target="inventory-log"] .accordion-icon');
                            if (inventoryLogContent && inventoryLogIcon) {
                                inventoryLogContent.classList.add('open');
                                inventoryLogIcon.classList.add('rotate-180');
                                inventoryLogContent.style.maxHeight = (inventoryLogContent.scrollHeight + 30 ||
                                    2000) + 'px';
                                window.addEventListener('resize', () => {
                                    if (inventoryLogContent.classList.contains('open')) {
                                        inventoryLogContent.style.maxHeight = (inventoryLogContent
                                            .scrollHeight + 30 || 2000) + 'px';
                                    }
                                });
                            }
                        });
                    }

                    // Initialize Equipment Toggles
                    function initializeEquipmentToggles() {
                        const equipmentToggles = document.querySelectorAll('.equipment-toggle');
                        equipmentToggles.forEach(toggle => {
                            toggle.addEventListener('click', function() {
                                const content = document.getElementById(toggle.dataset.target);
                                content.classList.toggle('open');
                                const svg = toggle.querySelector('svg');
                                svg.classList.toggle('rotate-180');
                                content.style.maxHeight = content.classList.contains('open') ? (content
                                    .scrollHeight + 30) + 'px' : '0';
                            });
                        });
                    }

                    // Initialize Equipment Details Modal
                    function initializeEquipmentDetailsModal() {
                        const modal = document.getElementById('equipment-details-modal');
                        const closeBtn = document.getElementById('close-details-modal');
                        const content = document.getElementById('equipment-details-content');

                        if (!modal || !closeBtn || !content) return;

                        function closeModal() {
                            modal.classList.remove('show');
                        }

                        function showLoadingState() {
                            content.innerHTML = `
                    <div class="loading">
                        <div class="spinner"></div>
                        Loading equipment details...
                    </div>
                `;
                        }

                        function showErrorState(message) {
                            content.innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                        ${message}
                    </div>
                `;
                        }

                        closeBtn.addEventListener('click', closeModal);
                        modal.addEventListener('click', function(e) {
                            if (e.target === modal) closeModal();
                        });
                        document.addEventListener('keydown', function(e) {
                            if (e.key === 'Escape' && modal.classList.contains('show')) closeModal();
                        });

                        document.querySelectorAll('a[href*="/inventory/"]').forEach(link => {
                            link.addEventListener('click', function(e) {
                                e.preventDefault();
                                const url = this.getAttribute('href');
                                showLoadingState();
                                modal.classList.add('show');
                                fetch(url)
                                    .then(response => {
                                        if (!response.ok) throw new Error(
                                            `HTTP error! status: ${response.status}`);
                                        return response.text();
                                    })
                                    .then(html => {
                                        content.innerHTML = html;
                                    })
                                    .catch(error => {
                                        console.error('Error loading equipment details:', error);
                                        showErrorState(
                                            'Failed to load equipment details. Please try again.');
                                        showAlert('Failed to load equipment details', 'error');
                                    });
                            });
                        });
                    }

                    // Initialize Modals
                    function initializeModals() {
                        const issueBtn = document.getElementById('issue-equipment-btn');
                        const returnBtn = document.getElementById('return-equipment-btn');
                        const issueModal = document.getElementById('issue-equipment-modal');
                        const returnModal = document.getElementById('return-equipment-modal');
                        const closeIssueModal = document.getElementById('close-issue-modal');
                        const closeReturnModal = document.getElementById('close-return-modal');
                        const cancelIssueModal = document.getElementById('cancel-issue-modal');

                        if (issueBtn && issueModal) {
                            issueBtn.addEventListener('click', () => issueModal.classList.add('show'));
                        }
                        if (returnBtn && returnModal) {
                            returnBtn.addEventListener('click', () => returnModal.classList.add('show'));
                        }
                        if (closeIssueModal && issueModal) {
                            closeIssueModal.addEventListener('click', () => issueModal.classList.remove('show'));
                        }
                        if (closeReturnModal && returnModal) {
                            closeReturnModal.addEventListener('click', () => returnModal.classList.remove('show'));
                        }
                        if (cancelIssueModal && issueModal) {
                            cancelIssueModal.addEventListener('click', () => issueModal.classList.remove('show'));
                        }

                        if (issueModal) {
                            issueModal.addEventListener('click', (e) => {
                                if (e.target === issueModal) issueModal.classList.remove('show');
                            });
                        }
                        if (returnModal) {
                            returnModal.addEventListener('click', (e) => {
                                if (e.target === returnModal) returnModal.classList.remove('show');
                            });
                        }
                    }

                    // Initialize Search and Filters
                    function initializeSearchAndFilters() {
                        const inventoryForm = document.getElementById('inventory-filter-form');
                        if (inventoryForm) {
                            const inputs = inventoryForm.querySelectorAll('input, select');
                            inputs.forEach(input => {
                                const eventType = input.type === 'text' || input.type === 'date' ? 'input' :
                                    'change';
                                input.addEventListener(eventType, _.debounce(() => {
                                    const queryParams = new URLSearchParams(new FormData(
                                    inventoryForm));
                                    queryParams.set('inventory_page', '1');
                                    window.location.href =
                                        `{{ route('inventory') }}?${queryParams.toString()}`;
                                }, 300));
                            });
                        }

                        const returnSearch = document.getElementById('returnSearch');
                        const returnDepartmentFilter = document.getElementById('returnDepartmentFilter');
                        const returnStatusFilter = document.getElementById('returnStatusFilter');

                        function filterReturnEquipment() {
                            if (!returnSearch || !returnDepartmentFilter || !returnStatusFilter) return;
                            const search = returnSearch.value.toLowerCase();
                            const department = returnDepartmentFilter.value;
                            const equipmentType = returnStatusFilter.value;
                            const accordions = document.querySelectorAll('#returnEquipmentContainer .equipment-accordion');

                            $.each(accordions, function() {
                                const equipment = this.querySelector('h3').textContent.toLowerCase();
                                const dept = this.querySelector('p').textContent.split(' • ').pop();
                                const show = (
                                    (search === '' || equipment.includes(search)) &&
                                    (department === '' || dept === department) &&
                                    (equipmentType === '' || equipment.includes(equipmentType.toLowerCase()))
                                );
                                this.style.display = show ? '' : 'none';
                            });
                        }

                        if (returnSearch) returnSearch.addEventListener('input', filterReturnEquipment);
                        if (returnDepartmentFilter) returnDepartmentFilter.addEventListener('change',
                        filterReturnEquipment);
                        if (returnStatusFilter) returnStatusFilter.addEventListener('change', filterReturnEquipment);
                    }

                    // Initialize Pagination
                    function initializePagination() {
                        const inventoryPageJump = document.getElementById('inventoryPageJump');
                        const returnPageJump = document.getElementById('returnPageJump');
                        const perPageSelect = document.getElementById('inventory-per-page');
                        const perPageDisplay = document.getElementById('inventory-per-page-display');

                        if (inventoryPageJump) {
                            inventoryPageJump.addEventListener('change', function() {
                                const page = parseInt(this.value);
                                const maxPage = parseInt(this.max);
                                if (isNaN(page) || page < 1 || page > maxPage) {
                                    showAlert(`Please enter a page number between 1 and ${maxPage}.`, 'error');
                                    this.value = this.defaultValue;
                                    return;
                                }
                                const form = document.getElementById('inventory-filter-form');
                                const queryParams = new URLSearchParams(new FormData(form));
                                queryParams.set('inventory_page', page);
                                window.location.href = `{{ route('inventory') }}?${queryParams.toString()}`;
                            });
                        }

                        if (returnPageJump) {
                            returnPageJump.addEventListener('change', function() {
                                const page = parseInt(this.value);
                                const maxPage = parseInt(this.max);
                                if (isNaN(page) || page < 1 || page > maxPage) {
                                    showAlert(`Please enter a page number between 1 and ${maxPage}.`, 'error');
                                    this.value = this.defaultValue;
                                    return;
                                }
                                const queryParams = new URLSearchParams(window.location.search);
                                queryParams.set('issuances_page', page);
                                window.location.href = `{{ route('inventory') }}?${queryParams.toString()}`;
                            });
                        }

                        if (perPageSelect && perPageDisplay) {
                            const syncPerPage = () => {
                                perPageDisplay.value = perPageSelect.value;
                                const form = document.getElementById('inventory-filter-form');
                                const queryParams = new URLSearchParams(new FormData(form));
                                queryParams.set('inventory_page', '1');
                                queryParams.set('inventory_per_page', perPageSelect.value);
                                window.location.href = `{{ route('inventory') }}?${queryParams.toString()}`;
                            };
                            perPageSelect.addEventListener('change', syncPerPage);
                            perPageDisplay.addEventListener('change', () => {
                                perPageSelect.value = perPageDisplay.value;
                                syncPerPage();
                            });
                        }
                    }

                    // Initialize Delete Functionality
                    function initializeDeleteFunctionality() {
                        const deleteButtons = document.querySelectorAll('.delete-inventory-btn');
                        deleteButtons.forEach(btn => {
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();
                                const form = btn.closest('form');
                                const equipmentName = btn.closest('.delete-inventory-form').dataset.name;
                                Swal.fire({
                                    title: 'Delete Inventory Item?',
                                    html: `
                            <div class="text-left space-y-3">
                                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <i class="fas fa-exclamation-triangle text-red-500 text-base"></i>
                                        <span class="font-semibold text-red-800 text-xs">Permanent Deletion Warning</span>
                                    </div>
                                    <div class="text-xs text-red-700">
                                        You are about to permanently delete "<strong>${equipmentName}</strong>".
                                    </div>
                                </div>
                                <div class="bg-amber-50 p-2 rounded-lg border border-amber-200">
                                    <div class="text-xs text-amber-700">
                                        <strong>Impact:</strong>
                                        <ul class="mt-1 space-y-1 text-[0.6rem]">
                                            <li>• This action cannot be undone</li>
                                            <li>• Related records will be updated</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="text-center text-xs text-gray-600">
                                    Type "<strong>DELETE</strong>" to confirm:
                                </div>
                            </div>
                        `,
                                    input: 'text',
                                    inputPlaceholder: 'Type DELETE to confirm',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#dc2626',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, Delete',
                                    cancelButtonText: '<i class="fas fa-shield-alt mr-2"></i>Keep Safe',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    inputValidator: function(value) {
                                        if (value !== 'DELETE') {
                                            return 'Please type "DELETE" exactly to confirm';
                                        }
                                    },
                                    customClass: {
                                        title: 'text-xs',
                                        content: 'text-[0.6rem]'
                                    }
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        form.submit();
                                    }
                                });
                            });
                        });
                    }

                    // Initialize Form Submission
                    function initializeFormSubmission() {
                        const issueForm = document.getElementById('issueForm');
                        if (issueForm) {
                            issueForm.addEventListener('submit', async function(e) {
                                e.preventDefault();
                                const formData = new FormData(this);
                                const payload = Object.fromEntries(formData.entries());
                                const submitButton = this.querySelector('button[type="submit"]');

                                if (!payload.serial_number || !payload.pr_number) {
                                    showAlert('Serial number and PR number are required.', 'error');
                                    return;
                                }

                                setLoadingState(submitButton, true);

                                try {
                                    const checkResponse = await fetch(window.checkDuplicatesUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]').content
                                        },
                                        body: JSON.stringify({
                                            serial_number: payload.serial_number,
                                            pr_number: payload.pr_number
                                        })
                                    });

                                    if (!checkResponse.ok) throw new Error(
                                        `HTTP error! Status: ${checkResponse.status}`);

                                    const checkData = await checkResponse.json();
                                    if (checkData.serial_exists || checkData.pr_exists) {
                                        let message = 'Potential duplicates found:<br>';
                                        if (checkData.serial_exists) message +=
                                            `• Serial Number "${payload.serial_number}" exists<br>`;
                                        if (checkData.pr_exists) message +=
                                            `• PR Number "${payload.pr_number}" exists<br>`;

                                        const result = await Swal.fire({
                                            title: 'Duplicate Detected',
                                            html: `
                                    <div class="text-left space-y-3">
                                        <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                                            <div class="text-xs text-yellow-700">${message}</div>
                                        </div>
                                    </div>
                                `,
                                            icon: 'warning',
                                            showCancelButton: !checkData.serial_exists,
                                            confirmButtonColor: '#00553d',
                                            cancelButtonColor: '#90143c',
                                            confirmButtonText: checkData.serial_exists ? 'OK' :
                                                '<i class="fas fa-check mr-2"></i>Proceed Anyway',
                                            cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                                            customClass: {
                                                title: 'text-xs',
                                                content: 'text-[0.6rem]'
                                            }
                                        });

                                        if (result.isConfirmed && !checkData.serial_exists) {
                                            issueForm.submit();
                                        } else {
                                            setLoadingState(submitButton, false);
                                        }
                                    } else {
                                        const result = await Swal.fire({
                                            title: 'Issue Equipment?',
                                            html: `
                                    <div class="text-left space-y-3">
                                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                            <div class="font-semibold text-blue-800 mb-2">Issuance Details:</div>
                                            <div class="text-xs text-blue-700">
                                                <div class="flex justify-between items-center">
                                                    <span>Staff Name:</span>
                                                    <span class="font-medium">${payload.staff_name}</span>
                                                </div>
                                                <div class="flex justify-between items-center mt-1">
                                                    <span>Equipment:</span>
                                                    <span class="font-medium">${payload.equipment_name}</span>
                                                </div>
                                                <div class="flex justify-between items-center mt-1">
                                                    <span>Serial Number:</span>
                                                    <span class="font-medium">${payload.serial_number}</span>
                                                </div>
                                                <div class="flex justify-between items-center mt-1">
                                                    <span>PR Number:</span>
                                                    <span class="font-medium">${payload.pr_number}</span>
                                                </div>
                                                <div class="flex justify-between items-center mt-1">
                                                    <span>Status:</span>
                                                    <span class="font-medium">${payload.status.charAt(0).toUpperCase() + payload.status.slice(1)}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `,
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonColor: '#00553d',
                                            cancelButtonColor: '#90143c',
                                            confirmButtonText: '<i class="fas fa-plus mr-2"></i>Issue Equipment',
                                            cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                                            customClass: {
                                                title: 'text-xs',
                                                content: 'text-[0.6rem]'
                                            }
                                        });

                                        if (result.isConfirmed) {
                                            issueForm.submit();
                                        } else {
                                            setLoadingState(submitButton, false);
                                        }
                                    }
                                } catch (error) {
                                    setLoadingState(submitButton, false);
                                    showAlert('Failed to validate data. Please try again.', 'error');
                                }
                            });
                        }

                        const returnForms = document.querySelectorAll('#returnEquipmentContainer form');
                        returnForms.forEach(form => {
                            form.addEventListener('submit', function(e) {
                                e.preventDefault();
                                const formData = new FormData(this);
                                const payload = Object.fromEntries(formData.entries());
                                const submitButton = this.querySelector('button[type="submit"]');
                                const equipmentName = this.closest('.equipment-accordion').querySelector(
                                    'h3').textContent;
                                const staffName = this.closest('.equipment-accordion').querySelector('p')
                                    .textContent.split(' • ')[0];

                                Swal.fire({
                                    title: 'Return Equipment?',
                                    html: `
                            <div class="text-left space-y-3">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                    <div class="font-semibold text-blue-800 mb-2">Return Details:</div>
                                    <div class="text-xs text-blue-700">
                                        <div class="flex justify-between items-center">
                                            <span>Equipment:</span>
                                            <span class="font-medium">${equipmentName}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>Staff Name:</span>
                                            <span class="font-medium">${staffName}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>Date Returned:</span>
                                            <span class="font-medium">${payload.date_returned}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>Condition:</span>
                                            <span class="font-medium">${payload.returned_condition.charAt(0).toUpperCase() + payload.returned_condition.slice(1)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `,
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#00553d',
                                    cancelButtonColor: '#90143c',
                                    confirmButtonText: '<i class="fas fa-undo mr-2"></i>Confirm Return',
                                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                                    customClass: {
                                        title: 'text-xs',
                                        content: 'text-[0.6rem]'
                                    }
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        setLoadingState(submitButton, true);
                                        form.submit();
                                    }
                                });
                            });
                        });
                    }

                    // Initialize Export Functionality
                    function initializeExportFunctionality() {
                        const exportButton = document.getElementById('inventory-export-btn');
                        if (exportButton) {
                            exportButton.addEventListener('click', function() {
                                const form = document.getElementById('inventory-filter-form');
                                const queryParams = new URLSearchParams(new FormData(form));
                                queryParams.set('export', 'csv');
                                setLoadingState(exportButton, true);

                                fetch(`{{ route('inventory') }}?${queryParams.toString()}`, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                                .content
                                        }
                                    })
                                    .then(response => {
                                        if (!response.ok) throw new Error(
                                            `HTTP error! Status: ${response.status}`);
                                        return response.blob();
                                    })
                                    .then(blob => {
                                        const url = window.URL.createObjectURL(blob);
                                        const a = document.createElement('a');
                                        a.href = url;
                                        a.download =
                                            `inventory_export_${new Date().toISOString().split('T')[0]}.csv`;
                                        document.body.appendChild(a);
                                        a.click();
                                        a.remove();
                                        window.URL.revokeObjectURL(url);
                                        showAlert('Inventory exported successfully.', 'success');
                                    })
                                    .catch(error => {
                                        console.error('Error exporting inventory:', error);
                                        showAlert('Failed to export inventory. Please try again.', 'error');
                                    })
                                    .finally(() => {
                                        setLoadingState(exportButton, false);
                                    });
                            });
                        }
                    }

                    // Initialize All
                    initializeChart();
                    initializeAccordions();
                    initializeEquipmentToggles();
                    initializeEquipmentDetailsModal();
                    initializeModals();
                    initializeSearchAndFilters();
                    initializePagination();
                    initializeDeleteFunctionality();
                    initializeFormSubmission();
                    initializeEditFunctionality();
                    initializeExportFunctionality();

                    // Handle Laravel Session Messages
                    @if (session('success'))
                        showAlert('{{ session('success') }}', 'success');
                    @endif
                    @if (session('error'))
                        showAlert('{{ session('error') }}', 'error');
                    @endif
                });
            </script>

            <x-auth-footer />
</x-app-layout>

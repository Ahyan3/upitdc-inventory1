<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-[#00553d] leading-tight">
            {{ __('Staff Management') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- CSRF Token -->
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <style>
                .accordion-content {
                    transition: max-height 0.3s ease-in-out, padding 0.3s ease-in-out;
                    max-height: 0;
                    overflow: hidden;
                    padding: 0 1rem;
                }

                .accordion-content.open {
                    max-height: 1000px;
                    padding: 1rem;
                }

                .staff-card {
                    transition: all 0.2s ease-in-out;
                }

                .staff-card:hover {
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

                .status-resigned {
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
                    justify-content: space-between;
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

                .view-btn {
                    position: relative;
                    z-index: 10;
                }

                th {
                    max-width: 200px;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }

                /* Prevent background scrolling when modal is open */
                body.modal-open {
                    overflow: hidden;
                }
            </style>

            <div class="flex min-h-screen bg-gray-50">
                <button id="toggleSidebar"
                    class="md:hidden fixed top-3 left-3 z-50 bg-[#90143c] text-white p-1.5 rounded-md border border-[#ffcc34] shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-bars text-xs"></i>
                </button>

                <div class="flex-1 container mx-auto px-3 py-6 max-w-7xl">
                    <div class="text-center mb-8 fade-in">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-full mb-4 shadow-lg relative">
                            <i class="fas fa-users text-white text-xl"></i>
                            <div
                                class="absolute inset-0 rounded-full bg-gradient-to-br from-[#90143c] to-[#b01a47] opacity-20 animate-ping">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-6 max-w-md mx-auto">
                            <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                                <div class="text-base font-bold text-[#00553d]">{{ $active_staff ?? '0' }}</div>
                                <div class="text-xs text-gray-600">Active Staff</div>
                            </div>
                            <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                                <div class="text-base font-bold text-[#b01a47]">{{ $resigned_staff ?? '0' }}</div>
                                <div class="text-xs text-gray-600">Resigned Staff</div>
                            </div>
                        </div>
                    </div>

                    <div id="alert-container" class="mb-4"></div>

                    <div class="space-y-4">
                        <!-- Add Staff Accordion -->
                        <div
                            class="bg-white rounded-lg shadow-md overflow-hidden staff-card border border-[#ffcc34] slide-up">
                            <button
                                class="accordion-toggle w-full flex justify-between items-center p-4 gradient-btn text-white transition-all duration-500"
                                data-target="add-staff" aria-expanded="false" aria-controls="add-staff">
                                <div class="flex items-center space-x-3">
                                    <div class="p-1.5 bg-white/20 rounded-md">
                                        <i class="fas fa-user-plus text-base"></i>
                                    </div>
                                    <div class="text-left">
                                        <span class="text-xs font-semibold block">Add New Staff</span>
                                        <span class="text-xs opacity-80">Create a new staff member record</span>
                                    </div>
                                </div>
                                <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>
                            <div id="add-staff" class="accordion-content" role="region"
                                aria-labelledby="add-staff-toggle">
                                <form id="add-staff-form" action="{{ route('staff.store') }}" method="POST"
                                    class="space-y-3 p-4" aria-label="Add Staff Form">
                                    @csrf
                                    <div>
                                        <label for="name"
                                            class="flex items-center space-x-2 text-xs font-semibold text-[#00553d] mb-2">
                                            <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md">
                                                <i class="fas fa-user text-white text-xs"></i>
                                            </div>
                                            <div>
                                                <span>Name</span>
                                                <span class="text-red-500">*</span>
                                                <div class="text-[0.6rem] font-normal text-gray-500 mt-1">Full name of
                                                    the staff
                                                    member</div>
                                            </div>
                                        </label>
                                        <input type="text" name="name" id="name" required
                                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 hover:shadow-md @error('name') border-red-500 @enderror"
                                            placeholder="Enter full name" aria-label="Staff Name">
                                        @error('name')
                                            <p class="text-[0.6rem] text-red-500 mt-2 flex items-center"><i
                                                    class="fas fa-exclamation-triangle mr-1 text-xs"></i>{{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="department"
                                            class="flex items-center space-x-2 text-xs font-semibold text-[#00553d] mb-2">
                                            <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md">
                                                <i class="fas fa-building text-white text-xs"></i>
                                            </div>
                                            <div>
                                                <span>Department</span>
                                                <span class="text-red-500">*</span>
                                                <div class="text-[0.6rem] font-normal text-gray-500 mt-1">Select staff's
                                                    department</div>
                                            </div>
                                        </label>
                                        <select name="department" id="department" required
                                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 hover:shadow-md @error('department') border-red-500 @enderror"
                                            aria-label="Department">
                                            <option value="">Select Department</option>
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept->name }}"
                                                    {{ old('department') == $dept->name ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department')
                                            <p class="text-[0.6rem] text-red-500 mt-2 flex items-center"><i
                                                    class="fas fa-exclamation-triangle mr-1 text-xs"></i>{{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="email"
                                            class="flex items-center space-x-2 text-xs font-semibold text-[#00553d] mb-2">
                                            <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md">
                                                <i class="fas fa-envelope text-white text-xs"></i>
                                            </div>
                                            <div>
                                                <span>Email</span>
                                                <span class="text-red-500">*</span>
                                                <div class="text-[0.6rem] font-normal text-gray-500 mt-1">Staff's email
                                                    address
                                                </div>
                                            </div>
                                        </label>
                                        <input type="email" name="email" id="email" required
                                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 hover:shadow-md @error('email') border-red-500 @enderror"
                                            placeholder="Enter email" aria-label="Email">
                                        @error('email')
                                            <p class="text-[0.6rem] text-red-500 mt-2 flex items-center"><i
                                                    class="fas fa-exclamation-triangle mr-1 text-xs"></i>{{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="status"
                                            class="flex items-center space-x-2 text-xs font-semibold text-[#00553d] mb-2">
                                            <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md">
                                                <i class="fas fa-toggle-on text-white text-xs"></i>
                                            </div>
                                            <div>
                                                <span>Status</span>
                                                <span class="text-red-500">*</span>
                                                <div class="text-[0.6rem] font-normal text-gray-500 mt-1">Current
                                                    employment
                                                    status</div>
                                            </div>
                                        </label>
                                        <select name="status" id="status" required
                                            class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 hover:shadow-md @error('status') border-red-500 @enderror"
                                            aria-label="Status">
                                            <option value="">Select Status</option>
                                            <option value="Active" selected>Active</option>
                                            <option value="Resigned">Resigned</option>
                                        </select>
                                        @error('status')
                                            <p class="text-[0.6rem] text-red-500 mt-2 flex items-center"><i
                                                    class="fas fa-exclamation-triangle mr-1 text-xs"></i>{{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                    <button type="submit" id="add-staff-btn"
                                        class="gradient-btn w-full text-white font-semibold py-3 px-6 rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center justify-center transition-all duration-300">
                                        <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                        <span class="btn-text flex items-center">
                                            <i class="fas fa-user-plus mr-2"></i>
                                            Add Staff
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Staff List -->
                        <div
                            class="bg-white rounded-lg shadow-md overflow-hidden staff-card border border-[#ffcc34] slide-up">
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xs font-bold text-[#00553d] flex items-center">
                                        <div
                                            class="p-1.5 bg-gradient-to-br from-[#00553d] to-[#007a52] rounded-md mr-2">
                                            <i class="fas fa-list text-white text-xs"></i>
                                        </div>
                                        Staff List
                                    </h3>
                                    <button id="export-btn"
                                        class="gradient-btn px-4 py-2 text-white font-semibold rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300">
                                        <i class="fas fa-file-export mr-2"></i>Export CSV
                                    </button>
                                </div>

                                <!-- Filter Form -->
                                <form id="filter-form" action="{{ route('staff.index') }}" method="GET"
                                    class="space-y-3 mb-4" aria-label="Filter Staff Form">
                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <div class="flex-1">
                                            <input type="text" name="search" id="search"
                                                placeholder="Search by name or email..."
                                                value="{{ request('search') }}"
                                                class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 hover:shadow-md"
                                                aria-label="Search staff">
                                        </div>
                                        <div class="flex-1">
                                            <select name="department" id="filter-department"
                                                class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 hover:shadow-md"
                                                aria-label="Filter Department">
                                                <option value="">All Departments</option>
                                                @foreach ($departments as $dept)
                                                    <option value="{{ $dept->name }}"
                                                        {{ request('department') == $dept->name ? 'selected' : '' }}>
                                                        {{ $dept->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex-1">
                                            <select name="status" id="filter-status"
                                                class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 hover:shadow-md"
                                                aria-label="Filter Status">
                                                <option value="">All Statuses</option>
                                                <option value="Active"
                                                    {{ request('status') == 'Active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="Resigned"
                                                    {{ request('status') == 'Resigned' ? 'selected' : '' }}>Resigned
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </form>

                                @if ($staff->isEmpty())
                                    <div
                                        class="text-center py-12 bg-gradient-to-br from-gray-50 to-white rounded-lg border-2 border-dashed border-gray-300 relative overflow-hidden">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-br from-blue-50/30 to-purple-50/30">
                                        </div>
                                        <div class="relative z-10">
                                            <div
                                                class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-users text-2xl text-gray-400"></i>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2 font-medium">No staff found</p>
                                            <p class="text-[0.6rem] text-gray-400">Add your first staff member using
                                                the form
                                                above</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-[#ffcc34]"
                                            aria-label="Staff Records">
                                            <thead class="bg-gradient-to-r from-[#90143c] to-[#b01a47] text-white">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-5 py-3 text-left text-[0.65rem] font-medium uppercase tracking-wider">
                                                        Name</th>
                                                    <th scope="col"
                                                        class="px-5 py-3 text-left text-[0.65rem] font-medium uppercase tracking-wider">
                                                        Department</th>
                                                    <th scope="col"
                                                        class="px-5 py-3 text-left text-[0.65rem] font-medium uppercase tracking-wider">
                                                        Email</th>
                                                    <th scope="col"
                                                        class="px-5 py-3 text-left text-[0.65rem] font-medium uppercase tracking-wider">
                                                        Status</th>
                                                    <th scope="col"
                                                        class="px-5 py-3 text-left text-[0.65rem] font-medium uppercase tracking-wider">
                                                        Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="staff-table-body" class="bg-white divide-y divide-[#ffcc34]">
                                                @foreach ($staff as $member)
                                                    <tr class="slide-up">
                                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">
                                                            {{ $member->name }}</td>
                                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">
                                                            {{ $member->department }}</td>
                                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-[#00553d]">
                                                            {{ $member->email }}</td>
                                                        <td class="px-5 py-3 whitespace-nowrap">
                                                            <span class="flex items-center space-x-1 text-xs">
                                                                <span
                                                                    class="status-indicator {{ $member->status === 'Active' ? 'status-active' : 'status-resigned' }}"></span>
                                                                <span>{{ $member->status }}</span>
                                                            </span>
                                                        </td>
                                                        <td class="px-5 py-3 whitespace-nowrap text-xs">
                                                            <button
                                                                class="view-btn text-[#007a52] hover:bg-blue-50 px-3 py-1.5 rounded-md font-semibold transition-all duration-200 border border-blue-200 hover:border-blue-300"
                                                                data-id="{{ $member->id }}"
                                                                data-name="{{ $member->name }}"
                                                                aria-label="View logs for {{ $member->name }}">
                                                                <i class="fas fa-info-circle mr-1"></i>View
                                                            </button>
                                                            <button
                                                                class="edit-btn text-[#00553d] hover:bg-blue-50 px-3 py-1.5 rounded-md font-semibold transition-all duration-200 border border-blue-200 hover:border-blue-300"
                                                                data-id="{{ $member->id }}"
                                                                data-name="{{ $member->name }}"
                                                                data-department="{{ $member->department }}"
                                                                data-email="{{ $member->email }}"
                                                                data-status="{{ $member->status }}"
                                                                aria-label="Edit staff {{ $member->name }}">
                                                                <i class="fas fa-edit mr-1"></i>Edit
                                                            </button>
                                                            <form action="{{ route('staff.destroy', $member->id) }}"
                                                                method="POST" class="inline delete-staff-form"
                                                                data-name="{{ $member->name }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="delete-btn text-[#90143c] hover:bg-red-50 px-3 py-1.5 rounded-md font-semibold transition-all duration-200 border border-red-200 hover:border-red-300"
                                                                    aria-label="Delete staff {{ $member->name }}">
                                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-4 pagination-container">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-[#00553d]">Show</span>
                                            <select name="per_page" id="per-page"
                                                class="px-2 py-1 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d]">
                                                <option value="20"
                                                    {{ request('per_page') == 20 ? 'selected' : '' }}>20
                                                </option>
                                                <option value="50"
                                                    {{ request('per_page') == 50 ? 'selected' : '' }}>50
                                                </option>
                                                <option value="100"
                                                    {{ request('per_page') == 100 ? 'selected' : '' }}>100
                                                </option>
                                            </select>
                                            <span class="text-xs text-[#00553d]">entries</span>
                                        </div>
                                        <div class="text-xs text-[#00553d]">
                                            Showing {{ $staff->firstItem() }} to {{ $staff->lastItem() }} of
                                            {{ $staff->total() }} results
                                        </div>
                                        <div class="flex items-center gap-2">
                                            {{ $staff->appends(request()->query())->links() }}
                                            <input type="number" id="page-input" min="1"
                                                max="{{ $staff->lastPage() }}"
                                                class="w-16 px-2 py-1 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d]"
                                                placeholder="Page">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- History Logs Modal -->
                    <div id="history-logs-modal"
                        class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50"
                        role="dialog" aria-labelledby="history-logs-title" aria-modal="true">
                        <div
                            class="bg-white rounded-xl shadow-lg max-w-6xl w-full mx-4 max-h-[80vh] flex flex-col border border-[#ffcc34]">
                            <div
                                class="bg-gradient-to-r from-[#90143c] to-[#b01a47] px-5 py-3 rounded-t-xl flex justify-between items-center">
                                <h2 id="history-logs-title" class="text-xs font-semibold text-white">History Logs</h2>
                                <div class="flex items-center space-x-2">
                                    <button id="export-logs-btn"
                                        class="gradient-btn px-4 py-2 text-white font-semibold rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300"
                                        data-staff-id="" aria-label="Export history logs as CSV">
                                        <i class="fas fa-file-export mr-2"></i>Export CSV
                                    </button>
                                    <button type="button" class="close-logs-btn text-white hover:text-gray-200"
                                        aria-label="Close history logs modal">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="p-5 overflow-y-auto overflow-x-hidden flex-1">
                                <div id="history-logs-content" class="w-full">
                                    <!-- Table will be injected here -->
                                </div>
                            </div>
                            <div class="px-5 py-3 bg-gray-50 rounded-b-xl border-t border-[#ffcc34]">
                                <button type="button"
                                    class="close-logs-btn bg-gray-600 hover:bg-gray-700 text-white font-medium py-1.5 px-3 rounded-lg text-xs transition-all duration-200 border border-[#ffcc34] shadow-sm">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    @if (session('success'))
                        <script>
                            Swal.fire({
                                title: 'Success!',
                                html: `
                            <div class="text-left">
                                <div class="flex items-center space-x-2 mb-3">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span>{!! session('success') !!}</span>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                                    <div class="text-xs text-green-700">
                                        <strong>Next Steps:</strong>
                                        <ul class="mt-2 space-y-1 text-[0.6rem]">
                                            <li>• Changes are now active</li>
                                            <li>• Notify staff of updates if needed</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `,
                                icon: 'success',
                                confirmButtonColor: '#00553d',
                                timer: 5000
                            });
                        </script>
                    @endif
                    @if (session('error'))
                        <script>
                            Swal.fire({
                                title: 'Error',
                                html: `
                            <div class="text-left">
                                <div class="flex items-center space-x-2 mb-3">
                                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                                    <span>{!! session('error') !!}</span>
                                </div>
                                <div class="bg-red-100 p-3 rounded-lg border border-red-200">
                                    <div class="text-xs text-red-700">
                                        <strong>Troubleshooting Tips:</strong>
                                        <ul class="mt-2 space-y-1 text-[0.6rem]">
                                            <li>• Check input values</li>
                                            <li>• Try again or contact support</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `,
                                icon: 'error',
                                confirmButtonColor: '#90143c'
                            });
                        </script>
                    @endif

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            // Accordion Handling
                            document.querySelectorAll('.accordion-toggle').forEach(toggle => {
                                toggle.addEventListener('click', () => {
                                    const target = document.getElementById(toggle.dataset.target);
                                    const icon = toggle.querySelector('.accordion-icon');
                                    const isOpen = target.classList.contains('open');
                                    target.classList.toggle('open');
                                    icon.classList.toggle('rotate-180');
                                    toggle.setAttribute('aria-expanded', !isOpen);
                                    if (target.classList.contains('open')) {
                                        target.style.animation = 'fadeIn 0.3s ease-out';
                                    }
                                });
                            });

                            // Utility Functions
                            const setLoadingState = (button, isLoading) => {
                                if (button) {
                                    button.disabled = isLoading;
                                    button.classList.toggle('btn-loading', isLoading);
                                }
                            };

                            const showAlert = (message, type = 'info') => {
                                const bgColor = type === 'success' ? '#d1fae5' : type === 'error' ? '#fee2e2' : '#e0f2fe';
                                const textColor = type === 'success' ? '#065f46' : type === 'error' ? '#b91c1c' : '#1e40af';
                                const alertDiv = document.createElement('div');
                                alertDiv.className =
                                    `p-3 rounded-lg mb-3 border text-xs ${type === 'error' ? 'border-red-200' : 'border-gray-200'}`;
                                alertDiv.style.backgroundColor = bgColor;
                                alertDiv.style.color = textColor;
                                alertDiv.innerHTML = `
                            <div class="flex items-center space-x-2">
                                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
                                <span>${message}</span>
                            </div>
                        `;
                                const container = document.getElementById('alert-container');
                                if (container) {
                                    container.appendChild(alertDiv);
                                    setTimeout(() => alertDiv.remove(), 5000);
                                }
                            };

                            // Form Submission Handling
                            const handleFormSubmit = async (form, button, successMsg, errorMsg = 'An error occurred.') => {
                                const formData = new FormData(form);
                                setLoadingState(button, true);
                                Swal.fire({
                                    title: 'Processing...',
                                    html: 'Please wait...',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                try {
                                    const methodField = form.querySelector('input[name="_method"]');
                                    const method = form.id === 'add-staff-form' ? 'POST' : (methodField && methodField
                                        .value === 'DELETE' ? 'DELETE' : 'PUT');
                                    const response = await fetch(form.action, {
                                        method: method,
                                        headers: {
                                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                                            'Accept': 'application/json',
                                            'Cache-Control': 'no-cache'
                                        },
                                        body: method === 'DELETE' ? null : formData
                                    });
                                    const data = await response.json();
                                    if (!response.ok || data.status === 'error') {
                                        let errorMessage = data.message || errorMsg;
                                        if (data.errors) {
                                            errorMessage = Object.values(data.errors).flat().join('<br>');
                                        }
                                        throw new Error(errorMessage);
                                    }
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        html: data.message || successMsg,
                                        confirmButtonColor: '#00553d',
                                        timer: 3000
                                    }).then(() => {
                                        window.location.href = window.location.pathname + '?' + new URLSearchParams(
                                            window.location.search).toString() + '&_=' + Date.now();
                                    });
                                } catch (error) {
                                    console.error('Form submission error:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        html: error.message || errorMsg,
                                        confirmButtonColor: '#90143c'
                                    });
                                } finally {
                                    setLoadingState(button, false);
                                }
                            };

                            // Auto-submit filter form on input change
                            const filterForm = document.getElementById('filter-form');
                            const searchInput = document.getElementById('search');
                            const departmentSelect = document.getElementById('filter-department');
                            const statusSelect = document.getElementById('filter-status');
                            const perPageSelect = document.getElementById('per-page');
                            const pageInput = document.getElementById('page-input');

                            const submitFilters = () => {
                                const url = new URL(window.location);
                                if (searchInput.value) {
                                    url.searchParams.set('search', searchInput.value);
                                } else {
                                    url.searchParams.delete('search');
                                }
                                if (departmentSelect.value) {
                                    url.searchParams.set('department', departmentSelect.value);
                                } else {
                                    url.searchParams.delete('department');
                                }
                                if (statusSelect.value) {
                                    url.searchParams.set('status', statusSelect.value);
                                } else {
                                    url.searchParams.delete('status');
                                }
                                url.searchParams.set('per_page', perPageSelect.value);
                                url.searchParams.set('_', Date.now());
                                window.location.href = url.toString();
                            };

                            if (searchInput) {
                                searchInput.addEventListener('input', debounce(() => submitFilters(), 300));
                            }
                            if (departmentSelect) {
                                departmentSelect.addEventListener('change', submitFilters);
                            }
                            if (statusSelect) {
                                statusSelect.addEventListener('change', submitFilters);
                            }
                            if (perPageSelect) {
                                perPageSelect.addEventListener('change', submitFilters);
                            }
                            if (pageInput) {
                                pageInput.addEventListener('change', () => {
                                    const page = parseInt(pageInput.value);
                                    if (page >= 1 && page <= {{ $staff->lastPage() }}) {
                                        const url = new URL(window.location);
                                        url.searchParams.set('page', page);
                                        url.searchParams.set('_', Date.now());
                                        window.location.href = url.toString();
                                    }
                                });
                            }

                            // Debounce function for search input
                            function debounce(func, wait) {
                                let timeout;
                                return function executedFunction(...args) {
                                    const later = () => {
                                        clearTimeout(timeout);
                                        func(...args);
                                    };
                                    clearTimeout(timeout);
                                    timeout = setTimeout(later, wait);
                                };
                            }

                            // Add Staff Form
                            const addStaffForm = document.getElementById('add-staff-form');
                            if (addStaffForm) {
                                addStaffForm.addEventListener('submit', async (e) => {
                                    e.preventDefault();
                                    const name = addStaffForm.querySelector('#name').value.trim();
                                    const email = addStaffForm.querySelector('#email').value.trim();
                                    const department = addStaffForm.querySelector('#department').value;
                                    const status = addStaffForm.querySelector('#status').value;
                                    if (!name || !email || !department || !status || !email.includes('@')) {
                                        showAlert('Please fill out all required fields correctly.', 'error');
                                        return;
                                    }
                                    Swal.fire({
                                        title: 'Add Staff Member?',
                                        html: `
                                    <div class="text-left space-y-3">
                                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                            <div class="text-xs text-blue-600">
                                                <strong>Name:</strong> ${name}<br>
                                                <strong>Email:</strong> ${email}<br>
                                                <strong>Department:</strong> ${department}<br>
                                                <strong>Status:</strong> ${status}
                                            </div>
                                        </div>
                                    </div>
                                `,
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonColor: '#00553d',
                                        cancelButtonColor: '#90143c',
                                        confirmButtonText: '<i class="fas fa-user-plus mr-1"></i>Confirm',
                                        cancelButtonText: 'Cancel'
                                    }).then(result => {
                                        if (result.isConfirmed) {
                                            handleFormSubmit(addStaffForm, addStaffForm.querySelector(
                                                '#add-staff-btn'), 'Staff added successfully!');
                                        }
                                    });
                                });
                            }

                            // Edit Staff
                            document.querySelectorAll('.edit-btn').forEach(btn => {
                                btn.addEventListener('click', () => {
                                    Swal.fire({
                                        title: 'Edit Staff Member',
                                        html: `
                <div class="space-y-3">
                    <div class="bg-blue-50 p-3 rounded-md border border-blue-200">
                        <div class="text-xs text-blue-600">
                            <strong>Current Name:</strong> ${btn.dataset.name}<br>
                            <strong>Email:</strong> ${btn.dataset.email}<br>
                            <strong>Department:</strong> ${btn.dataset.department}<br>
                            <strong>Status:</strong> ${btn.dataset.status}
                        </div>
                    </div>
                    <input id="swal-name" class="w-full px-3 py-2 text-xs border border-[#ffcc34] rounded-md" value="${btn.dataset.name}" placeholder="Name">
                    <input id="swal-email" class="w-full px-3 py-2 text-xs border border-[#ffcc34] rounded-md" value="${btn.dataset.email}" placeholder="Email">
                    <select id="swal-department" class="w-full px-3 py-3 text-xs border border-[#ffcc34] rounded-md">
                        <option value="">Select Department</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->name }}">{{ $dept->name }}</option>                                        
                        @endforeach
                    </select>
                    <select id="swal-status" class="w-full px-3 py-3 text-xs border border-[#ffcc34] rounded-md">
                        <option value="Active">Active</option>
                        <option value="Resigned">Resigned</option>
                    </select>
                </div>
            `,
                                        showCancelButton: true,
                                        confirmButtonColor: '#00553d',
                                        cancelButtonColor: '#90143c',
                                        confirmButtonText: '<i class="fas fa-save mr-1"></i>Confirm',
                                        cancelButtonText: 'Cancel',
                                        didOpen: () => {
                                            // Set the current values when modal opens
                                            document.getElementById('swal-department').value = btn
                                                .dataset.department;
                                            document.getElementById('swal-status').value = btn.dataset
                                                .status;
                                        },
                                        preConfirm: () => {
                                            const name = document.getElementById('swal-name').value
                                                .trim();
                                            const email = document.getElementById('swal-email').value
                                                .trim();
                                            const department = document.getElementById(
                                                'swal-department').value;
                                            const status = document.getElementById('swal-status').value;

                                            if (!name || !email || !department || !status || !email
                                                .includes('@')) {
                                                Swal.showValidationMessage(
                                                    'Please fill out all fields correctly.');
                                                return false;
                                            }

                                            return {
                                                name,
                                                email,
                                                department,
                                                status
                                            };
                                        }
                                    }).then(async (result) => {
                                        if (result.isConfirmed) {
                                            const {
                                                name,
                                                email,
                                                department,
                                                status
                                            } = result.value;

                                            // Create FormData manually with the updated values
                                            const formData = new FormData();
                                            formData.append('_token', document.querySelector(
                                                'meta[name="csrf-token"]').getAttribute(
                                                'content'));
                                            formData.append('_method', 'PUT');
                                            formData.append('name', name);
                                            formData.append('email', email);
                                            formData.append('department', department);
                                            formData.append('status', status);

                                            setLoadingState(btn, true);
                                            Swal.fire({
                                                title: 'Processing...',
                                                html: 'Please wait...',
                                                allowOutsideClick: false,
                                                showConfirmButton: false,
                                                didOpen: () => {
                                                    Swal.showLoading();
                                                }
                                            });

                                            try {
                                                const response = await fetch(
                                                    `/staff/${btn.dataset.id}`, {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-CSRF-TOKEN': document.querySelector(
                                                                    'meta[name="csrf-token"]')
                                                                .getAttribute('content'),
                                                            'Accept': 'application/json',
                                                            'Cache-Control': 'no-cache'
                                                        },
                                                        body: formData
                                                    });

                                                const data = await response.json();

                                                if (!response.ok || data.status === 'error') {
                                                    let errorMessage = data.message ||
                                                        'An error occurred.';
                                                    if (data.errors) {
                                                        errorMessage = Object.values(data.errors).flat()
                                                            .join('<br>');
                                                    }
                                                    throw new Error(errorMessage);
                                                }

                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Success!',
                                                    html: data.message ||
                                                        'Staff updated successfully!',
                                                    confirmButtonColor: '#00553d',
                                                    timer: 3000
                                                }).then(() => {
                                                    window.location.href = window.location
                                                        .pathname + '?' + new URLSearchParams(
                                                            window.location.search).toString() +
                                                        '&_=' + Date.now();
                                                });

                                            } catch (error) {
                                                console.error('Form submission error:', error);
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error',
                                                    html: error.message || 'An error occurred.',
                                                    confirmButtonColor: '#90143c'
                                                });
                                            } finally {
                                                setLoadingState(btn, false);
                                            }
                                        }
                                    });
                                });
                            });

                            // Delete Staff
                            document.querySelectorAll('.delete-staff-form').forEach(form => {
                                form.addEventListener('submit', async (e) => {
                                    e.preventDefault();
                                    console.log('Delete form submitted:', form.dataset.name);
                                    Swal.fire({
                                        title: `Delete ${form.dataset.name}?`,
                                        html: `
                                    <div class="text-left">
                                        <div class="bg-red-50 p-3 rounded-md border border-red-200">
                                            <div class="text-xs text-red-600">
                                                This will permanently delete staff member "<strong>${form.dataset.name}</strong>".
                                            </div>
                                        </div>
                                        <div class="mt-3 text-gray-600">
                                            <p class="text-xs">Type "<strong>DELETE</strong>" to confirm:</p>
                                            <input type="text" id="confirmation" class="w-full px-3 py-2 text-xs border border-[#ffcc34] rounded-md mt-1">
                                        </div>
                                    </div>
                                `,
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: '<i class="fas fa-trash mr-1"></i>Confirm',
                                        cancelButtonText: 'Cancel',
                                        preConfirm: () => {
                                            if (document.getElementById('confirmation').value !==
                                                'DELETE') {
                                                Swal.showValidationMessage(
                                                    'You must type "DELETE" to confirm.');
                                                return false;
                                            }
                                        }
                                    }).then(result => {
                                        if (result.isConfirmed) {
                                            handleFormSubmit(form, form.querySelector('.delete-btn'),
                                                `${form.dataset.name} deleted successfully!`);
                                        }
                                    });
                                });
                            });

                            // View History Logs
                            document.querySelectorAll('.view-btn').forEach(btn => {
                                btn.addEventListener('click', async (e) => {
                                    e.preventDefault();
                                    console.log('View button clicked:', {
                                        id: btn.dataset.id,
                                        name: btn.dataset.name
                                    });
                                    const modal = document.getElementById('history-logs-modal');
                                    const content = document.getElementById('history-logs-content');
                                    const exportBtn = document.getElementById('export-logs-btn');
                                    exportBtn.dataset.staffId = btn.dataset.id; // Set staff ID for export
                                    content.innerHTML = `
                                        <div class="text-center py-4">
                                            <i class="fas fa-spinner fa-spin text-xl text-[#00553d]"></i>
                                            <p class="mt-2 text-xs text-[#00553d]">Loading history...</p>
                                        </div>
                                    `;
                                    modal.classList.remove('hidden');
                                    modal.classList.add('flex');
                                    document.body.classList.add('modal-open'); // Prevent background scrolling

                                    try {
                                        const response = await fetch(`/staff/${btn.dataset.id}/history-logs`, {
                                            headers: {
                                                'Accept': 'application/json',
                                                'Cache-Control': 'no-cache'
                                            }
                                        });
                                        const data = await response.json();
                                        console.log('History logs response:', data);
                                        if (!response.ok || data.status === 'error') {
                                            throw new Error(data.message || 'Failed to load history logs');
                                        }
                                        if (data.logs && data.logs.length > 0) {
                                            let html = `
                                                <div class="mb-4">
                                                    <h3 class="text-xs font-semibold text-[#00553d]">Staff: ${data.staff_name || 'Unknown'}</h3>
                                                    <p class="text-[0.6rem] text-[#666]">Total logs: ${data.logs.length}</p>
                                                </div>
                                                <div class="w-full">
                                                    <table class="table-auto w-full divide-y divide-[#ffcc34]">
                                                        <thead class="bg-gradient-to-br from-[#90143c] to-[#b01a47]">
                                                            <tr>
                                                                <th class="px-5 py-2 text-left text-[0.65rem] font-medium uppercase tracking-wider text-white">Date</th>
                                                                <th class="px-5 py-2 text-left text-[0.65rem] font-medium uppercase tracking-wider text-white">Action</th>
                                                                <th class="px-5 py-2 text-left text-[0.65rem] font-medium uppercase tracking-wider text-white">Model</th>
                                                                <th class="px-5 py-2 text-left text-[0.65rem] font-medium uppercase tracking-wider text-white">Changes</th>
                                                                <th class="px-5 py-2 text-left text-[0.65rem] font-medium uppercase tracking-wider text-white">Description</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-[#ffcc34]">
                                            `;
                                            data.logs.forEach(log => {
                                                let changes = '-';
                                                try {
                                                    const oldValues = typeof log.old_values ===
                                                        'string' ? JSON.parse(log.old_values) : log
                                                        .old_values || {};
                                                    const newValues = typeof log.new_values ===
                                                        'string' ? JSON.parse(log.new_values) : log
                                                        .new_values || {};
                                                    changes = Object.keys(newValues)
                                                        .map(key => {
                                                            const oldVal = oldValues[key] || 'none';
                                                            const newVal = newValues[key] || 'none';
                                                            if (log.model_brand === 'equipment') {
                                                                if (['model', 'serial_number',
                                                                        'type', 'processor',
                                                                        'issue_date', 'status'
                                                                    ].includes(key)) {
                                                                    return `${key}: ${oldVal} -> ${newVal}`;
                                                                }
                                                                return null;
                                                            }
                                                            return `${key}: ${oldVal} -> ${newVal}`;
                                                        })
                                                        .filter(Boolean)
                                                        .join('<br>');
                                                    if (!changes) {
                                                        changes = log.model_brand === 'equipment' ?
                                                            'Equipment action' :
                                                            'Changed (details unavailable)';
                                                    }
                                                } catch (e) {
                                                    changes = log.model_brand === 'equipment' ?
                                                        'Equipment action (details unavailable)' :
                                                        'Changed (details unavailable)';
                                                }
                                                html += `
                                                    <tr>
                                                        <td class="px-5 py-3 text-xs text-black">${log.action_date || '-'}</td>
                                                        <td class="px-5 py-3 text-xs text-black">${log.action || '-'}</td>
                                                        <td class="px-5 py-3 text-xs text-black">${log.model_brand || '-'} (ID: ${log.model_id || '-'})</td>
                                                        <td class="px-5 py-3 text-xs text-black break-words">${changes}</td>
                                                        <td class="px-5 py-3 text-xs text-black">${log.description || '-'}</td>
                                                    </tr>
                                                `;
                                            });
                                            html += `
                                                        </tbody>
                                                    </table>
                                                </div>
                                            `;
                                            content.innerHTML = html;
                                        } else {
                                            content.innerHTML = `
                                                <div class="text-center py-8">
                                                    <p class="text-xs text-gray-500">No history logs found for this staff member.</p>
                                                </div>
                                            `;
                                        }
                                    } catch (error) {
                                        console.error('Error loading history logs:', error);
                                        showAlert(`Error loading logs: ${error.message}`, 'error');
                                        content.innerHTML = `
                                            <div class="text-center py-8">
                                                <p class="text-xs text-red-600">Failed to load logs. Please try again.</p>
                                            </div>
                                        `;
                                    }
                                });
                            });

                            // Close History Modal
                            document.querySelectorAll('.close-logs-btn').forEach(btn => {
                                btn.addEventListener('click', () => {
                                    console.log('Closing history logs modal');
                                    const modal = document.getElementById('history-logs-modal');
                                    modal.classList.add('hidden');
                                    modal.classList.remove('flex');
                                    document.body.classList.remove('modal-open'); // Restore background scrolling
                                });
                            });

                            // Export History Logs CSV
                            document.getElementById('export-logs-btn').addEventListener('click', async () => {
                                const staffId = document.getElementById('export-logs-btn').dataset.staffId;
                                console.log('Export history logs CSV clicked for staff ID:', staffId);
                                Swal.fire({
                                    title: 'Export History Logs?',
                                    text: 'This will generate a CSV file of the history logs for this staff member.',
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#00553d',
                                    cancelButtonColor: '#90143c',
                                    confirmButtonText: '<i class="fas fa-file-export mr-1"></i>Confirm',
                                    cancelButtonText: 'Cancel'
                                }).then(result => {
                                    if (result.isConfirmed) {
                                        window.location.href =
                                            `/staff/${staffId}/export-history-logs?_=${Date.now()}`;
                                    }
                                });
                            });

                            // Export Staff CSV
                            document.getElementById('export-btn').addEventListener('click', async () => {
                                console.log('Export CSV clicked');
                                Swal.fire({
                                    title: 'Export Staff List?',
                                    text: 'This will generate a CSV file of all staff records.',
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#00553d',
                                    cancelButtonColor: '#90143c',
                                    confirmButtonText: '<i class="fas fa-file-export mr-1"></i>Confirm',
                                    cancelButtonText: 'Cancel'
                                }).then(result => {
                                    if (result.isConfirmed) {
                                        window.location.href = '/staff/export-csv?_=' + Date.now();
                                    }
                                });
                            });
                        });
                    </script>
                </div>
            </div>
</x-app-layout>
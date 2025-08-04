<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base text-[#00553d] leading-tight">
            {{ __('Settings') }}
        </h2>
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
            /* Increased to accommodate more departments */
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

        /* Prevent department names from overflowing */
        .department-name {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>

    <div class="flex min-h-screen bg-gray-50">
        <button id="toggleSidebar"
            class="md:hidden fixed top-3 left-3 z-50 bg-[#90143c] text-white p-1.5 rounded-md border border-[#ffcc34] shadow-md hover:shadow-lg transition-all duration-200">
            <i class="fas fa-bars text-xs"></i>
        </button>

        <div class="flex-1 container mx-auto px-3 py-6 max-w-6xl">
            <div class="text-center mb-8 fade-in">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-full mb-4 shadow-lg relative">
                    <i class="fas fa-cog text-white text-xl animate-spin" style="animation-duration: 8s;"></i>
                    <div
                        class="absolute inset-0 rounded-full bg-gradient-to-br from-[#90143c] to-[#b01a47] opacity-20 animate-ping">
                    </div>
                </div>
                <h1
                    class="text-2xl font-bold bg-gradient-to-r from-[#90143c] to-[#00553d] bg-clip-text text-transparent">
                    System Settings</h1>
                <p class="text-xs text-[#00553d] opacity-80 max-w-sm mx-auto">Configure system settings and manage
                    departments</p>

                <div class="grid grid-cols-3 gap-3 mt-6 max-w-md mx-auto">
                    <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                        <div class="text-base font-bold text-[#90143c]">{{ $total_departments ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-600">Departments</div>
                    </div>
                    <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                        <div class="text-base font-bold text-[#00553d]">{{ $settings['default_return_period'] ?? 30 }}
                        </div>
                        <div class="text-xs text-gray-600">Return Days</div>
                    </div>
                    <div class="bg-white p-2 rounded-md shadow-sm border border-[#ffcc34]/30">
                        <div class="text-base font-bold text-[#b01a47]">
                            <span class="status-indicator status-active"></span>Active
                        </div>
                        <div class="text-xs text-gray-600">System Status</div>
                    </div>
                </div>
            </div>

            <div id="alert-container" class="mb-4"></div>

            <div class="space-y-4">
                <!-- Department Management moved to the top -->
                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center p-4 gradient-btn text-white transition-all duration-500"
                        data-target="department-management">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 bg-white/20 rounded-md">
                                <i class="fas fa-building text-base"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-semibold block">Department Management</span>
                                <span class="text-xs opacity-80">Organize and manage organizational departments</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="bg-white bg-opacity-30 text-xs px-2 py-0.5 rounded-full font-medium">{{ $total_departments ?? 'N/A' }}
                                departments</span>
                            <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300 rotate-180"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="department-management" class="accordion-content open">
                        <div class="p-4">
                            <div
                                class="bg-gradient-to-br from-blue-50 to-purple-50 p-4 rounded-lg border border-blue-200 mb-4 relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-200/30 to-purple-200/30 rounded-full -translate-y-12 translate-x-12">
                                </div>
                                <h3 class="text-xs font-bold text-[#00553d] mb-3 flex items-center relative z-10">
                                    <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md mr-2">
                                        <i class="fas fa-plus-circle text-white text-xs"></i>
                                    </div>
                                    Add New Department
                                    <span
                                        class="ml-auto text-[0.6rem] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full">Quick
                                        Add</span>
                                </h3>
                                <form id="department-form" action="{{ route('settings.department.store') }}"
                                    method="POST" class="space-y-3 relative z-10">
                                    @csrf
                                    <div class="flex space-x-3">
                                        <div class="flex-1">
                                            <input type="text" name="department_name" id="department_name"
                                                required
                                                class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent transition-all duration-300 hover:shadow-md @error('department_name') border-red-500 @enderror"
                                                placeholder="Enter department name (e.g., Human Resources, IT Department)...">
                                            @error('department_name')
                                                <p class="text-[0.6rem] text-red-500 mt-2 flex items-center"><i
                                                        class="fas fa-exclamation-triangle mr-1 text-xs"></i>{{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                        <button type="submit" id="add-department-btn"
                                            class="gradient-btn px-6 py-3 text-white font-semibold rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center transition-all duration-300">
                                            <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                            <span class="btn-text flex items-center">
                                                <i class="fas fa-plus mr-2"></i>
                                                Add Department
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
                                <h3 class="text-xs font-semibold text-[#00553d]">Filter Departments</h3>
                                <form method="GET" action="{{ route('settings') }}"
                                    class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <input type="text" name="department_search" id="department-search"
                                        placeholder="Search departments..."
                                        class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-64"
                                        value="{{ request('department_search') }}">
                                    <select name="per_page" id="per-page"
                                        class="px-3 py-3 rounded-lg text-xs border border-[#ffcc34] focus:outline-none focus:ring-2 focus:ring-[#00553d] w-full sm:w-24">
                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>
                                            10 per page </option>
                                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20
                                            per page </option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50
                                            per page </option>
                                    </select>
                                    <button type="submit"
                                        class="gradient-btn px-6 py-3 text-white font-semibold rounded-lg text-xs border border-[#ffcc34] shadow-md hover:shadow-lg flex items-center">
                                        <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                        <span class="btn-text"><i class="fas fa-filter mr-2"></i>Filter</span>
                                    </button>
                                </form>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xs font-bold text-[#00553d] flex items-center">
                                        <div
                                            class="p-1.5 bg-gradient-to-br from-[#00553d] to-[#007a52] rounded-md mr-2">
                                            <i class="fas fa-list text-white text-xs"></i>
                                        </div>
                                        Current Departments
                                    </h3>
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="text-[0.6rem] text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full font-medium">
                                            Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }}
                                            of {{ $total_departments ?? 'N/A' }} results
                                        </span>
                                        <span
                                            class="text-[0.6rem] text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full font-medium">
                                            Page {{ $departments->currentPage() }} of {{ $departments->lastPage() }}
                                        </span>
                                    </div>
                                </div>
                                @if ($departments->isEmpty())
                                    <div
                                        class="text-center py-12 bg-gradient-to-br from-gray-50 to-white rounded-lg border-2 border-dashed border-gray-300 relative overflow-hidden">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-br from-blue-50/30 to-purple-50/30">
                                        </div>
                                        <div class="relative z-10">
                                            <div
                                                class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-building text-2xl text-gray-400"></i>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2 font-medium">No departments found</p>
                                            <p class="text-[0.6rem] text-gray-400">Create your first department using
                                                the form above</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="grid gap-3 overflow-visible"> <!-- Ensure no overflow restriction -->
                                        @foreach ($departments as $department)
                                            <div
                                                class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 hover:border-[#ffcc34] transition-all duration-300">
                                                <div class="flex items-center space-x-3">
                                                    <div
                                                        class="w-10 h-10 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md flex items-center justify-center shadow-md">
                                                        <i class="fas fa-building text-white text-xs"></i>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="text-xs font-semibold text-[#00553d] block department-name">{{ $department->name }}</span>
                                                        <div class="flex items-center space-x-2 mt-1">
                                                            <span class="status-indicator status-active"></span>
                                                            <span class="text-[0.6rem] text-gray-500">Active
                                                                Department</span>
                                                            <span
                                                                class="text-[0.6rem] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full">ID:
                                                                {{ $department->id }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <button type="button"
                                                        class="details-department-btn text-[#007a52] hover:text-[#00553d] px-3 py-2 rounded-md hover:bg-green-50 transition-all duration-200 text-[0.6rem] font-semibold border border-green-200 hover:border-green-300"
                                                        data-id="{{ $department->id }}"
                                                        data-name="{{ $department->name }}"
                                                        data-created="{{ $department->created_at ? $department->created_at->format('M d, Y') : 'Unknown' }}"
                                                        data-updated="{{ $department->updated_at ? $department->updated_at->format('M d, Y') : 'Unknown' }}">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        Details
                                                    </button>
                                                    <button type="button"
                                                        class="edit-department-btn text-[#00553d] hover:text-[#007a52] px-3 py-2 rounded-md hover:bg-blue-50 transition-all duration-200 text-[0.6rem] font-semibold border border-blue-200 hover:border-blue-300"
                                                        data-id="{{ $department->id }}"
                                                        data-name="{{ $department->name }}">
                                                        <i class="fas fa-edit mr-1"></i>
                                                        Edit
                                                    </button>
                                                    <form
                                                        action="{{ route('settings.department.destroy', $department) }}"
                                                        method="POST" class="inline delete-department-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="delete-department-btn text-[#90143c] hover:text-[#b01a47] px-3 py-2 rounded-md hover:bg-red-50 transition-all duration-200 text-[0.6rem] font-semibold border border-red-200 hover:border-red-300"
                                                            data-name="{{ $department->name }}">
                                                            <i class="fas fa-trash mr-1"></i>
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="pagination-container mt-4">
                                        {{ $departments->appends(request()->query())->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Configuration moved below Department Management -->
                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden setting-card border border-[#ffcc34] slide-up">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center p-4 gradient-btn text-white transition-all duration-500"
                        data-target="system-settings">
                        <div class="flex items-center space-x-3">
                            <div class="p-1.5 bg-white/20 rounded-md">
                                <i class="fas fa-sliders-h text-base"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-semibold block">System Configuration</span>
                                <span class="text-xs opacity-80">Core system settings and preferences</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="status-indicator status-active"></span>
                            <svg class="accordion-icon w-4 h-4 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="system-settings" class="accordion-content">
                        <form id="settings-form" action="{{ route('settings.update') }}" method="POST"
                            class="space-y-6 p-4">
                            @csrf
                            @method('PATCH')
                            <div class="setting-item">
                                <label for="system_title"
                                    class="flex items-center space-x-2 text-xs font-semibold text-[#00553d] mb-2">
                                    <div class="p-1.5 bg-gradient-to-br from-[#90143c] to-[#b01a47] rounded-md">
                                        <i class="fas fa-heading text-white text-xs"></i>
                                    </div>
                                    <div>
                                        <span>System Title</span>
                                        <span class="text-red-500">*</span>
                                        <div class="text-[0.6rem] font-normal text-gray-500 mt-1">The main title
                                            displayed across your application</div>
                                    </div>
                                </label>
                                <div class="relative group">
                                    <input type="text" name="system_title" id="system_title"
                                        value="{{ old('system_title', $settings['system_title'] ?? 'UPITDC - Inventory System') }}"
                                        readonly
                                        class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg bg-gradient-to-r from-gray-50 to-gray-100 text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent cursor-not-allowed transition-all duration-300 group-hover:shadow-md @error('system_title') border-red-500 @enderror">
                                    <div
                                        class="absolute right-3 top-3 text-gray-400 group-hover:text-gray-600 transition-colors">
                                        <i class="fas fa-lock text-xs"></i>
                                    </div>
                                </div>
                                <div
                                    class="flex items-start space-x-2 mt-2 p-2 bg-blue-50 rounded-lg border border-blue-200">
                                    <i class="fas fa-info-circle text-blue-500 mt-0.5 text-xs"></i>
                                    <div class="text-[0.6rem] text-blue-700">
                                        <strong>Protected Setting:</strong>
                                        {{ $settingsDetails['system_title']->description ?? 'The title displayed in the application header and browser tab' }}
                                    </div>
                                </div>
                                @error('system_title')
                                    <p class="text-[0.6rem] text-red-500 mt-2 flex items-center"><i
                                            class="fas fa-exclamation-triangle mr-1 text-xs"></i>{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="setting-item">
                                <label for="default_return_period"
                                    class="flex items-center space-x-2 text-xs font-semibold text-[#00553d] mb-2">
                                    <div class="p-1.5 bg-gradient-to-br from-[#00553d] to-[#007a52] rounded-md">
                                        <i class="fas fa-calendar-alt text-white text-xs"></i>
                                    </div>
                                    <div>
                                        <span>Default Return Period</span>
                                        <span class="text-red-500">*</span>
                                        <div class="text-[0.6rem] font-normal text-gray-500 mt-1">Standard timeframe for
                                            equipment returns</div>
                                    </div>
                                </label>
                                <div class="relative group">
                                    <input type="number" name="default_return_period" id="default_return_period"
                                        value="{{ old('default_return_period', $settings['default_return_period'] ?? 30) }}"
                                        min="1" max="365" required
                                        class="w-full px-3 py-3 border border-[#ffcc34] rounded-lg text-xs focus:ring-2 focus:ring-[#00553d] focus:border-transparent  cursor-not-allowed transition-all duration-300 group-hover:shadow-md @error('default_return_period') border-red-500 @enderror"
                                        placeholder="Enter days (1-365)">
                                    <div
                                        class="absolute right-3 top-3 text-gray-400 group-hover:text-[#00553d] transition-colors">
                                        <i class="fas fa-hashtag text-xs"></i>
                                    </div>
                                    <div
                                        class="absolute -top-2 right-2 bg-[#00553d] text-white text-[0.6rem] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                        days
                                    </div>
                                </div>
                                <div
                                    class="flex items-start space-x-2 mt-2 p-2 bg-green-50 rounded-lg border border-green-200">
                                    <i class="fas fa-lightbulb text-green-500 mt-0.5 text-xs"></i>
                                    <div class="text-[0.6rem] text-green-700">
                                        <strong>Recommendation:</strong>
                                        {{ $settingsDetails['default_return_period']->description ?? 'Set between 7-365 days based on your equipment usage patterns' }}
                                    </div>
                                </div>
                                @error('default_return_period')
                                    <p class="text-[0.6rem] text-red-500 mt-2 flex items-center"><i
                                            class="fas fa-exclamation-triangle mr-1 text-xs"></i>{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="setting-item">
                                <label class="flex items-center space-x-2 text-xs font-semibold text-[#00553d] mb-2">
                                    <div class="p-1.5 bg-gradient-to-br from-[#b01a47] to-[#d4204a] rounded-md">
                                        <i class="fas fa-copy text-white text-xs"></i>
                                    </div>
                                    <div>
                                        <span>Duplicate PR Numbers</span>
                                        <div class="text-[0.6rem] font-normal text-gray-500 mt-1">Control PR number
                                            uniqueness validation</div>
                                    </div>
                                </label>
                                <div
                                    class="flex items-center space-x-3 p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-[#ffcc34] opacity-70 cursor-not-allowed hover:shadow-none transition-all duration-300">
                                    <div class="relative">
                                        <input type="checkbox" name="allow_duplicate_pr" id="allow_duplicate_pr"
                                            value="1" disabled
                                            {{ old('allow_duplicate_pr', $settings['allow_duplicate_pr'] ?? 0) == 1 ? 'checked' : '' }}
                                            class="h-4 w-4 text-[#00553d] focus:ring-[#00553d] border-[#ffcc34] rounded transition-colors cursor-not-allowed">
                                    </div>
                                    <label for="allow_duplicate_pr"
                                        class="text-xs text-[#00553d] flex-1 cursor-not-allowed select-none">
                                        <span class="font-medium">Allow duplicate PR numbers in equipment
                                            records</span>
                                        <div class="text-[0.6rem] text-gray-600 mt-1">Enable this if your organization
                                            uses non-unique PR numbering systems</div>
                                    </label>
                                    <div class="text-right">
                                        <span
                                            class="text-[0.6rem] px-1.5 py-0.5 rounded-full {{ old('allow_duplicate_pr', $settings['allow_duplicate_pr'] ?? 0) == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ old('allow_duplicate_pr', $settings['allow_duplicate_pr'] ?? 0) == 1 ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="flex items-start space-x-2 mt-2 p-2 bg-amber-50 rounded-lg border border-amber-200">
                                    <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 text-xs"></i>
                                    <div class="text-[0.6rem] text-amber-700">
                                        <strong>Warning:</strong>
                                        {{ $settingsDetails['allow_duplicate_pr']->description ?? 'Enabling this may cause data integrity issues. Use with caution.' }}
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-gray-200 opacity-70 cursor-not-allowed">
                                <button type="button" id="save-settings-btn" disabled
                                    class="w-full gradient-btn text-white font-semibold py-3 px-6 rounded-lg text-xs border border-[#ffcc34] shadow-md flex items-center justify-center transition-all duration-300">
                                    <i class="spinner fas fa-spinner fa-spin mr-2"></i>
                                    <span class="btn-text flex items-center">
                                        <i class="fas fa-save mr-2"></i>
                                        Save Configuration Changes
                                    </span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="edit-department-form" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="department_name" id="edit-department-name">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const isSettingsUpdate = "{{ session('success') }}".includes('Settings') || "{{ session('success') }}"
                    .includes('configuration');
                if (isSettingsUpdate) {
                    Swal.fire({
                        title: '🎉 Configuration Updated!',
                        html: `
                            <div class="text-left space-y-3">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span>{{ session('success') }}</span>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg border border-green-200 mt-3">
                                    <div class="text-xs text-green-700">
                                        <strong>What's Next?</strong>
                                        <ul class="mt-2 space-y-1 text-[0.6rem]">
                                            <li>• Changes are now active across the system</li>
                                            <li>• All users will see the updated configuration</li>
                                            <li>• Consider notifying team members about changes</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `,
                        icon: 'success',
                        timer: 5000,
                        showConfirmButton: true,
                        confirmButtonText: 'Got it!',
                        confirmButtonColor: '#00553d',
                        showCloseButton: true,
                        allowOutsideClick: false,
                    });
                } else {
                    Swal.fire({
                        title: 'Success!',
                        text: '<?php echo session('success'); ?>',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Oops! Something went wrong',
                    html: `
                        <div class="text-left">
                            <div class="flex items-center space-x-2 mb-3">
                                <i class="fas fa-exclamation-triangle text-red-500"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                            <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                <div class="text-xs text-red-700">
                                    <strong>Troubleshooting Tips:</strong>
                                    <ul class="mt-2 space-y-1 text-[0.6rem]">
                                        <li>• Check if all required fields are filled</li>
                                        <li>• Ensure values are within acceptable ranges</li>
                                        <li>• Try refreshing the page and attempt again</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `,
                    icon: 'error',
                    confirmButtonColor: '#90143c',
                    confirmButtonText: 'Try Again'
                });
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accordionToggles = document.querySelectorAll('.accordion-toggle');
            accordionToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const target = document.getElementById(toggle.dataset.target);
                    const icon = toggle.querySelector('.accordion-icon');
                    target.classList.toggle('open');
                    icon.classList.toggle('rotate-180');
                    if (target.classList.contains('open')) {
                        setTimeout(() => {
                            target.style.animation = 'fadeIn 0.3s ease-out';
                        }, 100);
                    }
                });
            });
            const settingsForm = document.getElementById('settings-form');
            const saveSettingsBtn = document.getElementById('save-settings-btn');
            if (settingsForm) {
                settingsForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const returnPeriod = document.getElementById('default_return_period').value;
                    const allowDuplicate = document.getElementById('allow_duplicate_pr').checked;
                    if (!returnPeriod || returnPeriod < 1 || returnPeriod > 365) {
                        showAlert('Return period must be between 1 and 365 days', 'error');
                        return;
                    }
                    Swal.fire({
                        title: '⚙️ Save System Configuration?',
                        html: `
                            <div class="text-left space-y-3">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                    <h4 class="font-semibold text-blue-800 mb-2">Configuration Summary:</h4>
                                    <div class="space-y-2 text-xs text-blue-700">
                                        <div class="flex justify-between">
                                            <span>Return Period:</span>
                                            <span class="font-medium">${returnPeriod} days</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Duplicate PR:</span>
                                            <span class="font-medium ${allowDuplicate ? 'text-green-600' : 'text-red-600'}">
                                                ${allowDuplicate ? 'Allowed' : 'Restricted'}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-amber-50 p-2 rounded-lg border border-amber-200">
                                    <div class="flex items-start space-x-2">
                                        <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
                                        <div class="text-xs text-amber-700">
                                            <strong>Impact:</strong> These changes will affect all future operations and may require user notification.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#00553d',
                        cancelButtonColor: '#90143c',
                        confirmButtonText: '<i class="fas fa-save mr-2"></i>Save Changes',
                        cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                        reverseButtons: true,
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            setLoadingState(saveSettingsBtn, true);
                            Swal.fire({
                                title: 'Processing...',
                                html: 'Saving your configuration changes...',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            settingsForm.submit();
                        }
                    });
                });
            }
            const departmentForm = document.getElementById('department-form');
            const addDepartmentBtn = document.getElementById('add-department-btn');
            if (departmentForm) {
                departmentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const departmentName = document.getElementById('department_name').value.trim();
                    if (!departmentName) {
                        showAlert('Please enter a department name', 'error');
                        return;
                    }
                    Swal.fire({
                        title: '🏢 Add New Department?',
                        html: `
                            <div class="text-left space-y-3">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                    <div class="font-semibold text-blue-800 mb-2">Department Details:</div>
                                    <div class="text-xs text-blue-700">
                                        <div class="flex justify-between items-center">
                                            <span>Name:</span>
                                            <span class="font-medium">"${departmentName}"</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span>Status:</span>
                                            <span class="bg-green-100 text-green-700 px-1.5 py-0.5 rounded-full text-[0.6rem]">Active</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[0.6rem] text-gray-600">
                                    This department will be available for equipment assignment immediately.
                                </div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#00553d',
                        cancelButtonColor: '#90143c',
                        confirmButtonText: '<i class="fas fa-plus mr-2"></i>Create Department',
                        cancelButtonText: 'Cancel'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            setLoadingState(addDepartmentBtn, true);
                            departmentForm.submit();
                        }
                    });
                });
            }
            const detailsButtons = document.querySelectorAll('.details-department-btn');
            detailsButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const departmentId = btn.dataset.id;
                    const departmentName = btn.dataset.name;
                    const createdDate = btn.dataset.created;
                    const updatedDate = btn.dataset.updated;
                    Swal.fire({
                        title: `📋 Department Details`,
                        html: `
                            <div class="text-left space-y-3">
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-3 rounded-lg border border-blue-200">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-md flex items-center justify-center">
                                            <i class="fas fa-building text-white text-xs"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-800 text-xs">${departmentName}</h3>
                                            <span class="text-[0.6rem] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full">ID: ${departmentId}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-green-50 p-2 rounded-lg border border-green-200">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <i class="fas fa-calendar-plus text-green-600 text-xs"></i>
                                            <span class="text-xs font-semibold text-green-800">Created</span>
                                        </div>
                                        <div class="text-[0.6rem] text-green-700">${createdDate}</div>
                                    </div>
                                    <div class="bg-blue-50 p-2 rounded-lg border border-blue-200">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <i class="fas fa-edit text-blue-600 text-xs"></i>
                                            <span class="text-xs font-semibold text-blue-800">Last Updated</span>
                                        </div>
                                        <div class="text-[0.6rem] text-blue-700">${updatedDate}</div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-2 rounded-lg border border-gray-200">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <i class="fas fa-info-circle text-gray-600 text-xs"></i>
                                        <span class="text-xs font-semibold text-gray-800">Status Information</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                        <span class="text-[0.6rem] text-gray-700">Active & Available for Assignment</span>
                                    </div>
                                </div>
                                <div class="bg-amber-50 p-2 rounded-lg border border-amber-200">
                                    <div class="flex items-start space-x-2">
                                        <i class="fas fa-lightbulb text-amber-600 mt-0.5 text-xs"></i>
                                        <div class="text-[0.6rem] text-amber-700">
                                            <strong>Quick Actions:</strong>
                                            <div class="mt-1 text-[0.6rem]">
                                                • Use "Edit" to modify department name<br>
                                                • Use "Delete" to remove (if no equipment assigned)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `,
                        width: '450px',
                        showCloseButton: true,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-edit mr-2"></i>Edit Department',
                        cancelButtonText: '<i class="fas fa-times mr-2"></i>Close',
                        confirmButtonColor: '#00553d',
                        cancelButtonColor: '#6b7280',
                        reverseButtons: true
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            const editBtn = document.querySelector(
                                `.edit-department-btn[data-id="${departmentId}"]`);
                            if (editBtn) editBtn.click();
                        }
                    });
                });
            });
            const editButtons = document.querySelectorAll('.edit-department-btn');
            editButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const departmentId = btn.dataset.id;
                    const departmentName = btn.dataset.name;
                    Swal.fire({
                        title: '✏️ Edit Department',
                        html: `
                            <div class="text-left space-y-3">
                                <div class="bg-blue-50 p-2 rounded-lg border border-blue-200">
                                    <div class="text-xs text-blue-700">
                                        <strong>Current Name:</strong> "${departmentName}"
                                    </div>
                                </div>
                            </div>
                        `,
                        input: 'text',
                        inputValue: departmentName,
                        inputPlaceholder: 'Enter new department name',
                        showCancelButton: true,
                        confirmButtonColor: '#00553d',
                        cancelButtonColor: '#90143c',
                        confirmButtonText: '<i class="fas fa-save mr-2"></i>Save Changes',
                        cancelButtonText: 'Cancel',
                        inputValidator: function(value) {
                            if (!value || !value.trim()) {
                                return 'Department name is required!';
                            }
                            if (value.trim().length > 50) {
                                return 'Department name is too long (max 50 characters)!';
                            }
                            if (value.trim() === departmentName) {
                                return 'Please enter a different name to make changes!';
                            }
                        }
                    }).then(function(result) {
                        if (result.value && result.value.trim() !== departmentName) {
                            const editForm = document.getElementById(
                                'edit-department-form');
                            const nameInput = document.getElementById(
                                'edit-department-name');
                            editForm.action = '/settings/department/' + departmentId;
                            nameInput.value = result.value.trim();
                            editForm.submit();
                        }
                    });
                });
            });
            const deleteButtons = document.querySelectorAll('.delete-department-btn');
            deleteButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const form = btn.closest('form');
                    const departmentName = btn.dataset.name;
                    Swal.fire({
                        title: '🗑️ Delete Department?',
                        html: `
                            <div class="text-left space-y-3">
                                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <i class="fas fa-exclamation-triangle text-red-500 text-base"></i>
                                        <span class="font-semibold text-red-800 text-xs">Permanent Deletion Warning</span>
                                    </div>
                                    <div class="text-xs text-red-700">
                                        You are about to permanently delete "<strong>${departmentName}</strong>" department.
                                    </div>
                                </div>
                                <div class="bg-amber-50 p-2 rounded-lg border border-amber-200">
                                    <div class="text-xs text-amber-700">
                                        <strong>⚠️ Impact:</strong>
                                        <ul class="mt-1 space-y-1 text-[0.6rem]">
                                            <li>• Equipment linked to this department will have no department assigned</li>
                                            <li>• Historical records will show "Department Deleted"</li>
                                            <li>• This action cannot be undone</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="text-center text-xs text-gray-600">
                                    Type "<strong>DELETE</strong>" below to confirm:
                                </div>
                            </div>
                        `,
                        input: 'text',
                        placeholder: 'Type DELETE to confirm',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Delete Forever',
                        cancelButtonText: '<i class="fas fa-shield-alt mr-2"></i>Keep Safe',
                        reverseButtons: true,
                        inputValidator: function(value) {
                            if (value !== 'DELETE') {
                                return 'Please type "DELETE" exactly to confirm';
                            }
                        }
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            function setLoadingState(button, loading) {
                if (loading) {
                    button.classList.add('btn-loading');
                    button.disabled = true;
                } else {
                    button.classList.remove('btn-loading');
                    button.disabled = false;
                }
            }

            function showAlert(message, type) {
                const alertContainer = document.getElementById('alert-container');
                const alertClass = type === 'error' ? 'bg-red-100 border-red-400 text-red-700' :
                    'bg-blue-100 border-blue-400 text-blue-700';
                const iconClass = type === 'error' ? 'fas fa-exclamation-triangle' : 'fas fa-info-circle';
                alertContainer.innerHTML = '<div class="alert ' + alertClass +
                    ' border px-3 py-2 rounded-lg flex items-center space-x-2 animate-pulse">' +
                    '<i class="' + iconClass + '"></i>' +
                    '<span class="text-xs">' + message + '</span>' +
                    '<button onclick="this.parentElement.remove()" class="ml-auto hover:scale-110 transition-transform">' +
                    '<i class="fas fa-times hover:text-gray-800"></i>' +
                    '</button>' +
                    '</div>';
                setTimeout(() => {
                    const alert = alertContainer.querySelector('.alert');
                    if (alert) {
                        alert.style.animation = 'fadeOut 0.3s ease-out forwards';
                        setTimeout(() => alert.remove(), 300);
                    }
                }, 4000);
            }
        });
    </script>

    <x-auth-footer />
</x-app-layout>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Setting::where('key', 'system_title')->value('value') ?? 'UPITDC - Inventory System' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/inventory.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Fixed Header -->
        <header class="fixed top-0 left-0 right-0 bg-white shadow-sm z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div class="flex items-center">
                    <img src="{{ asset('images/my-logo.png') }}" alt="Company Logo" class="h-10 mr-3" onerror="this.src='/images/fallback-logo.png'">
                    <h1 class="text-xl font-semibold text-red-600">{{ \App\Models\Setting::where('key', 'system_title')->value('value') ?? 'UPITDC - Inventory System' }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Navigation Buttons -->
                    <nav class="flex space-x-2">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('dashboard') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                        </a>
                        <a href="{{ route('inventory') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('inventory') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                            <i class="fas fa-boxes mr-1"></i> Inventory
                        </a>
                        <a href="{{ route('staff') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('staff') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                            <i class="fas fa-users mr-1"></i> Staff
                        </a>
                        <a href="{{ route('history') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('history') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                            <i class="fas fa-history mr-1"></i> History
                        </a>
                        <a href="{{ route('settings') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('settings') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                            <i class="fas fa-cog mr-1"></i> Settings
                        </a>
                    </nav>
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="userDropdownToggle" class="flex items-center text-sm font-medium text-black hover:text-gray-500 focus:outline-none" aria-label="User menu">
                            <span>{{ auth()->user()->name }}</span>
                            <i class="fas fa-user-circle ml-2 text-lg"></i>
                        </button>
                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-black hover:bg-gray-100" aria-label="View Profile">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" aria-label="Logout">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="pt-20">
            <main>
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        // Toggle user dropdown
        document.getElementById('userDropdownToggle').addEventListener('click', function() {
            document.getElementById('userDropdown').classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const toggle = document.getElementById('userDropdownToggle');
            if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
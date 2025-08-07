<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Settings::where('key', 'system_title')->value('value') ?? 'UPITDC - Inventory System' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/inventory.js'])
</head>
<body class="font-inter text-sm antialiased bg-gray-50 overflow-x-hidden">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-white shadow-sm z-50 border-b-2 border-[#ffcc34]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/ITDC.png') }}" alt="Company Logo" class="h-8" onerror="this.src='/images/ITDC.png'">
                <h1 class="text-base font-semibold text-gray-900">
                    {{ \App\Models\Settings::where('key', 'system_title')->value('value') ?? 'UPITDC - Inventory System' }}
                </h1>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                <!-- Navigation -->
                <nav class="hidden md:flex space-x-1">
                    <a href="{{ route('dashboard') }}" class="px-3 py-1.5 text-xs font-medium {{ Route::is('dashboard') ? 'text-white bg-[#90143c]' : 'text-gray-700 hover:bg-[#6b102c] hover:text-white' }} rounded-md transition duration-200 flex items-center">
                        <i class="fas fa-tachometer-alt mr-1.5"></i> Dashboard
                    </a>
                    <a href="{{ route('inventory') }}" class="px-3 py-1.5 text-xs font-medium {{ Route::is('inventory') ? 'text-white bg-[#90143c]' : 'text-gray-700 hover:bg-[#6b102c] hover:text-white' }} rounded-md transition duration-200 flex items-center">
                        <i class="fas fa-boxes mr-1.5"></i> Inventory
                    </a>
                    <a href="{{ route('staff.index') }}" class="px-3 py-1.5 text-xs font-medium {{ Route::is('staff*') ? 'text-white bg-[#90143c]' : 'text-gray-700 hover:bg-[#6b102c] hover:text-white' }} rounded-md transition duration-200 flex items-center">
                        <i class="fas fa-users mr-1.5"></i> Staff
                    </a>
                    <a href="{{ route('history') }}" class="px-3 py-1.5 text-xs font-medium {{ Route::is('history') ? 'text-white bg-[#90143c]' : 'text-gray-700 hover:bg-[#6b102c] hover:text-white' }} rounded-md transition duration-200 flex items-center">
                        <i class="fas fa-history mr-1.5"></i> History
                    </a>
                    <a href="{{ route('settings') }}" class="px-3 py-1.5 text-xs font-medium {{ Route::is('settings') ? 'text-white bg-[#90143c]' : 'text-gray-700 hover:bg-[#6b102c] hover:text-white' }} rounded-md transition duration-200 flex items-center">
                        <i class="fas fa-cog mr-1.5"></i> Settings
                    </a>
                </nav>

                <!-- Mobile Menu Button -->
                <button id="mobileMenuButton" class="md:hidden p-2 rounded-md text-gray-700 hover:bg-[#6b102c] hover:text-white focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
                @endauth

                <!-- Admin Dropdown -->
                <div class="relative">
                    <button id="userDropdownToggle" class="flex items-center text-xs font-medium text-gray-700 hover:text-[#90143c] focus:outline-none" aria-label="User menu">
                        @auth
                        {{ Auth::user()->name }}
                        @else
                        Admin
                        @endauth
                        <i class="fas fa-user-circle ml-1.5 text-base"></i>
                    </button>
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-44 bg-white border border-[#00553d] rounded-md shadow-lg z-50">
                        @auth
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-[#6b102c] hover:text-white" aria-label="View Profile">
                            <i class="fas fa-user mr-1.5"></i> Profile
                        </a>
                        <a href="{{ route('logout') }}" class="block px-3 py-2 text-xs text-red-600 hover:bg-[#6b102c] hover:text-white" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" aria-label="Logout">
                            <i class="fas fa-sign-out-alt mr-1.5"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                        @else
                        <a href="{{ route('login') }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-[#6b102c] hover:text-white" aria-label="Login">
                            <i class="fas fa-sign-in-alt mr-1.5"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-[#6b102c] hover:text-white" aria-label="Register">
                            <i class="fas fa-user-plus mr-1.5"></i> Register
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        @auth
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-[#00553d]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                <div class="flex flex-col space-y-1">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 text-xs font-medium {{ Route::is('dashboard') ? 'text-white bg-[#90143c]' : 'text-gray-700 hover:bg-[#6b102c] hover:text-white' }} rounded-md transition duration-200 flex items-center">
                        <i class="fas fa-tachometer-alt mr-1.5"></i> Dashboard
                    </a>
                    <a href="{{ route('inventory') }}" class="px-3 py-2 text-xs font-medium {{ Route::is('inventory') ? 'text-white bg-[#90143c]' : 'text-gray-700 hover:bg-[#6b102c] hover:text-white' }} rounded-md transition duration-200 flex items-center">
                        <i class="fas fa-boxes mr-1.5"></i> Inventory
                    </a>
                    <a href="{{ route('staff.index') }}" class="px-3 py-2 text-xs font-medium {{ Route::is('staff*') ? 'text-white bg-[#90143c]' : 'text-gray-700 hover:bg-[#6b102c] hover:text-white' }} rounded-md transition duration-200 flex items-center">
                        <i class="fas fa-users mr-1.5"></i> Staff
                    </a>
                    <a href="{{ route('history') }}" class="px-3 py-2 text-xs font-medium {{ Route::is('history') ? 'text-white bg-[#90143c]' : 'text-gray-700 hover:bg-[#6b102c] hover:text-white' }} rounded-md transition duration-200 flex items-center">
                        <i class="fas fa-history mr-1.5"></i> History
                    </a>
                    <a href="{{ route('settings') }}" class="px-3 py-2 text-xs font-medium {{ Route::is('settings') ? 'text-white bg-[#90143c]' : 'text-gray-700 hover:bg-[#6b102c] hover:text-white' }} rounded-md transition duration-200 flex items-center">
                        <i class="fas fa-cog mr-1.5"></i> Settings
                    </a>
                </div>
            </div>
        </div>
        @endauth
    </header>

    <!-- Main Content -->
    <main class="pt-16 pb-16 min-h-screen">
        {{ $slot }}
        </div>
        </div>
    </main>

    <script>
        // Toggle user dropdown
        document.getElementById('userDropdownToggle')?.addEventListener('click', function() {
            document.getElementById('userDropdown').classList.toggle('hidden');
        });

        // Toggle mobile menu
        document.getElementById('mobileMenuButton')?.addEventListener('click', function() {
            document.getElementById('mobileMenu').classList.toggle('hidden');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const toggle = document.getElementById('userDropdownToggle');
            if (dropdown && toggle && !dropdown.contains(event.target) && !toggle.contains(event.target)) {
                dropdown.classList.add('hidden');
            }

            const mobileMenu = document.getElementById('mobileMenu');
            const mobileButton = document.getElementById('mobileMenuButton');
            if (mobileMenu && mobileButton && !mobileMenu.contains(event.target) && !mobileButton.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
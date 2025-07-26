<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Settings::where('key', 'system_title')->value('value') ?? 'UPITDC - Inventory System' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/inventory.js'])
</head>

<body class="font-sans antialiased bg-gray-100 min-h-screen">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-white shadow-sm z-50 border-b-4 border-[#ffcc34]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <img src="{{ asset('images/ITDC.png') }}" alt="Company Logo" class="h-10 mr-3" onerror="this.src='/images/ITDC.png'">
                <h1 class="text-lg font-semibold text-black-600">{{ \App\Models\Settings::where('key', 'system_title')->value('value') ?? 'UPITDC - Inventory System' }}</h1>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                <!-- Navigation Buttons -->
                <nav class="hidden md:flex space-x-2">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('dashboard') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a href="{{ route('inventory') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('inventory') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                        <i class="fas fa-boxes mr-1"></i> Inventory
                    </a>
                    <a href="{{ route('staff.index') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('staff*') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                        <i class="fas fa-users mr-1"></i> Staff
                    </a>
                    <a href="{{ route('history') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('history') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                        <i class="fas fa-history mr-1"></i> History
                    </a>
                    <a href="{{ route('settings') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('settings') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                        <i class="fas fa-cog mr-1"></i> Settings
                    </a>
                </nav>

                <!-- Mobile menu button -->
                <button id="mobileMenuButton" class="md:hidden p-2 rounded-md text-black hover:bg-gray-100 focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
                @endauth

                <!-- User Dropdown -->
                <div class="relative">
                    <button id="userDropdownToggle" class="flex items-center text-sm font-medium text-black hover:text-gray-500 focus:outline-none" aria-label="User menu">
                        @auth
                        {{ Auth::user()->name }}
                        @else
                        User
                        @endauth
                        <i class="fas fa-user-circle ml-2 text-lg"></i>
                    </button>
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                        @auth
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-black hover:bg-gray-100" aria-label="View Profile">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" aria-label="Logout">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                        @else
                        <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-black hover:bg-gray-100" aria-label="Login">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="block px-4 py-2 text-sm text-black hover:bg-gray-100" aria-label="Register">
                            <i class="fas fa-user-plus mr-2"></i> Register
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (hidden by default) -->
        @auth
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                <div class="flex flex-col space-y-1">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('dashboard') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a href="{{ route('inventory') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('inventory') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                        <i class="fas fa-boxes mr-1"></i> Inventory
                    </a>
                    <a href="{{ route('staff.index') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('staff') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                        <i class="fas fa-users mr-1"></i> Staff
                    </a>
                    <a href="{{ route('history') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('history') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                        <i class="fas fa-history mr-1"></i> History
                    </a>
                    <a href="{{ route('settings') }}" class="px-3 py-2 text-sm font-medium {{ Route::is('settings') ? 'text-gray-500 bg-gray-100' : 'text-black hover:bg-gray-100' }} rounded-md transition duration-200">
                        <i class="fas fa-cog mr-1"></i> Settings
                    </a>
                </div>
            </div>
        </div>
        @endauth
    </header>

    <!-- Main Content -->
    <main class="pt-20 pb-8 min-h-screen"> <!-- Added min-h-screen to ensure full height -->
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
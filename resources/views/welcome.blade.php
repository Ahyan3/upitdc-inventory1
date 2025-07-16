<x-app-layout>
    <div class="fixed inset-0 -z-10">
        <img src="{{ asset('/images/upd-oblation.jpg') }}" alt="UP Diliman Campus"
             class="w-full h-full object-cover opacity-60">
    </div>

    <!-- Main Container -->
    <div class="relative flex flex-col min-h-screen">
        <!-- Header space (from app-layout) will appear here automatically -->
        
        <!-- Centered Content -->
        <main class="flex-grow flex items-center justify-center px-4">
            <div class="bg-white bg-opacity-80 p-8 rounded-xl shadow-md text-center animate-fade-in max-w-md w-full border-b-4 border-[#00553d] [#ffcc34] &">
                @auth
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Welcome Back!</h2>
                    <p class="text-base text-gray-600 mb-6">Manage your equipment and issuances</p>
                    <a href="{{ route('dashboard') }}"
                       class="bg-green-600 hover:bg-green-700 text-white text-sm py-2 px-6 rounded-lg transition duration-200">
                        Get Started
                    </a>
                @else
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Welcome to UPITDC - Inventory System</h2>
                    <p class="text-base text-gray-600 mb-6">Manage equipment and issuances with ease.</p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('login') }}"
                           class="bg-gray-600 hover:bg-gray-900 text-white text-sm py-2 px-6 rounded-lg transition duration-200">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="bg-gray-600 hover:bg-gray-900 text-white text-sm py-2 px-6 rounded-lg transition duration-200">
                            Register
                        </a>
                    </div>
                @endauth
            </div>
        </main>

        <!-- Footer -->
        <footer style="background-color: #90143c;" class="text-white py-4 w-full">
            <div class="container mx-auto px-4 text-center">
                <p class="text-xs">
                    Copyright © {{ date('Y') }}
                    UP Diliman ITDC - Inventory System. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</x-app-layout>
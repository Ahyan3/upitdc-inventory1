<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Welcome to UP Diliman Inventory System') }}
        </h2>
    </x-slot>

    <div class="flex min-h-screen bg-gray-50 flex-col relative">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="{{ asset('/images/upd-oblation.jpg') }}" alt="UP Diliman Campus" class="w-full h-full object-cover opacity-60">
        </div>

        <!-- Main Content -->
        <div class="flex-1 container mx-auto px-4 py-8 flex items-center justify-center relative z-10">
            <div class="text-center animate-fade-in bg-white bg-opacity-80 p-8 rounded-xl shadow-md">
                @auth
                    <!-- Content for logged-in users -->
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Welcome Back!</h2>
                    <p class="text-lg text-gray-600 mb-6">Ready to manage your equipment and issuances?</p>
                    <div class="flex justify-center">
                        <a href="{{ route('dashboard') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200" aria-label="Get started with the system">
                            Get Started
                        </a>
                    </div>
                @else
                    <!-- Content for guests -->
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Welcome</h2>
                    <p class="text-lg text-gray-600 mb-6">Manage equipment and issuances with ease.</p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('login') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200" aria-label="Login to the system">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-6 rounded-lg transition duration-200" aria-label="Register for a new account">
                            Register
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-4 relative z-10">
            <div class="container mx-auto px-4 text-center">
                <p class="text-sm">© {{ date('Y') }} UP Diliman ITDC - Inventory System. All rights reserved.</p>
            </div>
        </footer>
    </div>
</x-app-layout>
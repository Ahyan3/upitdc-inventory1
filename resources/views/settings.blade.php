<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="flex min-h-screen bg-gray-50">

        <button id="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 bg-gray-800 text-white p-2 rounded-lg">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Main Content -->
        <div class="flex-1 container mx-auto px-4 py-8">
            <div class="text-center mb-10 animate-fade-in">
                <h2 class="text-2xl font-bold text-red-600">Settings</h2>
                <p class="text-lg text-black-600">Configure system settings</p>
            </div>

            <!-- Settings Form -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden form-card">
                <div class="bg-red-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">System Settings</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('settings.update') }}" method="POST" class="space-y-4" aria-label="Settings Form">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="system_title" class="block text-sm font-medium text-gray-700 mb-1">System Title *</label>
                            <input type="text" name="system_title" id="system_title" value="{{ $settings['system_title'] ?? 'UPITDC - Inventory System' }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" aria-label="System Title">
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200" aria-label="Save Settings">
                            Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<div class="text-center mb-10 fade-in">
    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-[#90143c] via-[#b01a47] to-[#d4204a] rounded-full mb-6 shadow-xl relative">
        <i class="fas fa-cog text-white text-2xl animate-spin" style="animation-duration: 8s;"></i>
        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-[#90143c] to-[#b01a47] opacity-20 animate-ping"></div>
    </div>
    <h1 class="text-3xl font-bold text-[#90143c] mb-3 bg-gradient-to-r from-[#90143c] to-[#00553d] bg-clip-text text-transparent">System Settings</h1>
    <p class="text-sm text-[#00553d] opacity-80 max-w-md mx-auto">Configure system settings, manage departments, and customize your inventory management experience</p>
    
    {{-- Settings Overview Stats --}}
    <div class="grid grid-cols-3 gap-4 mt-8 max-w-md mx-auto">
        <div class="bg-white p-3 rounded-lg shadow-sm border border-[#ffcc34]/30">
            <div class="text-lg font-bold text-[#90143c]">{{ $departments->count() }}</div>
            <div class="text-xs text-gray-600">Departments</div>
        </div>
        <div class="bg-white p-3 rounded-lg shadow-sm border border-[#ffcc34]/30">
            <div class="text-lg font-bold text-[#00553d]">{{ $settings['default_return_period'] ?? 30 }}</div>
            <div class="text-xs text-gray-600">Return Days</div>
        </div>
        <div class="bg-white p-3 rounded-lg shadow-sm border border-[#ffcc34]/30">
            <div class="text-lg font-bold text-[#b01a47]">
                <span class="status-indicator status-active"></span>Active
            </div>
            <div class="text-xs text-gray-600">System Status</div>
        </div>
    </div>
</div>
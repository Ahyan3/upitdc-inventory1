<div class="bg-white rounded-xl shadow-lg overflow-hidden setting-card border border-[#ffcc34] slide-up">
    <button class="accordion-toggle w-full flex justify-between items-center p-6 bg-gradient-to-r from-[#90143c] via-[#b01a47] to-[#90143c] text-white hover:from-[#6b102d] hover:via-[#8e1539] hover:to-[#6b102d] transition-all duration-500" data-target="system-settings">
        {{-- Accordion header content --}}
    </button>
    
    <div id="system-settings" class="accordion-content">
        <form id="settings-form" action="{{ route('settings.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PATCH')
            
            {{-- System settings form fields --}}
            
            <div class="pt-6 border-t border-gray-200">
                <button type="submit" id="save-settings-btn" class="w-full bg-gradient-to-r from-[#00553d] via-[#007a52] to-[#00553d] hover:from-[#003d2b] hover:via-[#005a3d] hover:to-[#003d2b] text-white font-semibold py-4 px-8 rounded-xl transition-all duration-300 border-2 border-[#ffcc34] text-sm shadow-lg hover:shadow-2xl transform hover:scale-[1.02] relative overflow-hidden">
                    {{-- Button content --}}
                </button>
            </div>
        </form>
    </div>
</div>
<a href="{{ $route }}" class="bg-white rounded-xl shadow-md p-5 hover:bg-gray-100 hover:scale-105 transition duration-200 block border border-[#ffcc34]">
    <h3 class="text-[0.65rem] font-semibold text-[#00553d]">{{ $name }}</h3>
    <p class="text-base font-bold text-[#00553d]">{{ $value }}</p>
    @if (isset($badge))
        <span class="absolute top-2 right-2 px-2 py-1 text-xs rounded-full {{ $badge }}">{{ $value > 5 ? 'High' : '' }}</span>
    @endif
</a>
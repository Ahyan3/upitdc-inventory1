<div class="relative">
    <input type="text" name="{{ $name }}" id="{{ $name }}" value="{{ $value ?? '' }}" placeholder="{{ $placeholder ?? '' }}"
        class="w-full pl-8 pr-3 py-1 border border-[#ffcc34] rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-[#00553d]"
        {{ $attributes->merge(['required' => isset($required)]) }}>
    <div class="absolute left-2 top-2 text-[#00553d]">
        <i class="fas fa-search text-xs"></i>
    </div>
</div>
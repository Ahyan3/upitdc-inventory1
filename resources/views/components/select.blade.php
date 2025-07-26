<!-- resources/views/components/select.blade.php -->
@props([
    'name' => '',
    'options' => [],
    'selected' => null,
    'required' => false,
    'class' => 'w-full px-2 py-1 text-xs border rounded-md focus:outline-none focus:ring-2 focus:ring-[#ffcc34] border-[#90143c] text-[#00553d]'
])

@php
    // Handle array input for selected
    $selectedValue = is_array($selected) ? (isset($selected[0]) ? $selected[0] : null) : $selected;
@endphp

<select
    {{ $attributes->merge(['name' => $name, 'class' => $class . ($errors->has($name) ? ' border-red-500' : '')]) }}
    {{ $required ? 'required' : '' }}
>
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" {{ $selectedValue == $value ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
</select>

@error($name)
    <p class="mt-1 text-xs text-[#90143c]">{{ $message }}</p>
@enderror
<!-- @extends('layouts.settings')

@section('content')
    {{-- 1. Include alerts at the top --}}
    @include('settings.partials.alerts')

    {{-- 2. Your main content here --}}
    <div class="container mx-auto px-4 py-8">
        @include('settings.partials.header')
        
        <div class="space-y-6">
            @include('settings.partials.system')
            @include('settings.partials.departments')
        </div>
    </div>

    {{-- 3. Include forms at the bottom --}}
    @include('settings.partials.forms')

    {{-- 4. Push the CSS styles --}}
    @push('styles')
    <style>
        /* Animation classes using Tailwind */
        .animate-pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Status indicators */
        .status-indicator {
            @apply inline-block w-2 h-2 rounded-full mr-1;
        }
        .status-active { @apply bg-green-500; }
        .status-warning { @apply bg-yellow-500; }
        .status-inactive { @apply bg-red-500; }

        /* Department card hover effect */
        .department-card:hover {
            @apply transform -translate-y-1 scale-[1.02] shadow-lg;
        }

        /* Setting item hover effect */
        .setting-item:hover {
            @apply bg-gray-50 rounded-lg p-4 -m-2;
        }
        .setting-item:hover::before {
            height: 60%;
        }
    </style>
    @endpush
@endsection -->
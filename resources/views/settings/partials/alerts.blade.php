@if (session('success'))
<div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <strong>Success!</strong> {{ session('success') }}
    </div>
</div>
@endif

@if (session('error'))
<div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
    <div class="flex items-center">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>Error!</strong> {{ session('error') }}
    </div>
</div>
@endif
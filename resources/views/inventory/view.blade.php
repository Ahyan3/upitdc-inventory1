<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('View Equipment') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gray-800 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Equipment Details</h2>
                </div>
                <div class="p-6">
                    <p><strong>Staff Name:</strong> {{ $equipment->staff_name }}</p>
                    <p><strong>Department:</strong> {{ $equipment->department->name ?? 'N/A' }}</p>
                    <p><strong>Equipment Name:</strong> {{ $equipment->equipment_name }}</p>
                    <p><strong>Model/Brand:</strong> {{ $equipment->model_brand }}</p>
                    <p><strong>Date Issued:</strong> {{ $equipment->date_issued }}</p>
                    <p><strong>Serial Number:</strong> {{ $equipment->serial_number }}</p>
                    <p><strong>PR Number:</strong> {{ $equipment->pr_number }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($equipment->status) }}</p>
                    <p><strong>Remarks:</strong> {{ $equipment->remarks ?? 'N/A' }}</p>
                    <a href="{{ route('inventory') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">Back to Inventory</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
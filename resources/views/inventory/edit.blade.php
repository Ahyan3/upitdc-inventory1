<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-gray-800 leading-tight">
            {{ __('Edit Equipment') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8 w-full">
            <div class="bg-white rounded-xl shadow-md overflow-hidden form-card w-full">
                <div class="bg-red-600 px-6 py-4">
                    <h2 class="text-sm font-semibold text-white">Edit Equipment</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('inventory.update', $equipment) }}" method="POST" class="space-y-4" aria-label="Edit Equipment Form">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="staff_name" class="block text-xs font-medium text-gray-700 mb-1">Staff Name *</label>
                            <input type="text" name="staff_name" id="staff_name" value="{{ old('staff_name', $equipment->staff_name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-xs">
                        </div>
                        <div>
                            <label for="department_id" class="block text-xs font-medium text-gray-700 mb-1">Department *</label>
                            <select name="department_id" id="department_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-xs">
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $equipment->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="equipment_name" class="block text-xs font-medium text-gray-700 mb-1">Equipment Name *</label>
                            <input type="text" name="equipment_name" id="equipment_name" value="{{ old('equipment_name', $equipment->equipment_name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-xs">
                        </div>
                        <div>
                            <label for="model_brand" class="block text-xs font-medium text-gray-700 mb-1">Model/Brand *</label>
                            <input type="text" name="model_brand" id="model_brand" value="{{ old('model_brand', $equipment->model_brand) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-xs">
                        </div>
                        <div>
                            <label for="date_issued" class="block text-xs font-medium text-gray-700 mb-1">Date Issued *</label>
                            <input type="date" name="date_issued" id="date_issued" value="{{ old('date_issued', $equipment->date_issued) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-xs">
                        </div>
                        <div>
                            <label for="serial_number" class="block text-xs font-medium text-gray-700 mb-1">Serial Number *</label>
                            <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $equipment->serial_number) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-xs">
                        </div>
                        <div>
                            <label for="pr_number" class="block text-xs font-medium text-gray-700 mb-1">PR Number *</label>
                            <input type="text" name="pr_number" id="pr_number" value="{{ old('pr_number', $equipment->pr_number) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-xs">
                        </div>
                        <div>
                            <label for="remarks" class="block text-xs font-medium text-gray-700 mb-1">Remarks</label>
                            <textarea name="remarks" id="remarks" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-xs">{{ old('remarks', $equipment->remarks) }}</textarea>
                        </div>
                        <div>
                            <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" id="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-xs">
                                <option value="available" {{ old('status', $equipment->status) == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="issued" {{ old('status', $equipment->status) == 'issued' ? 'selected' : '' }}>Issued</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 text-xs">
                                Update Equipment
                            </button>
                            <a href="{{ route('inventory') }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 text-center text-xs">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
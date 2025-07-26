{{-- Edit Department Form (hidden by default) --}}
<form id="edit-department-form" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="department_name" id="edit-department-name">
</form>

{{-- Delete Confirmation Modal (hidden by default) --}}
<div id="delete-confirmation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center" style="display: none;">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full">
        <div class="flex items-center mb-4">
            <div class="bg-red-100 p-2 rounded-full mr-3">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Confirm Deletion</h3>
        </div>
        <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this department?</p>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="document.getElementById('delete-confirmation-modal').style.display='none'" 
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </button>
            <form id="delete-form" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
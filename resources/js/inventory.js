document.addEventListener('DOMContentLoaded', function () {
    // Tab switching
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            tabButtons.forEach(btn => {
                btn.classList.remove('bg-gray-100');
                btn.classList.add('hover:bg-gray-100');
                btn.setAttribute('aria-selected', 'false');
            });
            button.classList.add('bg-gray-100');
            button.classList.remove('hover:bg-gray-100');
            button.setAttribute('aria-selected', 'true');

            tabContents.forEach(content => content.classList.add('hidden'));
            document.getElementById(`${button.dataset.tab}-tab`).classList.remove('hidden');
        });
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('#inventoryTableBody, #staffTableBody, #historyTableBody, #issuanceTableBody');
    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Export to CSV
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn && tableBody) {
        exportBtn.addEventListener('click', function () {
            const rows = tableBody.querySelectorAll('tr');
            let csv = [];
            const headers = tableBody.parentElement.querySelectorAll('thead th');
            csv.push(Array.from(headers).map(header => header.textContent).join(','));

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 0 && row.style.display !== 'none') {
                    const rowData = Array.from(cells).map(cell => `"${cell.textContent.replace(/"/g, '""')}"`).join(',');
                    csv.push(rowData);
                }
            });

            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('href', url);
            a.setAttribute('download', 'export.csv');
            a.click();
            window.URL.revokeObjectURL(url);
        });
    }

    // Edit staff (modal or inline)
    window.editStaff = function (button) {
        const row = button.closest('tr');
        const id = button.getAttribute('data-id');
        const name = row.cells[0].textContent;
        const department = row.cells[1].textContent;
        const email = row.cells[2].textContent;

        // Create modal for editing
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-semibold text-red-600 mb-4">Edit Staff</h2>
                <form action="/staff/${id}" method="POST" class="space-y-4">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.content}">
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" name="name" id="edit_name" value="${name}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="edit_department" class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                        <select name="department" id="edit_department" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="IT" ${department === 'IT' ? 'selected' : ''}>IT</option>
                            <option value="Finance" ${department === 'Finance' ? 'selected' : ''}>Finance</option>
                            <option value="HR" ${department === 'HR' ? 'selected' : ''}>Human Resources</option>
                            <option value="Operations" ${department === 'Operations' ? 'selected' : ''}>Operations</option>
                            <option value="Marketing" ${department === 'Marketing' ? 'selected' : ''}>Marketing</option>
                            <option value="Other" ${department === 'Other' ? 'selected' : ''}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" id="edit_email" value="${email}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="this.closest('.fixed').remove()" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg">Cancel</button>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg">Save</button>
                    </div>
                </form>
            </div>
        `;
        document.body.appendChild(modal);
    };

    const issueForm = document.getElementById('issueForm');
    if (issueForm) {
        issueForm.addEventListener('submit', function (e) {
            const staffName = document.getElementById('staff_name').value;
            const department = document.getElementById('department').value;
            const equipmentName = document.getElementById('equipment_name').value;
            const modelBrand = document.getElementById('model_brand').value;
            const serialNumber = document.getElementById('serial_number').value;
            const dateIssued = document.getElementById('date_issued').value;
            const prNumber = document.getElementById('pr_number').value;

            if (!staffName || !department || !equipmentName || !modelBrand || !serialNumber || !dateIssued || !prNumber) {
                e.preventDefault();
                alert('Please fill all required fields.');
            }
        });
    };
});
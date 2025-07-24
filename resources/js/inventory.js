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

    // Search and Filter functionality
    const searchInput = document.getElementById('searchInput');
    const departmentFilter = document.getElementById('departmentFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableBodies = document.querySelectorAll('#inventoryTableBody, #historyTableBody');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const department = departmentFilter.value;
        const status = statusFilter.value;

        tableBodies.forEach(tableBody => {
            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const departmentText = tableBody.id === 'inventoryTableBody' ? row.cells[1]?.textContent || '' : '';
                const statusText = tableBody.id === 'inventoryTableBody' ? row.cells[7]?.textContent.toLowerCase() || '' : '';

                const matchesSearch = text.includes(searchTerm);
                const matchesDepartment = tableBody.id !== 'inventoryTableBody' || !department || departmentText === department;
                const matchesStatus = tableBody.id !== 'inventoryTableBody' || !status || statusText.includes(status.toLowerCase());

                row.style.display = matchesSearch && matchesDepartment && matchesStatus ? '' : 'none';
            });
        });
    }

    if (searchInput && tableBodies.length) {
        searchInput.addEventListener('input', filterTable);
        departmentFilter.addEventListener('change', filterTable);
        statusFilter.addEventListener('change', filterTable);
    }

    // Export to CSV
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn && tableBodies.length) {
        exportBtn.addEventListener('click', function () {
            let csv = [];
            tableBodies.forEach(tableBody => {
                const headers = tableBody.parentElement.querySelectorAll('thead th');
                csv.push(Array.from(headers).map(header => header.textContent).join(','));

                const rows = tableBody.querySelectorAll('tr');
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length > 0 && row.style.display !== 'none') {
                        const rowData = Array.from(cells).map(cell => `"${cell.textContent.replace(/"/g, '""')}"`).join(',');
                        csv.push(rowData);
                    }
                });
            });

            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('href', url);
            a.setAttribute('download', 'inventory_export.csv');
            a.click();
            window.URL.revokeObjectURL(url);
        });
    }

    // Delete functionality
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            const itemName = this.dataset.item;

            Swal.fire({
                title: `Delete "${itemName}"?`,
                text: "This action cannot be undone!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Deleting...';
                    this.disabled = true;

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => { throw err; });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                form.closest('tr').remove();
                                Swal.fire('Deleted!', data.message, 'success');
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            this.innerHTML = originalHtml;
                            this.disabled = false;
                            Swal.fire('Error!', error.message || 'Failed to delete item', 'error');
                        });
                }
            });
        });
    });
});
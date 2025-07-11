document.addEventListener('DOMContentLoaded', function() {
    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    const dateIssued = document.getElementById('date_issued');
    const dateReturned = document.getElementById('date_returned');
    if (dateIssued && !dateIssued.value) dateIssued.value = today;
    if (dateReturned && !dateReturned.value) dateReturned.value = today;

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#inventoryTableBody tr');
            
            rows.forEach(row => {
                const staff = row.cells[0].textContent.toLowerCase();
                const equipment = row.cells[2].textContent.toLowerCase();
                const serial = row.cells[5].textContent.toLowerCase();
                row.style.display = (staff.includes(searchTerm) || equipment.includes(searchTerm) || serial.includes(searchTerm)) ? '' : 'none';
            });
        });
    }

    // Export CSV
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            const rows = document.querySelectorAll('#inventoryTableBody tr');
            let csvContent = 'Staff,Department,Equipment,Category,Model,Serial,Issued,PR Number,Status\n';

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = row.querySelectorAll('td');
                    const rowData = [
                        cells[0].textContent,
                        cells[1].textContent,
                        cells[2].textContent,
                        cells[3].textContent,
                        cells[4].textContent,
                        cells[5].textContent,
                        cells[6].textContent,
                        cells[7].textContent,
                        cells[8].querySelector('span').textContent
                    ].map(cell => `"${cell.replace(/"/g, '""')}"`).join(',');
                    csvContent += rowData + '\n';
                }
            });

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'inventory_export.csv';
            link.click();
            URL.revokeObjectURL(link.href);
        });
    }
});

// Edit functionality (to be implemented with a modal or form population)
window.editIssuance = function(button) {
    const issuanceId = button.getAttribute('data-id');
    alert(`Editing issuance ID: ${issuanceId}`);
    // Future: Fetch issuance data via AJAX and populate the issue form
};
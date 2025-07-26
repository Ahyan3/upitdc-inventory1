// Auto-submit filters
['time-filter', 'type-filter', 'chart-time-filter', 'inventory-status-filter'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', () => el.closest('form').submit());
});

// Chart initialization
function initializeChart() {
    const ctx = document.getElementById('equipmentChart');
    if (!ctx) return console.log('Chart canvas not found');

    try {
        const equipmentData = JSON.parse(ctx.dataset.equipment || '[]');
        if (!equipmentData.length) {
            ctx.closest('.p-5').innerHTML = '<p class="text-xs text-[#00553d] text-center">No data available</p>';
            return;
        }

        if (window.equipmentChart) window.equipmentChart.destroy();
        window.equipmentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: equipmentData.map(item => item.equipment_name || 'Unknown'),
                datasets: [{
                    label: 'Issuance Count',
                    data: equipmentData.map(item => item.issuance_count || 0),
                    backgroundColor: '#ffcc34',
                    borderColor: '#00553d',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Number of Issuances', font: { size: 10 }, color: '#00553d' }, ticks: { color: '#00553d', font: { size: 10 } } },
                    x: { title: { display: true, text: 'Equipment Type', font: { size: 10 }, color: '#00553d' }, ticks: { color: '#00553d', font: { size: 10 } } }
                },
                plugins: { legend: { labels: { font: { size: 10 }, color: '#00553d' } } }
            }
        });
    } catch (error) {
        console.error('Chart error:', error);
    }
}
document.addEventListener('DOMContentLoaded', initializeChart);

// Dashboard counts
function updateDashboardCounts() {
    const stats = document.querySelectorAll('[data-name] .text-[#00553d]');
    stats.forEach(el => {
        el.dataset.originalText = el.textContent;
        el.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    });

    fetch('/api/dashboard-counts', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(response => response.ok ? response.json() : Promise.reject(`HTTP ${response.status}`))
    .then(data => {
        stats.forEach(el => {
            const name = el.closest('[data-name]').dataset.name.toLowerCase().replace(/ /g, '');
            el.textContent = data[name] || el.dataset.originalText;
            el.classList.add('animate-pulse');
            setTimeout(() => el.classList.remove('animate-pulse'), 500);
        });
    })
    .catch(error => {
        stats.forEach(el => el.textContent = el.dataset.originalText);
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update counts.', confirmButtonColor: '#90143c', customClass: { popup: 'text-xs' } });
    });
}
document.addEventListener('DOMContentLoaded', () => {
    updateDashboardCounts();
    setInterval(updateDashboardCounts, 30000);
});

// Form submissions
document.querySelectorAll('#add-issuance-form, #edit-issuance-form, #edit-inventory-form').forEach(form => {
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(form);
        const method = form.querySelector('input[name="_method"]')?.value || form.method;
        console.log('Submitting form:', {
            action: form.action,
            method,
            data: Object.fromEntries(formData)
        });
        try {
            const response = await fetch(form.action, {
                method: method.toUpperCase(), // Use PUT for @method('PUT')
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData,
            });
            console.log('Response status:', response.status, 'OK:', response.ok);
            const data = await response.json();
            console.log('Response data:', data);
            Swal.fire({
                icon: data.status === 'success' ? 'success' : 'error',
                title: data.status === 'success' ? 'Success' : 'Error',
                text: data.message,
                confirmButtonColor: '#90143c',
                customClass: { popup: 'text-xs' },
            }).then(() => {
                if (data.status === 'success') window.location.reload();
            });
        } catch (error) {
            console.error('Form submission error:', error, 'Form:', form.id);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to process request: ' + error.message,
                confirmButtonColor: '#90143c',
                customClass: { popup: 'text-xs' },
            });
        }
    });
});

// Delete confirmations
document.querySelectorAll('.delete-issuance-form, .delete-inventory-form').forEach(form => {
    form.addEventListener('submit', e => {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: `Delete "${form.dataset.name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#90143c',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            customClass: { popup: 'text-xs' }
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });
});

// Edit issuance modal
document.querySelectorAll('.edit-issuance-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const id = btn.dataset.id;
        try {
            const response = await fetch(`/issuances/${id}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            const issuance = await response.json();
            const form = document.getElementById('edit-issuance-form');
            form.action = `/issuances/${id}`;
            document.getElementById('edit_staff_id').value = issuance.staff_id || '';
            document.getElementById('edit_equipment_id').value = issuance.equipment_id || '';
            document.getElementById('edit_issued_at').value = issuance.issued_at || '';
            document.getElementById('edit_status').value = String(issuance.status || ''); 
            document.getElementById('edit-issuance-modal').classList.remove('hidden');
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load issuance data.', confirmButtonColor: '#90143c', customClass: { popup: 'text-xs' } });
        }
    });
});

// Edit inventory modal
document.querySelectorAll('.edit-inventory-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const id = btn.dataset.id;
        try {
            const response = await fetch(`/inventory/${id}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            const item = await response.json();
            const form = document.getElementById('edit-inventory-form');
            form.action = `/inventory/${id}`;
            document.getElementById('edit_inventory_staff_name').value = item.staff_name || '';
            document.getElementById('edit_equipment_name').value = item.equipment_name || '';
            document.getElementById('edit_model_brand').value = item.model_brand || '';
            document.getElementById('edit_serial_number').value = item.serial_number || '';
            document.getElementById('edit_pr_number').value = item.pr_number || '';
            document.getElementById('edit_date_issued').value = item.date_issued || '';
            document.getElementById('edit_inventory_status').value = String(item.status || '');
            document.getElementById('edit-inventory-modal').classList.remove('hidden');
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load inventory data.', confirmButtonColor: '#90143c', customClass: { popup: 'text-xs' } });
        }
    });
});

// Status updates
document.querySelectorAll('.issuance-status').forEach(select => {
    select.addEventListener('change', async () => {
        const id = select.dataset.id;
        const status = select.value;
        try {
            const response = await fetch(`/issuances/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status })
            });
            const data = await response.json();
            Swal.fire({
                icon: data.status === 'success' ? 'success' : 'error',
                title: data.status === 'success' ? 'Success' : 'Error',
                text: data.message,
                confirmButtonColor: '#90143c',
                customClass: { popup: 'text-xs' }
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to update status.',
                confirmButtonColor: '#90143c',
                customClass: { popup: 'text-xs' }
            });
        }
    });
});
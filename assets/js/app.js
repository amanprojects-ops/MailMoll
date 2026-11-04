// MailMoll Main Application JS

document.addEventListener('DOMContentLoaded', function () {
    // 1. Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('sidebar-wrapper').classList.toggle('toggled');
            document.getElementById('page-content-wrapper').classList.toggle('toggled');
        });
    }

    // 2. Dark Mode Toggle
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    if (themeToggleBtn) {
        const savedTheme = localStorage.getItem('mailmoll_theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
        updateThemeIcon(savedTheme);

        themeToggleBtn.addEventListener('click', function () {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('mailmoll_theme', newTheme);
            updateThemeIcon(newTheme);
        });
    }

    function updateThemeIcon(theme) {
        const icon = document.querySelector('#themeToggleBtn i');
        if (icon) {
            icon.className = theme === 'dark' ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill';
        }
    }

    // 3. Initialize DataTables if present
    if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 25,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records..."
            }
        });
    }

    // 4. Initialize Flatpickr if present
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.datetimepicker', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today"
        });
    }

    // 5. Global Confirmation Dialogs
    document.querySelectorAll('.btn-confirm-delete').forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');
            const message = this.getAttribute('data-message') || "This action cannot be undone.";

            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // 6. Manual Worker Run Button
    const runWorkerBtn = document.getElementById('runWorkerBtn');
    if (runWorkerBtn) {
        runWorkerBtn.addEventListener('click', function () {
            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            fetch(window.MAILMOLL_URL + '/api/worker/run', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                this.disabled = false;
                this.innerHTML = originalHtml;

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Worker Cycle Finished',
                        text: data.message
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Worker run failed', 'error');
                }
            })
            .catch(err => {
                this.disabled = false;
                this.innerHTML = originalHtml;
                Swal.fire('Error', err.message, 'error');
            });
        });
    }
});

// Helper function to test SMTP Connection via AJAX
function testSmtpConnection(accountId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    Swal.fire({
        title: 'Testing SMTP Connection...',
        html: 'Initiating TLS handshake and authentication test with mail server.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(window.MAILMOLL_URL + '/api/smtp/test/' + accountId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Connection Successful!',
                html: `<div class="text-start">
                    <p class="text-success mb-2">${data.message}</p>
                    <pre style="max-height: 200px; font-size: 11px; background: #1e293b; color: #f8fafc; padding: 10px; border-radius: 6px; overflow-y: auto;">${data.debug}</pre>
                </div>`,
                width: '600px'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Connection Failed',
                html: `<div class="text-start">
                    <p class="text-danger mb-2">${data.message}</p>
                    <pre style="max-height: 200px; font-size: 11px; background: #1e293b; color: #f8fafc; padding: 10px; border-radius: 6px; overflow-y: auto;">${data.debug}</pre>
                </div>`,
                width: '600px'
            });
        }
    })
    .catch(err => {
        Swal.fire('Network Error', err.message, 'error');
    });
}

/* ===================================================================
   AuraPay — Shared JavaScript
   =================================================================== */

document.addEventListener('DOMContentLoaded', function () {

    // Sidebar toggle for mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('appSidebar') || document.getElementById('adminSidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (e) {
            if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }

    // Landing nav shadow on scroll
    const landingNav = document.getElementById('landingNav');
    if (landingNav) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 10) {
                landingNav.classList.add('scrolled');
            } else {
                landingNav.classList.remove('scrolled');
            }
        });
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });

    // OTP input: auto-advance and numeric only
    const otpInput = document.querySelector('.otp-input');
    if (otpInput) {
        otpInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
        });
    }

    // Confirm before leaving forms with unsaved changes
    const forms = document.querySelectorAll('form[method="post"]');
    let formChanged = false;
    forms.forEach(function (form) {
        form.addEventListener('change', function () { formChanged = true; });
        form.addEventListener('submit', function () { formChanged = false; });
    });
    window.addEventListener('beforeunload', function (e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});

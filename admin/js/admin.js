/**
 * Admin Panel — JavaScript
 * Sidebar toggle, delete modals, form validation
 */

document.addEventListener('DOMContentLoaded', () => {
    initSidebarToggle();
    initDeleteModals();
    initFlashDismiss();
});

/* ---------- Sidebar Toggle (Mobile) ---------- */
function initSidebarToggle() {
    const toggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.admin-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    if (!toggle || !sidebar) return;

    const setOpen = (open) => {
        sidebar.classList.toggle('open', open);
        toggle.setAttribute('aria-expanded', String(open));
        if (overlay) overlay.classList.toggle('active', open);
    };

    toggle.addEventListener('click', () => {
        setOpen(!sidebar.classList.contains('open'));
    });

    if (overlay) {
        overlay.addEventListener('click', () => {
            setOpen(false);
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && sidebar.classList.contains('open')) {
            setOpen(false);
            toggle.focus();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768 && sidebar.classList.contains('open')) {
            setOpen(false);
        }
    });
}

/* ---------- Delete Confirmation Modal ---------- */
function initDeleteModals() {
    // Attach to all delete buttons
    document.querySelectorAll('[data-delete-id]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const id = btn.getAttribute('data-delete-id');
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            if (modal && form) {
                form.querySelector('input[name="delete_id"]').value = id;
                modal.classList.add('active');
            }
        });
    });
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
}

/* ---------- Auto-dismiss Flash Messages ---------- */
function initFlashDismiss() {
    const flash = document.querySelector('.flash-message');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-10px)';
            flash.style.transition = 'all 0.3s ease';
            setTimeout(() => flash.remove(), 300);
        }, 4000);
    }
}

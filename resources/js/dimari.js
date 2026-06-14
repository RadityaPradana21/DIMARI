/**
 * DIMARI — Global JS
 * Toast system, progress animation, logout modal
 */

// ===== TOAST =====
function showToast(message, type = 'info', duration = 3500) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const icons = { success: '✅', error: '❌', info: 'ℹ️', warning: '⚠️' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<span>${icons[type] || 'ℹ️'}</span><span>${message}</span>`;

    toast.addEventListener('click', () => dismissToast(toast));
    container.appendChild(toast);

    setTimeout(() => dismissToast(toast), duration);
}

function dismissToast(toast) {
    if (toast.classList.contains('hiding')) return;
    toast.classList.add('hiding');
    toast.addEventListener('animationend', () => toast.remove(), { once: true });
}

// ===== MOBILE MENU =====
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const toggle = document.querySelector('.mobile-menu-toggle');

    if (!menu || !toggle) return;

    const isOpen = menu.classList.toggle('mobile-open');
    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}

document.addEventListener('click', function(e) {
    const menu   = document.getElementById('mobileMenu');
    const toggle = document.querySelector('.mobile-menu-toggle');
    if (menu && toggle && !menu.contains(e.target) && !toggle.contains(e.target)) {
        menu.classList.remove('mobile-open');
        toggle.setAttribute('aria-expanded', 'false');
    }
});

// ===== LOGOUT MODAL =====
function openLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) modal.classList.remove('hidden');
}

function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) modal.classList.add('hidden');
}

// Close modal on backdrop click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('logoutModal');
    if (modal && e.target === modal) closeLogoutModal();
});

// Close modal on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLogoutModal();
});

// ===== PROGRESS BAR ANIMATION =====
document.addEventListener('DOMContentLoaded', function() {
    const fills = document.querySelectorAll('.progress-fill');
    fills.forEach(fill => {
        const target = fill.getAttribute('data-width') || fill.style.width;
        fill.style.width = '0%';
        setTimeout(() => {
            fill.style.width = target + (target.includes('%') ? '' : '%');
        }, 200);
    });

    const dots = document.querySelectorAll('.step-dot.done');
    dots.forEach((dot, i) => {
        dot.style.opacity = '0';
        dot.style.transform = 'scale(0.5)';
        setTimeout(() => {
            dot.style.transition = 'all 0.3s ease';
            dot.style.opacity = '1';
            dot.style.transform = 'scale(1)';
        }, 300 + i * 80);
    });

    const statNums = document.querySelectorAll('.stat-num');
    statNums.forEach(el => {
        const target = parseInt(el.textContent, 10);
        if (isNaN(target) || target === 0) return;
        let current = 0;
        const step = Math.ceil(target / 20);
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current;
            if (current >= target) clearInterval(timer);
        }, 40);
    });
});

// ===== EXPOSE KE WINDOW (wajib untuk Vite ES module) =====
// Vite membundle JS sebagai ES module — fungsi lokal tidak otomatis
// masuk ke window scope, sehingga onclick="..." di HTML tidak bisa
// menemukannya. Solusi: daftarkan eksplisit ke window.
window.showToast        = showToast;
window.openLogoutModal  = openLogoutModal;
window.closeLogoutModal = closeLogoutModal;
window.toggleMobileMenu = toggleMobileMenu;
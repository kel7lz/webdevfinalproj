// =============================================
// King's Cup Coffee — Main JavaScript
// =============================================

'use strict';

// ── Mobile Navigation ──────────────────────────────────────
(function() {
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('nav-links');

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove('open');
            }
        });
    }
})();

// ── User Dropdown ──────────────────────────────────────────
(function() {
    const userBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', () => {
            userDropdown.classList.remove('show');
        });
    }
})();

// ── Flash Message Auto-Dismiss ─────────────────────────────
(function() {
    const flash = document.querySelector('.flash');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 400ms';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 400);
        }, 4000);
    }
})();

// ── Password Toggle (Show/Hide) ────────────────────────────
(function() {
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            if (!input) return;
            
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    });
})();

// ── Quantity Stepper ───────────────────────────────────────
(function() {
    document.querySelectorAll('.qty-control').forEach(control => {
        const minusBtn = control.querySelector('.qty-minus');
        const plusBtn = control.querySelector('.qty-plus');
        const valueEl = control.querySelector('.qty-value');
        const inputEl = control.querySelector('input[type="hidden"]');

        if (!minusBtn || !plusBtn || !valueEl) return;

        let qty = parseInt(valueEl.textContent) || 1;

        function updateQty(n) {
            qty = Math.max(1, Math.min(99, n));
            valueEl.textContent = qty;
            if (inputEl) inputEl.value = qty;
        }

        minusBtn.addEventListener('click', () => updateQty(qty - 1));
        plusBtn.addEventListener('click', () => updateQty(qty + 1));
    });
})();
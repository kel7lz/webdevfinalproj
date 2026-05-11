/* ============================================================
   assets/js/admin.js — Admin panel JavaScript
   ============================================================ */

'use strict';

// ── Sidebar toggle (mobile) ─────────────────────────────────
(function () {
  const toggle  = document.getElementById('sidebar-toggle');
  const sidebar = document.getElementById('admin-sidebar');
  if (!toggle || !sidebar) return;

  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('is-open');
  });
  document.addEventListener('click', (e) => {
    if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
      sidebar.classList.remove('is-open');
    }
  });
})();

// ── Order status update via AJAX ────────────────────────────
(function () {
  document.querySelectorAll('.js-status-select').forEach((sel) => {
    sel.addEventListener('change', async () => {
      const orderId = sel.dataset.orderId;
      const status  = sel.value;
      const csrf    = document.querySelector('input[name="csrf_token"]')?.value || '';

      sel.disabled = true;

      try {
        const res = await fetch('/mercato/admin/orders.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `action=update_status&order_id=${orderId}&status=${status}&csrf_token=${encodeURIComponent(csrf)}`,
        });
        const data = await res.json();
        if (data.success) {
          showAdminFlash('Order #' + orderId + ' updated to ' + status, 'success');
        } else {
          showAdminFlash('Failed to update: ' + (data.error || 'Unknown error'), 'error');
        }
      } catch (e) {
        showAdminFlash('Network error. Please try again.', 'error');
      } finally {
        sel.disabled = false;
      }
    });
  });
})();

// ── Product delete confirm ──────────────────────────────────
(function () {
  document.querySelectorAll('.js-delete-product').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      if (!confirm('Delete this product? This cannot be undone.')) {
        e.preventDefault();
      }
    });
  });
})();

// ── Image URL preview ───────────────────────────────────────
(function () {
  const urlInput = document.getElementById('product-image-url');
  const preview  = document.getElementById('product-image-preview');
  if (!urlInput || !preview) return;

  urlInput.addEventListener('input', () => {
    const url = urlInput.value.trim();
    if (url) {
      preview.src = url;
      preview.style.display = 'block';
    } else {
      preview.style.display = 'none';
    }
  });
})();

// ── Admin flash helper ──────────────────────────────────────
function showAdminFlash(msg, type) {
  const existing = document.querySelector('.flash');
  if (existing) existing.remove();

  const div = document.createElement('div');
  div.className = 'flash ' + (type === 'error' ? 'flash-error' : 'flash-success');
  div.textContent = msg;
  div.style.maxWidth = 'none';

  const content = document.querySelector('.admin-content');
  if (content) content.prepend(div);

  setTimeout(() => {
    div.style.transition = 'opacity 400ms';
    div.style.opacity = '0';
    setTimeout(() => div.remove(), 400);
  }, 4000);
}
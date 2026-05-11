/* ============================================================
   assets/js/main.js — Customer-side JavaScript
   ============================================================ */

'use strict';

// ── Nav: hamburger & user dropdown ─────────────────────────
(function () {
  const hamburger   = document.getElementById('hamburger');
  const navLinks    = document.getElementById('nav-links');
  const userToggle  = document.getElementById('user-menu-toggle');
  const userDropdown = document.getElementById('user-dropdown');

  if (hamburger && navLinks) {
    hamburger.addEventListener('click', () => {
      navLinks.classList.toggle('is-open');
    });
    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!hamburger.contains(e.target) && !navLinks.contains(e.target)) {
        navLinks.classList.remove('is-open');
      }
    });
  }

  if (userToggle && userDropdown) {
    userToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      userDropdown.classList.toggle('is-open');
    });
    document.addEventListener('click', () => {
      userDropdown.classList.remove('is-open');
    });
  }
})();

// ── Flash message auto-dismiss ──────────────────────────────
(function () {
  const flash = document.querySelector('.flash');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity 400ms';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 400);
    }, 4000);
  }
})();

// ── Product Detail: quantity stepper ───────────────────────
(function () {
  const minusBtn = document.getElementById('qty-minus');
  const plusBtn  = document.getElementById('qty-plus');
  const qtyVal   = document.getElementById('qty-value');
  const qtyInput = document.getElementById('qty-input');

  if (!minusBtn || !plusBtn || !qtyVal) return;

  let qty = parseInt(qtyVal.textContent) || 1;

  function updateQty(n) {
    qty = Math.max(1, Math.min(99, n));
    qtyVal.textContent = qty;
    if (qtyInput) qtyInput.value = qty;
  }

  minusBtn.addEventListener('click', () => updateQty(qty - 1));
  plusBtn.addEventListener('click',  () => updateQty(qty + 1));
})();

// ── Cart: quantity update via AJAX ──────────────────────────
(function () {
  document.querySelectorAll('.js-cart-qty').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const productId = btn.dataset.productId;
      const action    = btn.dataset.action; // 'inc' | 'dec'

      const res = await fetch('/mercato/customer/cart_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=${action}&product_id=${productId}&csrf_token=${getCsrfToken()}`,
      });

      if (res.ok) window.location.reload();
    });
  });

  document.querySelectorAll('.js-cart-remove').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const productId = btn.dataset.productId;
      const res = await fetch('/mercato/customer/cart_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove&product_id=${productId}&csrf_token=${getCsrfToken()}`,
      });
      if (res.ok) window.location.reload();
    });
  });
})();

// ── Menu: category tab filter ───────────────────────────────
(function () {
  const tabs  = document.querySelectorAll('.js-cat-tab');
  const cards = document.querySelectorAll('.js-product-card');

  if (!tabs.length) return;

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      tabs.forEach((t) => t.classList.remove('is-active'));
      tab.classList.add('is-active');

      const cat = tab.dataset.cat;
      cards.forEach((card) => {
        if (cat === 'all' || card.dataset.cat === cat) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
})();

// ── Payment method selection ────────────────────────────────
(function () {
  const methods = document.querySelectorAll('.js-payment-method');
  methods.forEach((radio) => {
    radio.addEventListener('change', () => {
      methods.forEach((r) => r.closest('.payment-method-option').classList.remove('selected'));
      radio.closest('.payment-method-option').classList.add('selected');
    });
  });
})();

// ── PayMongo: initiate payment ──────────────────────────────
(function () {
  const payBtn  = document.getElementById('js-pay-btn');
  const spinner = document.getElementById('js-pay-spinner');
  const errBox  = document.getElementById('js-pay-error');

  if (!payBtn) return;

  payBtn.addEventListener('click', async () => {
    const method = document.querySelector('input[name="payment_method"]:checked');
    if (!method) {
      showError('Please select a payment method.');
      return;
    }

    payBtn.disabled = true;
    if (spinner) spinner.style.display = 'inline-block';
    if (errBox) errBox.textContent = '';

    try {
      const res = await fetch('/mercato/customer/payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=create_intent&payment_method=${method.value}&csrf_token=${getCsrfToken()}`,
      });

      const data = await res.json();

      if (data.error) {
        showError(data.error);
        payBtn.disabled = false;
        if (spinner) spinner.style.display = 'none';
        return;
      }

      // Redirect to PayMongo-hosted page or handle inline
      if (data.redirect_url) {
        window.location.href = data.redirect_url;
      } else if (data.client_key) {
        // Show the embedded payment element
        initPayMongoElement(data.client_key, data.intent_id, method.value);
      }
    } catch (err) {
      showError('A network error occurred. Please try again.');
      payBtn.disabled = false;
      if (spinner) spinner.style.display = 'none';
    }
  });

  function showError(msg) {
    if (errBox) { errBox.textContent = msg; errBox.style.display = 'block'; }
    else alert(msg);
  }
})();

// ── PayMongo JS SDK element mount ───────────────────────────
function initPayMongoElement(clientKey, intentId, method) {
  const container = document.getElementById('paymongo-element');
  if (!container) return;

  // Hide pay button, show element area
  const paySection = document.getElementById('payment-action-section');
  if (paySection) paySection.style.display = 'none';
  container.style.display = 'block';

  // For GCash / PayMaya — show QR page
  if (method === 'gcash' || method === 'paymaya') {
    container.innerHTML = `
      <div class="gcash-frame">
        <div class="gcash-frame__header">
          <span style="font-size:24px">${method === 'gcash' ? '💙' : '💜'}</span>
          ${method === 'gcash' ? 'GCash' : 'Maya'}
        </div>
        <div class="gcash-qr">
          <p style="padding:20px;color:#555;font-size:13px">
            Scan with ${method === 'gcash' ? 'GCash' : 'Maya'} app<br>
            <small>(QR generated by PayMongo)</small>
          </p>
        </div>
        <p style="text-align:center;font-size:13px;margin-top:8px">
          King's Cup
        </p>
      </div>
      <button class="btn btn--primary btn--full" id="js-verify-btn">
        I've Completed Payment
      </button>
    `;

    document.getElementById('js-verify-btn').addEventListener('click', () => {
      verifyPayment(intentId, clientKey);
    });
    return;
  }

  // For card — use PayMongo JS SDK
  if (window.PayMongo) {
    const elements = window.PayMongo.elements({ clientKey });
    const card = elements.create('card');
    card.mount('#paymongo-element');

    const confirmBtn = document.createElement('button');
    confirmBtn.className = 'btn btn--primary btn--full mt-6';
    confirmBtn.textContent = 'Confirm Payment';
    container.after(confirmBtn);

    confirmBtn.addEventListener('click', async () => {
      confirmBtn.disabled = true;
      const result = await elements.confirmPayment({
        elements,
        confirmParams: { return_url: window.location.origin + '/mercato/customer/payment_success.php' },
      });
      if (result.error) {
        document.getElementById('js-pay-error').textContent = result.error.message;
        confirmBtn.disabled = false;
      }
    });
  }
}

// ── Verify payment completion ───────────────────────────────
async function verifyPayment(intentId, clientKey) {
  const btn = document.getElementById('js-verify-btn');
  if (btn) { btn.disabled = true; btn.textContent = 'Verifying…'; }

  const res = await fetch('/mercato/customer/payment.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=verify&intent_id=${intentId}&client_key=${clientKey}&csrf_token=${getCsrfToken()}`,
  });

  const data = await res.json();

  if (data.success) {
    // Show verified UI
    const container = document.getElementById('paymongo-element');
    if (container) {
      container.innerHTML = `
        <div class="payment-verified">
          <div class="payment-verified__icon">✅</div>
          <div class="payment-verified__title">Payment Verified</div>
          <div class="payment-verified__msg">Your payment has been confirmed.<br>Your order is now being prepared.</div>
        </div>
        <a href="/mercato/customer/orders.php" class="btn btn--primary btn--full mt-6">Check Order Status</a>
      `;
    }
  } else {
    if (btn) { btn.disabled = false; btn.textContent = 'I\'ve Completed Payment'; }
    const errBox = document.getElementById('js-pay-error');
    if (errBox) { errBox.textContent = data.error || 'Payment not confirmed yet. Please try again.'; errBox.style.display = 'block'; }
  }
}

// ── CSRF token helper ───────────────────────────────────────
function getCsrfToken() {
  const el = document.querySelector('input[name="csrf_token"]');
  return el ? encodeURIComponent(el.value) : '';
}
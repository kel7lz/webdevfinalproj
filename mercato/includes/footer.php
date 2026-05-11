<?php
// ============================================================
// includes/footer.php — Customer-side footer
// ============================================================
?>
</main><!-- /.main-content -->

<footer class="footer">
  <div class="footer__inner container">
    <div class="footer__brand">
      <p class="footer__logo"><?= APP_NAME ?></p>
      <p class="footer__tagline">Quality, Authenticity, and Connection.</p>
    </div>
    <div class="footer__contact">
      <p class="footer__label">Contact us anytime</p>
      <p><a href="mailto:info@mercatocafe.com">info@mercatocafe.com</a></p>
      <p><?= APP_NAME ?></p>
    </div>
  </div>
  <div class="footer__bottom">
    <p>&copy; <?= date('Y') ?> <?= APP_NAME ?> Coffee Company. All rights reserved.</p>
  </div>
</footer>

<script src="<?= ASSETS_URL ?>/js/main.js"></script>
</body>
</html>
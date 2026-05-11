<?php
// ============================================================
// customer/index.php — Homepage
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
session_boot();

$page_title = APP_NAME . ' — Brewed to Perfection';

$categories = db_fetch_all(
    'SELECT * FROM categories ORDER BY sort_order ASC LIMIT 4'
);

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero">
  <div class="container">
    <p class="hero__tagline">It's a great day for coffee here at <?= h(APP_NAME) ?>!</p>
    <h1 class="hero__title">With new ways to experience your espresso favorites and make every sip special.</h1>
    <a href="<?= APP_URL ?>/customer/menu.php" class="btn btn--outline btn--lg" style="color:var(--color-bg);border-color:var(--color-bg);">
      Order Now!
    </a>
  </div>
</section>

<section class="story-section container">
  <div class="story-section__image">
    <div class="story-section__image-placeholder"></div>
  </div>
  <div class="story-section__content">
    <p class="story-section__label"><?= h(APP_NAME) ?>'s Story</p>
    <h2 class="story-section__title">Behind The Drink of<br>The Royals</h2>
    <p>Read how every brew made its way to the hearts of many and where it all began.</p>
    <a href="<?= APP_URL ?>/customer/story.php" class="btn btn--primary">Read</a>
  </div>
</section>

<section id="menu">
  <div class="section-header container">
    <h2 class="section-header__title">Brewed to Perfection</h2>
    <p class="section-header__subtitle">Our coffee menu offers a delightful selection of rich espresso blends, freshly crafted drinks, and warm, delicious pastries.</p>
  </div>

  <div class="category-grid container">
    <?php
    $card_styles = [
      ['class' => 'category-card--espresso', 'icon' => '☕', 'slug' => 'espresso-blends'],
      ['class' => 'category-card--pastries', 'icon' => '🥐', 'slug' => 'fresh-pastries'],
      ['class' => 'category-card--cold',     'icon' => '🧋', 'slug' => 'cold-drinks'],
    ];
    $descriptions = [
      'Espresso Blends' => 'Rich and smooth coffee made from carefully selected beans.',
      'Fresh Pastries'  => 'Freshly baked sweet and savory treats.',
      'Cold Drinks'     => 'Refreshing beverages perfect for a cool pick-me-up.',
      'No-Coffee Blends'=> 'Vibrant, fruit-forward blends with no coffee.',
    ];
    $i = 0;
    foreach ($categories as $cat):
      $style = $card_styles[$i % 3];
      $i++;
    ?>
    <div class="category-card <?= $style['class'] ?>">
      <div class="category-card__icon"><?= $style['icon'] ?></div>
      <h3 class="category-card__title"><?= h($cat['name']) ?></h3>
      <p class="category-card__desc"><?= h($descriptions[$cat['name']] ?? '') ?></p>
      <a href="<?= APP_URL ?>/customer/menu.php?cat=<?= h($cat['slug']) ?>" class="btn">
        Check it out
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="join-section">
  <div class="container">
    <h2 class="join-section__title">Join us now, and get your drinks started!</h2>
    <div class="join-steps">
      <div class="join-step">
        <div class="join-step__icon">👤</div>
        <div class="join-step__label">Create an account</div>
      </div>
      <div class="join-step">
        <div class="join-step__icon">☕</div>
        <div class="join-step__label">Order and pay</div>
      </div>
      <div class="join-step">
        <div class="join-step__icon">😊</div>
        <div class="join-step__label">Enjoy!</div>
      </div>
    </div>
  </div>
</section>

<section class="container" style="padding:var(--space-12) var(--space-6);text-align:center;">
  <h2>Questions?</h2>
  <p class="text-muted">We have answers. <a href="mailto:info@mercatocafe.com" style="color:var(--color-secondary);font-weight:600;">Check out the FAQs</a></p>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
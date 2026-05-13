<?php
// ============================================================
// King's Cup Coffee — Homepage
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
        <p class="hero-tagline">It's a great day for coffee at <?= h(APP_NAME) ?></p>
        <h1>With new ways to experience your espresso favorites and make every sip special.</h1>
        <a href="<?= APP_URL ?>/customer/menu.php" class="btn btn-gold btn-lg">Order Now</a>
    </div>
</section>

<section class="story-section container">
    <div class="story-text">
        <p class="story-label"><?= h(APP_NAME) ?>'s Story</p>
        <h2>Behind The Drink of The Royals</h2>
        <p class="story-desc">Read how every brew made its way to the hearts of many and where it all began.</p>
        <a href="#" class="btn btn-primary">Read Our Story</a>
    </div>
    <div class="story-image">
        <div class="oval-frame">
            <img src="<?= ASSETS_URL ?>/images/coffee-story.jpg" alt="Our Story" 
                 onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22240%22 height=%22320%22><rect fill=%22%23C8A96E%22 width=%22240%22 height=%22320%22/><text x=%22120%22 y=%22160%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%233B1F0F%22 font-size=%2248%22>☕</text></svg>'">
        </div>
    </div>
</section>

<section class="brewed-section" id="menu">
    <h2 class="brewed-title">Brewed to Perfection</h2>
    <p class="brewed-desc">Our coffee menu offers a delightful selection of rich espresso blends, freshly crafted drinks, and warm, delicious pastries.</p>

    <div class="cards-grid container">
        <?php
        $icons = ['☕', '🥐', '🧋', '🍵'];
        $styles = ['card-light', 'card-dark', 'card-light', 'card-dark'];
        $i = 0;
        foreach ($categories as $cat):
        ?>
        <div class="menu-card <?= $styles[$i % 4] ?>">
            <div class="card-img-wrap">
                <img src="<?= ASSETS_URL ?>/images/category-<?= $cat['slug'] ?>.jpg" alt="<?= h($cat['name']) ?>"
                     onerror="this.style.display='none'">
            </div>
            <h3><?= $icons[$i] ?> <?= h($cat['name']) ?></h3>
            <p><?= h($cat['description'] ?? '') ?></p>
            <a href="<?= APP_URL ?>/customer/menu.php?cat=<?= h($cat['slug']) ?>" 
               class="btn-check <?= $styles[$i % 4] === 'card-dark' ? 'btn-check-light' : '' ?>">
                Check it out
            </a>
        </div>
        <?php $i++; endforeach; ?>
    </div>
</section>

<section class="join-banner container">
    <div class="join-image">
        <img src="<?= ASSETS_URL ?>/images/join-us.jpg" alt="Join Us"
             onerror="this.style.display='none'">
    </div>
    <div class="join-content">
        <p class="join-text">Join us now, and get your drinks started!</p>
        <div class="join-steps">
            <div class="step">
                <div class="step-icon">👤</div>
                <span>Create an account</span>
            </div>
            <div class="step">
                <div class="step-icon">☕</div>
                <span>Order and pay</span>
            </div>
            <div class="step">
                <div class="step-icon">😊</div>
                <span>Enjoy!</span>
            </div>
        </div>
    </div>
</section>

<section class="faq-section container">
    <h2>Questions?</h2>
    <p>We have answers. <a href="mailto:info@kingscup.com">Check out the FAQs</a></p>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
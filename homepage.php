<?php
require '_base.php';

$_title = "Books & Stationery Store - Home";
require '_head.php';

// Get featured products
$stm = $_db->query("SELECT * FROM products ORDER BY product_id DESC LIMIT 8");
$featured_products = $stm->fetchAll();

// Get user's wishlist items
$wishlisted_items = [];
if (isset($_users) && $_users->role === 'member') {
    $stm_wl = $_db->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $stm_wl->execute([$_users->id]);
    $wishlisted_items = $stm_wl->fetchAll(PDO::FETCH_COLUMN);
}
?>

<style>
    .hero-section {
        text-align: center;
        padding: 50px 20px;
        background-color: #eef2f3;
        border-radius: 10px;
        margin-bottom: 40px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .hero-section h2 {
        font-size: 2.5em;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        padding: 10px;
    }

    .product-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        background: white;
        transition: transform 0.2s;
        display: flex;
        flex-direction: column;
    }

    .product-card h3 {
        font-size: 1.1em;
        margin-bottom: 5px;
        min-height: 2.8em;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .product-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
        margin-bottom: 15px;
        background-color: #f9f9f9;
    }

    .price {
        color: #e74c3c;
        font-size: 1.3em;
        font-weight: bold;
        margin: 10px 0;
    }

    .btn-shop, .btn-cart {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
    }

    .btn-shop:hover { background-color: #2980b9; }
    .btn-cart:hover { background-color: #27ae60; }

    .button-group {
        display: flex;
        gap: 10px;
        margin-top: auto;
        width: 100%;
        align-items: stretch;
    }
    
    .button-group a {
        display: flex;
        flex-grow: 1;
        text-decoration: none;
    }
    
    .btn-cart {
        background-color: #2ecc71;
        width: 100%;
        box-sizing: border-box;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-wishlist {
        background-color: white;
        border: 1px solid #ddd;
        color: #ff4757;
        padding: 0 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        box-sizing: border-box;
        min-width: 52px;
    }

    .btn-wishlist:hover {
        border-color: #ff4757;
        background-color: #fff0f2;
    }

    [data-theme="dark"] .hero-section { background-color: #2c3e50; }
    [data-theme="dark"] .hero-section h2 { color: white; }
    [data-theme="dark"] .product-card { background-color: #1a1a1a; border-color: #444; }
    [data-theme="dark"] .btn-wishlist { background-color: #333; border-color: #555; }
</style>

<div class="hero-section">
    <h2>Discover Your Next Great Read</h2>
    <p style="font-size: 1.2em; margin-bottom: 20px;">Explore our massive collection of books and premium stationery supplies.</p>
    <a href="<?= BASE_URL ?>/product/list.php" class="btn-shop">Shop All Products</a>
</div>

<h2 style="text-align: center; margin-bottom: 20px;">🔥 Featured Additions</h2>

<div class="product-grid">
    <?php if ($featured_products): ?>
        <?php foreach ($featured_products as $p): ?>
            <div class="product-card">
                <?php $photo_file = !empty($p->photo) ? $p->photo : 'notebook.png'; ?>
                
                <img src="image/product_photos/<?= htmlspecialchars($photo_file) ?>" 
                     alt="<?= htmlspecialchars($p->product_name) ?>" 
                     onerror="this.onerror=null; this.src='image/product_photos/notebook.png';">
                
                <h3><?= htmlspecialchars($p->product_name) ?></h3>
                
                <p class="price">RM <?= number_format($p->price, 2) ?></p>
                
                <div class="button-group">
                    <a href="/product/detail.php?action=add&id=<?= urlencode($p->product_id) ?>">
                        <span class="btn-cart">Add to Cart</span>
                    </a>
                    
                    <?php if (isset($_users) && $_users): ?>
                        <a href="<?= BASE_URL ?>/wishlist_toggle.php?product_id=<?= urlencode($p->product_id) ?>" 
                           style="flex-grow: 0;" 
                           class="btn-wishlist"
                           title="Add/Remove Wishlist">
                            <?= in_array($p->product_id, $wishlisted_items) ? '❤️' : '🤍' ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; width: 100%;">No products found in the database. Please add some products!</p>
    <?php endif; ?>
</div>

<br><br>

<?php require '_foot.php'; ?>
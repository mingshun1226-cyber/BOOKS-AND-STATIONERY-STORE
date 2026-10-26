<?php
require '_base.php';
auth('member');

$_title = 'My Wishlist';

// ------------------------------------------------------------
// Handle Add to Cart from Wishlist
// ------------------------------------------------------------
if (is_post()) {
    $action = post('action');
    $product_id = post('product_id');

    if ($action == 'add_cart' && $product_id != '') {
        $cart = get_cart();
        $existing_qty = $cart[$product_id] ?? 0;

        // add 1 unit
        update_cart($product_id, $existing_qty + 1);

        temp('info', 'Item added to cart.');
        redirect('/order/cart.php');
    }
}

// ------------------------------------------------------------
// Get wishlist items
// ------------------------------------------------------------
$user_id = $_users->id;

$stm = $_db->prepare("
    SELECT p.*, w.created_at
    FROM wishlist w
    JOIN products p ON w.product_id = p.product_id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stm->execute([$user_id]);
$wishlist_items = $stm->fetchAll();

require '_head.php';
?>

<main style="padding: 20px;">
    <h2>❤️ My Wishlist</h2>
    <hr>

    <?php if ($info = temp('info')): ?>
        <div style="background: #ffc107; padding: 12px 15px; margin: 15px 0 20px 0; border-radius: 6px; color: #333; font-weight: bold;">
            <?= $info ?>
        </div>
    <?php endif; ?>

    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 20px;">
        <?php if ($wishlist_items): ?>
            <?php foreach ($wishlist_items as $p): ?>
                <div class="product-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; background: white; display: flex; flex-direction: column;">
                    <?php $photo_file = !empty($p->photo) ? $p->photo : 'notebook.png'; ?>
                    
                    <img src="<?= BASE_URL ?>/image/product_photos/<?= htmlspecialchars($photo_file) ?>"
                         alt="<?= htmlspecialchars($p->product_name) ?>"
                         onerror="this.onerror=null; this.src='<?= BASE_URL ?>/image/product_photos/notebook.png';"
                         style="max-width: 100%; height: 150px; object-fit: contain; margin-bottom: 10px;">

                    <h3 style="font-size: 1.1em; margin-bottom: 5px; min-height: 2.8em;">
                        <?= htmlspecialchars($p->product_name) ?>
                    </h3>

                    <p class="price" style="color: green; font-weight: bold; margin-bottom: 15px;">
                        RM <?= number_format($p->price, 2) ?>
                    </p>

                    <div style="display: flex; gap: 10px; justify-content: center; margin-top: auto;">
                        
                        <form method="post" style="flex-grow: 1; margin: 0;">
                            <input type="hidden" name="action" value="add_cart">
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($p->product_id) ?>">
                            <button type="submit"
                                    style="background: #2ecc71; color: white; padding: 8px; width: 100%; border: none; cursor: pointer; border-radius: 4px;">
                                Add to Cart
                            </button>
                        </form>

                        <a href="<?= BASE_URL ?>/wishlist_toggle.php?product_id=<?= urlencode($p->product_id) ?>" style="text-decoration: none;">
                            <button type="button"
                                    style="background: white; color: red; border: 1px solid #ddd; padding: 8px 15px; cursor: pointer; border-radius: 4px;"
                                    title="Remove">
                                💔
                            </button>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Your wishlist is empty. Go find some books to save!</p>
        <?php endif; ?>
    </div>
</main>

<?php require '_foot.php'; ?>
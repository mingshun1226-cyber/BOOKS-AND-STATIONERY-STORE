<?php
require '_base.php';

auth();

// ----------------------------------------------------------------------------

$product_id = req('product_id');

if ($product_id != '') {
    $user_id = $_users->id; 

    // Check if this product already exists in user's wishlist
    $stm = $_db->prepare('SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?');
    $stm->execute([$user_id, $product_id]);
    $w = $stm->fetch();

    if ($w) {
        // Remove from wishlist
        $stm = $_db->prepare('DELETE FROM wishlist WHERE user_id = ? AND product_id = ?');
        $stm->execute([$user_id, $product_id]);

        temp('info', 'Removed from Wishlist 💔');
    }
    else {
        // Add to wishlist
        $stm = $_db->prepare('INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)');
        $stm->execute([$user_id, $product_id]);

        temp('info', 'Added to Wishlist ❤️');
    }
}

redirect($_SERVER['HTTP_REFERER'] ?? 'product.php');
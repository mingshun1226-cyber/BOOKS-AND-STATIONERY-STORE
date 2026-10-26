<?php
require '_base.php';
auth('admin');


$id = req('id');

if ($id) {
    // Find the product in the database to get its photo name
    $stm = $_db->prepare("SELECT photo FROM products WHERE product_id = ?");
    $stm->execute([$id]);
    $product = $stm->fetch();

    if ($product) {
        // delete the physical photo file from the server
        if ($product->photo) {
            $file_path = PRODUCT_PHOTO_PATH . '/' . $product->photo;

            if (is_file($file_path)) {
                unlink($file_path);
            }
        }

        // Delete the product record from the database
        $stm = $_db->prepare("DELETE FROM products WHERE product_id = ?");
        $stm->execute([$id]);

        temp('info', 'Product and photo deleted successfully');
    }
}

// Redirect back to the product list
redirect('productlist.php');
?>
<?php
require '_base.php';

$cart = get_cart();
$order_id = '';


if (!empty($cart)) {
    
    $shipping_address = $_SESSION['shipping_address'] ?? '';
    $ids = array_keys($cart);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stm = $_db->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
    $stm->execute($ids);
    $products_in_cart = $stm->fetchAll();

    $grand_total = 0;
    foreach ($products_in_cart as $p) {
        $qty = $cart[$p->product_id];
        $grand_total += ($p->price * $qty);
    }

    try {
        $_db->beginTransaction();
        
        $order_id = uniqid('ORD-');

        // Insert into orders 
        $stm1 = $_db->prepare("
            INSERT INTO orders (order_id, user_id, total_amount, status, order_date) 
            VALUES (?, ?, ?, 'Paid', NOW())
        ");
        $stm1->execute([$order_id, $_users->id, $grand_total]);

        $stm2 = $_db->prepare("INSERT INTO order_items (order_item_id, order_id, product_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
    
        $stm3 = $_db->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ? AND stock >= ?");

        foreach ($products_in_cart as $p) {
            $qty = $cart[$p->product_id];
            $order_item_id = uniqid('ITM-');
            
            $stm2->execute([$order_item_id, $order_id, $p->product_id, $qty, $p->price]);
            
            $stm3->execute([$qty, $p->product_id, $qty]);
           
            if ($stm3->rowCount() == 0) {
                throw new Exception("Sorry, " . $p->product_name . " is out of stock or does not have enough quantity!");
            }
        }

        $_db->commit();
        set_cart([]);
        unset($_SESSION['shipping_address']);

        } catch (Exception $e) {
        $_db->rollBack();
        die("Database Error: " . $e->getMessage()); 
        }
    }

$_title = 'Payment Successful';
require '_head.php'; 
?>

<div style="text-align: center; margin: 100px auto; max-width: 600px;">
    <div style="font-size: 80px; color: #4CAF50; margin-bottom: 20px;">✅</div>
    <h1 style="color: #333; margin-bottom: 10px;">Payment Successful!</h1>
    
    <?php if ($order_id): ?>
        <p style="font-size: 18px; color: #555;">Your Order ID is: <strong><?= $order_id ?></strong></p>
    <?php endif; ?>
    
    <p style="color: #666; font-size: 16px; margin-bottom: 30px;">
        Thank you for your purchase. Your payment has been processed securely via Stripe.
    </p>
    
    <a href="user_order/order_history.php" style="background-color: #635bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
        View Order History
    </a>
</div>

<?php require '_foot.php'; ?>
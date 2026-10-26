<?php
require '_base.php';

$domain_url = 'http://localhost:8000';


$cart = get_cart();

if (empty($cart)) {
    redirect('/product/list.php'); 
}
$line_items = [];
foreach ($cart as $product_id => $unit) {

    $stm = $_db->prepare("SELECT * FROM products WHERE product_id = ?");
    $stm->execute([$product_id]);
    $p = $stm->fetch();

    if ($p) {
        $line_items[] = [
            'price_data' => [
                'currency' => 'myr', 
                'product_data' => [
                    'name' => $p->product_name, 
                ],
                
                'unit_amount' => round($p->price * 100), 
            ],
            'quantity' => $unit, 
        ];
    }
}


try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'], 
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => $domain_url . '/checkout_success.php', 
        'cancel_url' => $domain_url . '/order/cart.php', 
    ]);


    header("HTTP/1.1 303 See Other");
    header("Location: " . $checkout_session->url);
    exit();

} catch (Exception $e) {
    echo "Stripe Error: " . $e->getMessage();
}
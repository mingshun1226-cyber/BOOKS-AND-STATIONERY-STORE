
<?php

// php setup
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

//  for save and delete photo
define('PRODUCT_PHOTO_PATH', __DIR__ . '/image/product_photos');
define('USER_PHOTO_PATH', __DIR__ . '/image/user_photos');

define('PRODUCT_PHOTO_DIR', '/image/product_photos');
define('USER_PHOTO_DIR', '/image/user_photos');
define('BASE_URL', '');


require_once __DIR__ . '/lib/stripe/init.php';


$_stripe_publishable_key = 'pk_test_51TKxYsKeQ6X4R5DuZKNlwHxsHVWWiG7uOVA9Nlyg9rJ1iIHJ8WRVY77Zxz0MGBWGDflvXJ26zKLiEgQ03DmzYvEu00Efd8gcuC';
$_stripe_secret_key      = 'sk_test_51TKxYsKeQ6X4R5DuGwNkdA4iagh36VfNzamMTRyZZUG6trsZJFDLq4Q0Zc1yMihtBtfmqg3HJjOxfc52KQg7SiF600SeDmjzUg';


\Stripe\Stripe::setApiKey($_stripe_secret_key);



// General Page Functions


// Is GET request?
function is_get() {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post() {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null) {
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null) {
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null) {
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null) {
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null) {
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    }
    else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// Obtain uploaded file --> cast to object
function get_file($key) {
    $f = $_FILES[$key] ?? null;
    
    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}

// Crop, resize and save photo
function save_photo($f, $folder, $width = 200, $height = 200) {
    $photo = uniqid() . '.jpg';

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }
    
    require_once __DIR__ . '/lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width, $height)
        ->toFile($folder . '/' . $photo, 'image/jpeg');

    return $photo;
}

// Is money?
function is_money($value) {
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}

// Is email?
function is_email($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

// ============================================================================
// HTML Helpers
// ============================================================================

// Encode HTML special characters
function encode($value) {
    return htmlentities($value);
}

// Generate <input type='text'>
function html_text($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='password'>
function html_password($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='number'>
function html_number($key, $min = '', $max = '', $step = '', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' id='$key' name='$key' value='$value'
                 min='$min' max='$max' step='$step' $attr>";
}

// Generate <input type='search'>
function html_search($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='search' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false) {
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate <input type='file'>
function html_file($key, $accept = '', $attr = '') {
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

// Generate table headers <th>
function table_headers($fields, $sort, $dir, $href = '') {
    foreach ($fields as $k => $v) {
        $d = 'asc'; // Default direction
        $c = '';    // Default class
        
        if ($k == $sort) {
            $d = $dir == 'asc' ? 'desc' : 'asc';
            $c = $dir;
        }

        echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v</a></th>";
    }
}

// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = [];

// Generate <span class='err'>
function err($key) {
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class='err'>$_err[$key]</span>";
    }
    else {
        echo '<span></span>';
    }
}


// Security
//////////////////
// Global user object
$_users = $_SESSION['users'] ?? null;

// Login user
function login($users, $url = '/') {
    $_SESSION['users'] = $users;
    redirect($url);
}

// Logout user
function logout($url = '/') {
    unset($_SESSION['users']);
    redirect($url);
}

// Authorization
function auth(...$roles) {
    global $_users;
    if ($_users) {
        if ($roles) {
            if (in_array($_users->role, $roles)) {
                return; // OK
            }
        }
        else {
            return; // OK
        }
    }
    
    redirect('/login.php');
}

// ============================================================================
// Database Setups and Functions
// ============================================================================

// Global PDO object
$_db = new PDO("mysql:host=localhost;dbname=books_stationery_db", 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

// Is unique?
function is_unique($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

// ============================================================================
// Shopping Cart Functions
// ============================================================================

// Get shopping cart
function get_cart() {
    return $_SESSION['cart'] ?? [];
}

// Set shopping cart
function set_cart($cart = []) {
    $_SESSION['cart'] = $cart;
}

// Update shopping cart 
function update_cart($id, $unit) {
    global $_db; // connect database
    $cart = get_cart();

    //db check this product in stock
    $stm = $_db->prepare('SELECT stock, product_name FROM products WHERE product_id = ?');
    $stm->execute([$id]);
    $product = $stm->fetch();

    if ($product && $unit >= 1) {
        
        if ($unit > $product->stock) {
            $cart[$id] = $product->stock;
            temp('info', "Oops! Only {$product->stock} unit(s) of '{$product->product_name}' are available in stock.");
        } else {
            $cart[$id] = $unit;
        }

    } else {
        unset($cart[$id]);
    }

    set_cart($cart);
    ksort($cart); 
}


// ============================================================================
// Create ramdom ID
// ============================================================================
function generate_id($prefix = 'ADM') {
    return $prefix . '-' . strtoupper(bin2hex(random_bytes(4)));
}
$_units=array_combine(range(1,99),range(1,99));

// ============================================================================
// Email Sending Function (PHPMailer)
// ============================================================================

// Tell PHP where to find the PHPMailer files
require_once 'lib/PHPMailer.php';
require_once 'lib/SMTP.php';
require_once 'lib/Exception.php';



function get_mail($from_name = 'Books Store') {
    $m = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $m->isSMTP();
        $m->Host = 'smtp.gmail.com';
        $m->SMTPAuth = true;
        $m->Username = '4boyslibrary@gmail.com';
        $m->Password = 'aennkcjspynpalto';
        $m->SMTPSecure = 'tls';
        $m->Port = 587;

        $m->setFrom('4boyslibrary@gmail.com', $from_name);

    } catch (Exception $e) {
        echo "Mailer Error: {$m->ErrorInfo}";
    }

    return $m;
}
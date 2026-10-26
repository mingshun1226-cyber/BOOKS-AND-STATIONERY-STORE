
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'Books & Stationery Store' ?></title>
    <link rel="stylesheet" href="/css/app.css">
    
    <style>
    
        body {
            font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        /* 1. Top Black Utility Bar */
        .top-bar {
            background-color: #111;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 40px;
            font-size: 13px;
        }
        
        /* 2. Main Header */
        .main-header {
            display: flex;
            align-items: center;
            padding: 20px 40px;
            background: #fff;
            gap: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: 900;
            color: #0058a3; 
            text-decoration: none;
            letter-spacing: -1px;
            min-width: max-content;
        }
        .logo span {
            color: #ffcc00; 
        }

        .main-nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .main-nav a {
            text-decoration: none;
            color: #111;
            font-weight: 600;
            font-size: 14px;
        }
        .main-nav a:hover { text-decoration: underline; }

        [data-theme="dark"] body {
            background-color: #121212 !important;
            color: #f1f1f1 !important;
        }

        [data-theme="dark"] main {
            background-color: #121212 !important;
            color: #f1f1f1 !important;
        }

        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6,
        [data-theme="dark"] p,
        [data-theme="dark"] label,
        [data-theme="dark"] span,
        [data-theme="dark"] strong,
        [data-theme="dark"] td,
        [data-theme="dark"] th,
        [data-theme="dark"] li {
            color: #f1f1f1 !important;
        }

        [data-theme="dark"] table {
            background-color: #1e1e1e !important;
            color: #f1f1f1 !important;
        }

        [data-theme="dark"] th {
            background-color: #2a2a2a !important;
            color: #ffffff !important;
            border-color: #444 !important;
        }

        [data-theme="dark"] td {
            background-color: #1e1e1e !important;
            color: #f1f1f1 !important;
            border-color: #333 !important;
        }

        [data-theme="dark"] tr {
            border-color: #333 !important;
        }

        [data-theme="dark"] hr {
            border-color: #333 !important;
        }

        [data-theme="dark"] input,
        [data-theme="dark"] textarea,
        [data-theme="dark"] select {
            background-color: #2a2a2a !important;
            color: #ffffff !important;
            border: 1px solid #444 !important;
        }

        [data-theme="dark"] input::placeholder,
        [data-theme="dark"] textarea::placeholder {
            color: #aaaaaa !important;
        }

        [data-theme="dark"] .flash-msg {
            background: #1f3a1f !important;
            color: #c8f7c5 !important;
            border-color: #2e5c2e !important;
        }

        /* common white card/container fix */
        [data-theme="dark"] .container,
        [data-theme="dark"] .product-card,
        [data-theme="dark"] .order-card,
        [data-theme="dark"] .summary-box,
        [data-theme="dark"] .form-box {
            background-color: #1e1e1e !important;
            color: #f1f1f1 !important;
            border-color: #333 !important;
            box-shadow: none !important;
        }

        /* generic inline white background fix */
        [data-theme="dark"] [style*="background: white"],
        [data-theme="dark"] [style*="background-color: white"],
        [data-theme="dark"] [style*="background: #fff"],
        [data-theme="dark"] [style*="background-color: #fff"],
        [data-theme="dark"] [style*="background: #f8f9fa"],
        [data-theme="dark"] [style*="background-color: #f8f9fa"] {
            background: #1e1e1e !important;
            color: #f1f1f1 !important;
            border-color: #333 !important;
        }
        
        .admin-badge {
            background: #ffcc00;
            color: #111 !important;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px !important;
        }

        /* 3. Search Box */
        .search-container {
            flex-grow: 1;
        }
        .search-container form {
            display: flex;
            background: #f5f5f5;
            border-radius: 50px;
            padding: 4px 20px;
            align-items: center;
            border: 1px solid transparent;
        }
        .search-container form:focus-within {
            border-color: #0058a3;
            background: #fff;
        }
        .search-container input {
            border: none;
            background: transparent;
            padding: 10px;
            width: 100%;
            font-size: 15px;
            outline: none;
        }
        .search-container button {
            background: transparent;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }

        /* 4. Right Side Icons */
        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
            min-width: max-content;
        }
        .header-icons a {
            text-decoration: none;
            color: #111;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }
        .icon-btn { font-size: 20px; }

        /* 5. Category Bar ) */
        .category-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 40px 20px 40px;
            overflow-x: auto;
            white-space: nowrap;
            border-bottom: 1px solid #dfdfdf;
            scrollbar-width: none;
            background-color: #fff; 
        }
        .category-bar::-webkit-scrollbar { display: none; }
        
        .cat-btn {
            display: inline-block;
            text-decoration: none;
            color: #111;
            font-weight: 700; 
            font-size: 13px;
            padding: 10px 16px;
            border: 1px solid #dfdfdf; 
            border-radius: 4px; 
            background-color: #fff;
            transition: all 0.2s;
        }
        .cat-btn:hover { border-color: #929292; }
        
        .cat-btn.active {
            border: 2px solid #0058a3;
            padding: 9px 15px; 
        }

        /* Dark Mode Overrides */
        [data-theme="dark"] .main-header, 
        [data-theme="dark"] .category-bar,
        [data-theme="dark"] header {
            background: #1e1e1e !important;
            border-color: #333;
        }
        [data-theme="dark"] .main-nav a, 
        [data-theme="dark"] .header-icons a, 
        [data-theme="dark"] .cat-btn {
            color: #f1f1f1;
        }
        [data-theme="dark"] .search-container form {
            background: #2a2a2a;
            border-color: #444;
        }
        [data-theme="dark"] .search-container input { color: #fff; }
        [data-theme="dark"] .cat-btn {
            background: #1e1e1e;
            border-color: #444;
        }
        [data-theme="dark"] .cat-btn:hover { border-color: #888; }
        [data-theme="dark"] .cat-btn.active { border-color: #4fa3e3; }

      
        /*style for flash msg*/ 
        .flash-msg {
            background: #dff0d8;
            color: #3c763d;
            border: 1px solid #c3e6cb;
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 600;
        }

        /*red dot and num*/
        .notif-badge {
            display: inline-block;
            min-width: 18px;
            height: 18px;
            background: #FF3737;
            color: white;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            line-height: 18px;
            border-radius: 50%;
            margin-left: 6px;
            padding: 0 5px;
        }

        button {
            transition: 0.2s;
        }

        .btn-approve {
            background-color: #28a745; 
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-approve:hover { background-color: #2fce51; }

        .btn-reject {
            background-color: #cf182a; 
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-reject:hover { background-color: #c82333; }

        .admin-mini-menu {
            display: flex;
            flex-direction: row;   
            gap: 8px;
            margin-top: 6px;
        }

        .admin-mini-menu a {
            font-size: 12px;
            color: #111;
            text-decoration: none;
            background: #ffcc00;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: 600;
        }
        .admin-mini-menu a:hover {
            text-decoration: underline;
        }
    </style>
</head>



<body>
    <header style="display: block; width: 100%; margin: 0; padding: 0;">
       
        <div class="top-bar">
            <div>MY | English</div>
            <div>📦 4 BOYS BOOK <span>STATIONERY</span></div>
            <div>
                <button id="theme-toggle" style="cursor:pointer; background:#333; color:white; border:1px solid #1d4eef; 
                border-radius:4px; padding:4px 10px; font-size:12px;">🌙 Dark Mode</button>
            </div>
        </div>

        <div class="main-header">
            <a href="/homepage.php" class="logo">4 BOYS BOOK <span>STATIONERY</span></a>

            <?php  //if request(admin) =pending remind admin2 
                $pending_count = 0;
            if (isset($_users) && $_users && $_users->role === 'admin') {
                $stm = $_db->query("SELECT COUNT(*) FROM admin_request WHERE status = 'pending'");
                $pending_count = $stm->fetchColumn();
                }
            ?>

            <nav class="main-nav">
                <a href="/homepage.php">Home</a>
                <a href="/product/list.php">Products</a>
            </nav>
            
            <a href="/admin/contact_admin.php" style="background-color: #0058a3; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; text-decoration: none; display: flex; align-items: center; gap: 5px;">
                Contact Admin
                </a>

            <div class="search-container">    
                <form action="/product/list.php" method="GET">
                    <button type="submit">🔍</button>
                    <input type="text" name="search" placeholder="What are you looking for?">
                </form>
            </div>

            <div class="header-icons">
                <?php if (!isset($_users) || !$_users): ?>
                    <a href="/login.php"><span class="icon-btn">👤</span> Hey! Log in</a>
                    <a href="/user/register.php">Register</a>
                <?php else: ?>
                    <div style="display: flex; flex-direction: row; gap: 10px; align-items: center;">
                        <a href="/user/profile.php" style="font-size: 13px;"><span class="icon-btn">👤</span> Profile</a>
                        <a href="/user_order/order_history.php" style="font-size: 13px;"><span class="icon-btn">📦</span> My Orders</a>
                        <a href="/logout.php" style="font-size: 12px; color: #d9534f;">Logout🚪🚶</a>
                    </div>
                    <?php if ($_users->role === 'admin'): ?>
                    <div class="admin-mini-menu">
                            <a href="/admin/admin_list.php">Admin Maintain</a>
                            <a href="/productlist.php">Manage Products</a>
                            <a href="/admin_order/order_list.php">Manage Orders</a>
                            <a href="/admin/admin_view.php">Admin View</a>
                            <a href="/admin/admin_request_list.php">Requests
                            <?php if ($pending_count > 0): ?>
                            <span class="notif-badge"><?= $pending_count ?></span>
                        <?php endif; ?>
                         </a>
                    </div>
            <?php endif; ?>
                    <a href="/chat.php" title="Chat"><span class="icon-btn">💬</span></a>
                    <a href="/wishlist.php" title="Wishlist"><span class="icon-btn">❤️</span></a>
                <?php endif; ?>
                <a href="/order/cart.php"><span class="icon-btn">🛒</span></a>
            </div>
        </div>

<div class="category-bar">
            <?php $current_cat = $_GET['category_id'] ?? ''; ?>
            
            <a href="/product/list.php" 
               class="cat-btn <?= empty($current_cat) ? 'active' : '' ?>">
               All Products
            </a>
            
            <a href="/product/list.php?category_id=1" 
               class="cat-btn <?= $current_cat == '1' ? 'active' : '' ?>">
               Stationery & Writing
            </a>
            
            <a href="/product/list.php?category_id=2" 
               class="cat-btn <?= $current_cat == '2' ? 'active' : '' ?>">
               Office Supplies
            </a>
            
            <a href="/product/list.php?category_id=3" 
               class="cat-btn <?= $current_cat == '3' ? 'active' : '' ?>">
               Art Materials
            </a>
        </div>
        
    </header> 
    
    <script>
        const toggleBtn = document.getElementById('theme-toggle');
        const currentTheme = localStorage.getItem('theme') || 'light';
        
        if (currentTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            toggleBtn.innerText = '☀️ Light Mode';
        }

        toggleBtn.addEventListener('click', () => {
            let theme = document.documentElement.getAttribute('data-theme');
            if (theme === 'dark') {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                toggleBtn.innerText = '🌙 Dark Mode';
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                toggleBtn.innerText = '☀️ Light Mode';
            }
        });
    </script>

    <main style="padding: 20px 40px;">
        <?php if ($msg = temp('info')): ?>
            <div class="flash-msg"><?= $msg ?></div>
        <?php endif; ?>
<?php
require '_base.php';
auth('admin');

// -------------------------------------------------------------
// YOUR PHP LOGIC GOES HERE (Getting the search text, 
// fetching $arr from the database, etc.)
// -------------------------------------------------------------
$search = req('search');

if ($search) {
    $stm = $_db->prepare("SELECT * FROM products WHERE product_name LIKE ? OR product_id LIKE ? ORDER BY product_id DESC");
    $stm->execute(["%$search%", "%$search%"]);
} else {
    $stm = $_db->prepare("SELECT * FROM products ORDER BY product_id DESC");
    $stm->execute();
}
$arr = $stm->fetchAll();

// -------------------------------------------------------------

$_title = 'Product List';
require '_head.php';
?>

<style>
    /* Toolbar for Search and Add Button */
    .toolbar {
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        margin-bottom: 20px; 
        flex-wrap: wrap; 
        gap: 15px;
    }
    .toolbar input[type="text"], .toolbar input[type="search"] {
        padding: 8px 15px; 
        border: 1px solid rgba(128, 128, 128, 0.4);
        border-radius: 20px; 
        background-color: transparent; 
        color: inherit;
        font-size: 15px; 
        width: 250px; 
        outline: none;
    }
    .toolbar input:focus { border-color: #3498db; }
    
    .btn-add {
        background-color: #2ecc71; 
        color: white; 
        padding: 10px 15px;
        border: none; 
        border-radius: 5px; 
        font-weight: bold;
        text-decoration: none; 
        transition: filter 0.3s;
    }
    .btn-add:hover { filter: brightness(0.9); }

    /* Modern Table Styling */
    .product-table {
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
        border-radius: 8px; overflow: hidden;
    }
    .product-table th {
        background-color: rgba(128, 128, 128, 0.1); 
        padding: 15px;
        text-align: left; 
        font-weight: 600; 
        border-bottom: 2px solid rgba(128, 128, 128, 0.2);
    }
    .product-table td {
        padding: 12px 15px; 
        vertical-align: middle; 
        border-bottom: 1px solid rgba(128, 128, 128, 0.1);
    }
    .product-table tr:hover { background-color: rgba(128, 128, 128, 0.05); }

    .table-photo {
        width: 60px; 
        height: 60px; 
        object-fit: cover; 
        border-radius: 8px;
        border: 1px solid rgba(128,128,128,0.2); 
        display: block;
    }

    /* Action Buttons */
    .btn-action{
        padding: 6px 12px; 
        border-radius: 4px; 
        text-decoration: none;
        font-size: 13px; 
        font-weight: bold; 
        color: white; 
        margin-right: 15px;
    }
    .btn-update { background-color: #55e591ad; }
    .btn-delete-sm { background-color: #e74c3c; }
</style>

<div class="container" style="max-width: 1000px; 
margin: 20px auto; 
padding: 20px;">
    <h2>Product List</h2>

    <div class="toolbar">
        <form method="get">
            <?= html_search('search', 'placeholder="Search products..."') ?>
            <button type="submit" style="display:none;">Search</button>
        </form>
        
        <a href="productadd.php" class="btn-add">+ Add New Product</a>
    </div>

    <p><?= count($arr) ?> record(s)</p>

    <table class="product-table">
        <thead>
            <tr>
                <th>Photo</th>
                <th>Product ID</th>
                <th>Product Name and Detail</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($arr as $p): ?>
            <tr>
                <td><img src="/image/product_photos/<?= $p->photo ?>" class="table-photo"></td>
                <td><?= $p->product_id ?></td>
                <td><?= $p->product_name ?></td>
                <td>RM <?= number_format($p->price, 2) ?></td>
                <td style="text-align: center; vertical-align: middle;">
                    <?php if ($p->stock <= 0): ?>
                        <span style="color: white; background-color: #dc3545; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.9em;">
                        Out of Stock
                        </span>
                    <?php elseif ($p->stock <= 5): ?>
                        <span style="color: #856404; background-color: #fff3cd; border: 1px solid #ffeeba; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.9em;">
                    <?= $p->stock ?> (Low 🚨)
                        </span>
                    <?php else: ?>
                        <span style="color: #28a745; font-weight: bold;">
                    <?= $p->stock ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="productupdate.php?id=<?= $p->product_id ?>" class="btn-action btn-update">Update</a>
                    <a href="productdelete.php?id=<?= $p->product_id ?>" 
                       class="btn-action btn-delete-sm" 
                       style="margin-left: 10px;" 
                       onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require '_foot.php'; ?>
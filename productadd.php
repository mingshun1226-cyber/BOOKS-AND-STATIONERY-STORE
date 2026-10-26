<?php
require '_base.php';
auth('admin');

$stm_cat = $_db->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
$categories = $stm_cat->fetchAll();

if (is_post()) {
   
    // ACTION: Add New Product Form Submission
    $id    = req('id');
    $name  = req('name');
    $price = req('price');
    $stock = req('stock');
    $f     = get_file('photo');
    $category_id = req('category_id');

    //validation
    if ($id !== null) {
        if (!$id) $_err['id'] = 'Required';
        else if (!is_unique($id, 'products', 'product_id')) $_err['id'] = 'Duplicate ID';
        if (!$name) $_err['name'] = 'Required';
        if (!$price) $_err['price'] = 'Required';
        else if (!is_money($price)) $_err['price'] = 'Invalid price format';
        if ($stock === '') $_err['stock'] = 'Required';
        if (!$category_id) $_err['category_id'] = 'Required';
        if (!$f) $_err['photo'] = 'Photo Required';

if (!$_err) {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $photo_name = uniqid() . '.' . $ext; 
            move_uploaded_file($_FILES['photo']['tmp_name'], PRODUCT_PHOTO_PATH . '/' . $photo_name);
            $stm = $_db->prepare("
            INSERT INTO products (product_id, product_name, price, stock, category_id, photo)
            VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stm->execute([$id, $name, $price, $stock, $category_id, $photo_name]);
            
            temp('info', 'Product added successfully');
            redirect('productlist.php');
        }
    }
}



$_title = 'Add Product & Gallery';
require '_head.php';
?>

<style>
    /* Main Container */
    .container {
        max-width: 800px;
        margin: 40px auto;
        padding: 30px 40px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        border: 1px solid rgba(128, 128, 128, 0.2);
    }

    h2 {
        color: inherit;
        border-bottom: 2px solid rgba(128, 128, 128, 0.2);
        padding-bottom: 10px;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 20px;
    }
    
    label {
        font-weight: 600;
        display: block;
        margin-bottom: 8px;
        color: inherit;
    }

    input[type="text"], 
    input[type="number"], 
    input[type="file"] {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid rgba(128, 128, 128, 0.4);
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
        background-color: transparent;
        color: inherit;
        transition: border-color 0.3s;
    }

    input[type="text"]:focus, 
    input[type="number"]:focus {
        border-color: #3498db;
        outline: none;
    }

    .err {
        color: #ff6b6b;
        font-size: 14px;
        margin-top: 5px;
        display: block;
    }

    button {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        font-weight: bold;
        transition: filter 0.3s;
    }

    button:hover { filter: brightness(0.8); }
    .btn-submit { background-color: #3498db; color: white; }
    .btn-cancel { background-color: #7f8c8d; color: white; margin-left: 10px; }

    
    /* Drag & Drop Area Styling */
    .drop-area {
        width: 100%; min-height: 180px; border: 3px dashed #333;
        background: #fff !important; color: #000 !important;
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; cursor: pointer; position: relative; border-radius: 5px;
    }
    .drop-area:hover, .drop-area.drag-over { background: #e2e8f0 !important; border-color: #3498db; }
    .drop-text { text-align: center; pointer-events: none; }
</style>

<div class="container">
    <h2>Add New Product</h2>
    
    <form method="post" class="form" enctype="multipart/form-data">
        
        <div class="form-group">
            <label>Product ID</label>
            <?= html_text('id') ?> 
            <?= err('id') ?>
        </div>

        <div class="form-group">
            <label>Product Name</label>
            <?= html_text('name') ?> 
            <?= err('name') ?>
        </div>

        <div class="form-group">
            <label>Price (RM)</label>
            <?= html_number('price', '0.00', '9999.99', '0.01') ?> 
            <?= err('price') ?>
        </div>

        <div class="form-group">
            <label>Stock</label>
            <?= html_number('stock', '0', '9999', '1') ?> 
            <?= err('stock') ?>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category_id" style="width: 100%; padding: 10px 15px; border: 1px solid rgba(128, 128, 128, 0.4); border-radius: 5px; font-size: 16px; box-sizing: border-box; background-color: transparent; color: inherit;">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c->category_id ?>" <?= req('category_id') == $c->category_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c->category_name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?= err('category_id') ?>
        </div>

        <div class="form-group">
            <label>Photo Upload</label>
            <div id="drop-zone" class="drop-area">
                <span class="drop-text">Drag & Drop photo here<br>or Click to browse</span>
                
                <?= html_file('photo', 'image/*', 'hidden') ?>
                
                <img id="img-preview" src="" style="display:none; max-width:150px; margin-top:10px; border-radius:5px;">
            </div>
            <div class="err"><?= err('photo') ?></div>
        </div>

        <div style="margin-top: 30px;">
            <button class="btn-submit">Submit</button>
            <a href="productlist.php"><button type="button" class="btn-cancel">Cancel</button></a>
        </div>
    </form>

    <hr style="margin: 50px 0; border: 0; border-top: 1px solid rgba(128, 128, 128, 0.2);">

    
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('drop-zone');
    // base.php automatically sets the ID of the file input to 'photo'
    const photoInput = document.getElementById('photo'); 
    const imgPreview = document.getElementById('img-preview');
    const dropText = document.querySelector('.drop-text');

    if (!dropZone || !photoInput) {
        console.error("Drag-and-drop elements not found.");
        return;
    }

    // Click to upload
    dropZone.addEventListener('click', () => photoInput.click());

    // Prevent default browser behavior
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, e => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    // Highlight area on drag
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-over'));
    });
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-over'));
    });

    // Handle dropped file
    dropZone.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            photoInput.files = files; // Assign files to the hidden input
            showPreview(files[0]);
        }
    });

    // Handle clicked file
    photoInput.addEventListener('change', () => {
        if (photoInput.files.length > 0) {
            showPreview(photoInput.files[0]);
        }
    });

    function showPreview(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                imgPreview.src = e.target.result;
                imgPreview.style.display = 'block';
                if (dropText) dropText.style.display = 'none'; 
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>

<?php require '_foot.php'; ?>
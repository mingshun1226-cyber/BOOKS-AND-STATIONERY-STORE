<?php
require '_base.php';
auth('admin');


$id = req('id');

if (is_get()) {
    $stm = $_db->prepare("SELECT * FROM products WHERE product_id = ?");
    $stm->execute([$id]);
    $p = $stm->fetch();

   
    if (!$p) {
        redirect('productlist.php');
    }

    $GLOBALS['name']  = $p->product_name;
    $GLOBALS['price'] = $p->price;
    $GLOBALS['stock'] = $p->stock;
    
    // Save the old photo name in the session so we remember it when the form submits
    $_SESSION['old_photo'] = $p->photo;
}

// If the form is submitted (POST request)
if (is_post()) {
    $name  = req('name');
    $price = req('price');
    $stock = req('stock');
    $f     = get_file('photo'); 
    
    
    $photo = $_SESSION['old_photo']; 

    // Validation
    if (!$name) $_err['name'] = 'Required';
    
    if (!$price) $_err['price'] = 'Required';
    else if (!is_money($price)) $_err['price'] = 'Invalid price format';
    
    if ($stock === '') $_err['stock'] = 'Required';

    // If no errors, update the database
    if (!$_err) {
        
        // Did the user upload a NEW photo?
        if ($f) {
            // Delete the OLD photo from the server
            if ($photo) {
                $old_path = PRODUCT_PHOTO_PATH . '/' . $photo;
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
            // Save the NEW photo and get its generated name
            $photo = save_photo($f, PRODUCT_PHOTO_PATH);
        }

        // Update the database record
        $stm = $_db->prepare("UPDATE products SET product_name = ?, price = ?, stock = ?, photo = ? WHERE product_id = ?");
        $stm->execute([$name, $price, $stock, $photo, $id]);

        // Clean up the session and redirect
        unset($_SESSION['old_photo']);
        temp('info', 'Product updated successfully');
        redirect('productlist.php');
    }
}

$_title = 'Update Product';
require '_head.php';
?>

<style>
    /* Main Container (Matches your Add Page) */
    .container {
        max-width: 800px; margin: 40px auto; padding: 30px 40px;
        border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        border: 1px solid rgba(128, 128, 128, 0.2);
    }
    h2 {
        color: inherit; border-bottom: 2px solid rgba(128, 128, 128, 0.2);
        padding-bottom: 10px; margin-bottom: 25px;
    }
    .form-group { margin-bottom: 20px; }
    label { font-weight: 600; display: block; margin-bottom: 8px; color: inherit; }

    /* Inputs */
    input[type="text"], input[type="number"], input[type="file"] {
        width: 100%; padding: 10px 15px; border: 1px solid rgba(128, 128, 128, 0.4);
        border-radius: 5px; font-size: 16px; box-sizing: border-box;
        background-color: transparent; color: inherit; transition: border-color 0.3s;
    }
    input[type="text"]:focus, input[type="number"]:focus { border-color: #3498db; outline: none; }
    
    .err { color: #ff6b6b; font-size: 14px; margin-top: 5px; display: block; }

    /* Buttons */
    button {
        padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px;
        cursor: pointer; font-weight: bold; transition: filter 0.3s; color: white;
    }
    button:hover { filter: brightness(0.8); }
    .btn-submit { background-color: #61e89b; } 
    .btn-cancel { background-color: #7f8c8d; margin-left: 10px; }
    .photo-note { font-size: 13px; opacity: 0.7; margin-top: 5px; }

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
    <h2>Update Product: <?= htmlspecialchars($id) ?></h2>
    
    <form method="post" class="form" enctype="multipart/form-data">
        
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
            <label>Product Photo</label>
            
            <div id="drop-zone" class="drop-area">
                <?php 
                    // Check if a photo currently exists for this product
                    $has_photo = isset($_SESSION['old_photo']) && $_SESSION['old_photo']; 
                ?>
                
                <span class="drop-text" style="<?= $has_photo ? 'display: none;' : '' ?>">
                    Drag & Drop new photo here<br>or Click to browse
                </span>
                
                <?= html_file('photo', 'image/*', 'hidden') ?>
                
                <img id="img-preview" src="<?= $has_photo ? 'photos/' . $_SESSION['old_photo'] : '' ?>" 
                     style="<?= $has_photo ? 'display:block;' : 'display:none;' ?> max-width:150px; margin-top:10px; border-radius:5px; object-fit:cover;">
            </div>
        
            <div class="err"><?= err('photo') ?></div>
        </div>

        <div style="margin-top: 30px;">
            <button class="btn-submit">Update Product</button>
            <a href="productlist.php"><button type="button" class="btn-cancel">Cancel</button></a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('drop-zone');
    const photoInput = document.getElementById('photo'); 
    const imgPreview = document.getElementById('img-preview');
    const dropText = document.querySelector('.drop-text');

    if (!dropZone || !photoInput) return;

    // 1. Click to upload
    dropZone.addEventListener('click', () => photoInput.click());

    // 2. Prevent default browser behavior
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, e => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    // 3. Highlight area on drag
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-over'));
    });
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-over'));
    });

    // 4. Handle dropped file
    dropZone.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            photoInput.files = files; // Assign files to the hidden input
            showPreview(files[0]);
        }
    });

    // 5. Handle clicked file
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
                if (dropText) dropText.style.display = 'none'; // Hide text when image is shown
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>

<?php require '_foot.php'; ?>
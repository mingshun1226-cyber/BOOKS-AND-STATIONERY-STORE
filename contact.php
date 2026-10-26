<?php
require '_base.php';
$_title = 'Contact Us';
require '_head.php';
?>

<div style="max-width: 900px; margin: 40px auto; padding: 20px;">
    
    <h1 style="text-align: center; margin-bottom: 30px;">Visit Our Store</h1>

    <div style="display: flex; gap: 40px; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 300px; background: rgba(128,128,128,0.05); padding: 30px; border-radius: 8px; border: 1px solid rgba(128,128,128,0.2);">
            <h3>Contact Information</h3>
            <p><strong>📍 Address:</strong><br>
            5P56+5W3 Kuala Lumpur,<br> 
            Federal Territory of <br>
            Kuala Lumpur,Malaysia</p>
            
            <p><strong>📞 Phone:</strong> +011 36209334</p>
            <p><strong>✉️ Email:</strong> 4boyslibrary@gmail.com</p>
            
            <h3 style="margin-top: 30px;">Opening Hours</h3>
            <p><strong>Mon - Fri:</strong> 9:00 AM - 8:00 PM</p>
            <p><strong>Saturday:</strong> 10:00 AM - 6:00 PM</p>
            <p><strong>Sunday &nbsp&nbsp&nbsp:</strong> Closed</p>
        </div>

        <div style="flex: 2; min-width: 300px;">
            
            <iframe 
                src="https://www.google.com/maps?q=3.1579,101.7123&z=17&output=embed"
                width="100%" 
                height="450" 
                style="border:0; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>

        </div>
    </div>
</div>

<?php require '_foot.php'; ?>
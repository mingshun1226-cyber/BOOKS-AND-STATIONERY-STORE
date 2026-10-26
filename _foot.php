</main>

    <footer>
        Developed by <b>4boys</b> &middot;
        Copyrighted &copy; <?= date('Y') ?>
        <br><br>
        <a href="../contact.php" style="color: #007bff; text-decoration: none; font-size: 16px;">📍 Store Location</a>
    </footer>

    <style>
    footer {
        text-align: center;
        font-size: 18px;          
        padding: 20px 0;
        color: #555;
        border-top: 1px solid #ddd;
        margin-top: 40px;
    }
    </style>
    
</body>
</html>
<script>
    setTimeout(() => {
        const msg = document.querySelector('.flash-msg');
        if (msg) {
            msg.style.transition = 'all 0.5s';
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(-10px)';

            setTimeout(() => {
                msg.remove();
            }, 500);
        }
    }, 2000);
</script>
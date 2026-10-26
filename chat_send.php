<?php
require '_base.php';
auth(); 

if (is_post()) {
    $message = trim($_POST['message'] ?? '');
    
    if ($message !== '') {
     
        // Save the user's message to the database
        $stm = $_db->prepare("INSERT INTO chat_messages (user_id, message) VALUES (?, ?)");
        $stm->execute([$_users->id, $message]);

        // If the person sending the message is NOT an admin, send an auto-reply
        if ($_users->role !== 'admin') {
            
            $bot_reply = "Thank you for contacting Books & Stationery Store! 📚 An admin has been notified and will reply to you shortly. Our office hours are 9am - 6pm.";
            
            $admin_stm = $_db->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
            $admin = $admin_stm->fetch();
            
            if ($admin) {
                $stm = $_db->prepare("INSERT INTO chat_messages (user_id, message) VALUES (?, ?)");
                $stm->execute([$admin->id, $bot_reply]);
            }
        }
    }
}
?>
<?php
require '_base.php';
auth();

// Fetch the last 50 messages, joined with the users table to get the sender's name
$stm = $_db->query("
    SELECT c.message, c.created_at, u.name, u.id as sender_id 
    FROM chat_messages c 
    JOIN users u ON c.user_id = u.id 
    ORDER BY c.id ASC 
    LIMIT 50
");
$messages = $stm->fetchAll();

// Output as HTML so JavaScript can just drop it into the chat box
foreach ($messages as $msg) {
    // Make your own messages look different from others
    $is_me = ($msg->sender_id == $_users->id);
    $color = $is_me ? "blue" : "green";
    $name = $is_me ? "You" : htmlspecialchars($msg->name);
    
    // FIX: Combined into a single line so the message doesn't print twice
    echo "<div style='margin-bottom: 5px;'>";
    echo "<b style='color: {$color}'>{$name}:</b> ";
    echo "<span style='color: #333;'>" . htmlspecialchars($msg->message) . "</span>";
    echo "<span style='font-size: 0.8em; color: gray; margin-left: 10px;'>(" . date('H:i', strtotime($msg->created_at)) . ")</span>";
    echo "</div>";
}
?>
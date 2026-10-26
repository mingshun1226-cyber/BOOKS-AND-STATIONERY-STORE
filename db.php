<?php
$pdo = new PDO("mysql:host=localhost;dbname=books_stationery_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
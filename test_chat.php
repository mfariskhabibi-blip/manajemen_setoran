<?php
$db = new PDO('mysql:host=localhost;dbname=setoran_db', 'root', '');
$stmt = $db->query('SELECT * FROM chats');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

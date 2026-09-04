<?php
// Simple script to create default group and add all users using direct DB access
$host = 'localhost';
$dbname = 'manajemen_setoran';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if default group exists
    $stmt = $pdo->prepare("SELECT * FROM groups WHERE nama_grup = ?");
    $stmt->execute(['Grup Diskusi Warga']);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$group) {
        // Create default group
        $stmt = $pdo->prepare("INSERT INTO groups (nama_grup, deskripsi, created_at) VALUES (?, ?, ?)");
        $stmt->execute(['Grup Diskusi Warga', 'Grup diskusi untuk seluruh warga yang terdaftar', date('Y-m-d H:i:s')]);
        $groupId = $pdo->lastInsertId();
        echo "Created default group with ID: $groupId\n";
        $group = ['id' => $groupId];
    } else {
        echo "Default group already exists with ID: " . $group['id'] . "\n";
    }

    // Get all active users
    $stmt = $pdo->prepare("SELECT * FROM users WHERE status = ?");
    $stmt->execute(['active']);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($users) . " active users\n";

    // Add all users to the group
    $addedCount = 0;
    foreach ($users as $user) {
        // Check if user is already in group
        $stmt = $pdo->prepare("SELECT * FROM group_members WHERE group_id = ? AND user_id = ?");
        $stmt->execute([$group['id'], $user['id']]);
        $existingMember = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingMember) {
            $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id, joined_at) VALUES (?, ?, ?)");
            $stmt->execute([$group['id'], $user['id'], date('Y-m-d H:i:s')]);
            $addedCount++;
            echo "Added user: " . $user['nama'] . "\n";
        }
    }

    echo "Added $addedCount users to the group\n";
    echo "Done!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

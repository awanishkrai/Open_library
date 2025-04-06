<?php
require_once '../../db.php';

$discussion_id = $_GET['id'];

// Get discussion
$stmt = $conn->prepare("SELECT * FROM club_discussions WHERE id = ?");
$stmt->bind_param("i", $discussion_id);
$stmt->execute();
$discussion = $stmt->get_result()->fetch_assoc();

// Get comments
$stmt2 = $conn->prepare("SELECT c.*, u.name AS user_name FROM comments c JOIN users u ON c.user_id = u.id WHERE discussion_id = ? ORDER BY created_at ASC");
$stmt2->bind_param("i", $discussion_id);
$stmt2->execute();
$comments = $stmt2->get_result();

$discussion['comments'] = [];
while ($row = $comments->fetch_assoc()) {
    $discussion['comments'][] = $row;
}

echo json_encode($discussion);
?>

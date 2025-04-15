<?php
require_once '../../db.php';

$discussion_id = $_GET['id'];

// Get discussion with the username of the user who started it
$stmt = $conn->prepare("
    SELECT cd.*, u.username AS started_by_username
    FROM club_discussions cd
    JOIN users u ON cd.started_by = u.id
    WHERE cd.id = ?");
$stmt->bind_param("i", $discussion_id);
$stmt->execute();
$discussion = $stmt->get_result()->fetch_assoc();

// Get comments
$stmt2 = $conn->prepare("SELECT c.*, u.username AS user_name FROM comments c JOIN users u ON c.user_id = u.id WHERE discussion_id = ? ORDER BY created_at ASC");
$stmt2->bind_param("i", $discussion_id);
$stmt2->execute();
$comments = $stmt2->get_result();

$discussion['comments'] = [];
while ($row = $comments->fetch_assoc()) {
    $discussion['comments'][] = $row;
}

// Return discussion with the username of the user who started the discussion
echo json_encode($discussion);

?>

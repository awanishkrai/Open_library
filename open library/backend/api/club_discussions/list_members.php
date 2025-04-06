<?php
require_once '../../db.php';

$club_id = $_GET['club_id'] ?? 0;

$stmt = $conn->prepare("
    SELECT u.id, u.name, u.email, cm.joined_at
    FROM club_members cm
    JOIN users u ON cm.user_id = u.id
    WHERE cm.club_id = ?
    ORDER BY cm.joined_at ASC
");
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

echo json_encode($members);
?>

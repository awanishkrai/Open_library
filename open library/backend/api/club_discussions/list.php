<?php
require_once '../../db.php';

$club_id = $_GET['club_id'];

$stmt = $conn->prepare("SELECT * FROM club_discussions WHERE club_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();

$discussions = [];
while ($row = $result->fetch_assoc()) {
    $discussions[] = $row;
}

echo json_encode($discussions);
?>

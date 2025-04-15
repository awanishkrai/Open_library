<?php
require_once '../../db.php';

// --- CORS Headers ---
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// --- Get club_id ---
$club_id = intval($_GET['club_id'] ?? 0);
if (!$club_id) {
    echo json_encode(["error" => "Club ID is required."]);
    exit;
}

// --- Fetch Discussions ---
$stmt = $conn->prepare("SELECT d.id, d.book_id, d.title, d.created_at, u.username AS started_by
                        FROM club_discussions d
                        JOIN users u ON d.started_by = u.id
                        WHERE d.club_id = ?
                        ORDER BY d.created_at DESC");
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();

$discussions = [];
while ($row = $result->fetch_assoc()) {
    $discussions[] = $row;
}

echo json_encode($discussions);
?>

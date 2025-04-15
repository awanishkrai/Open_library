<?php
// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Handle preflight (OPTIONS) request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content
    exit;
}

require_once '../../db.php'; // Adjust the path as needed

// Validate and get club_id
$club_id = filter_input(INPUT_GET, 'club_id', FILTER_VALIDATE_INT);
if (!$club_id) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid or missing club_id"]);
    exit;
}

// Prepare SQL query
$stmt = $conn->prepare("
    SELECT u.id, u.name, cm.joined_at
    FROM club_members cm
    JOIN users u ON cm.user_id = u.id
    WHERE cm.club_id = ?
    ORDER BY cm.joined_at ASC
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to prepare query"]);
    exit;
}

$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();

// Collect results
$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = [
        "id" => $row['id'],
        "name" => $row['name'],
        "joined_at" => $row['joined_at']
    ];
}

echo json_encode($members);
?>

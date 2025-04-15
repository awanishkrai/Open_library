<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "open_library");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get all clubs
$sql = "SELECT id, name, description, created_at FROM book_clubs ORDER BY created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit();
}

$clubs = [];
while ($row = $result->fetch_assoc()) {
    $club_id = $row['id'];

    // Fetch members for this club
    $members = [];
    $memberStmt = $conn->prepare("
        SELECT u.username 
        FROM club_members cm
        JOIN users u ON cm.user_id = u.id
        WHERE cm.club_id = ?
    ");
    $memberStmt->bind_param("i", $club_id);
    $memberStmt->execute();
    $memberResult = $memberStmt->get_result();

    while ($member = $memberResult->fetch_assoc()) {
        $members[] = $member['username'];
    }

    $row['members'] = $members; // Attach members list to club

    $clubs[] = $row;
}

echo json_encode(["success" => true, "clubs" => $clubs]);

$conn->close();
?>

<?php
require_once '../../db.php';

// --- CORS Headers ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- Read JSON ---
$data = json_decode(file_get_contents("php://input"), true);

$club_id = intval($data['club_id'] ?? 0);
$book_id = trim($data['book_id'] ?? '');
$title = trim($data['title'] ?? '');
$username = trim($data['username'] ?? '');

// --- Validate Input ---
if (!$club_id || !$book_id || !$title || !$username) {
    echo json_encode(["error" => "All fields are required."]);
    exit;
}

// --- Get User ID ---
$userStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    echo json_encode(["error" => "User not found."]);
    exit;
}

$user = $userResult->fetch_assoc();
$user_id = $user['id'];

// --- Insert Discussion ---
$stmt = $conn->prepare("INSERT INTO club_discussions (club_id, book_id, title, started_by) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issi", $club_id, $book_id, $title, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "discussion_id" => $stmt->insert_id]);
} else {
    echo json_encode(["error" => "Failed to create discussion."]);
}
?>

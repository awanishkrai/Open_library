<?php
// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight (CORS)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

// Include DB connection
require_once '../../db.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['discussion_id'], $data['username'], $data['comment']) || 
    empty(trim($data['discussion_id'])) || 
    empty(trim($data['username'])) || 
    empty(trim($data['comment']))) {
    http_response_code(400);
    echo json_encode(["error" => "discussion_id, username, and comment are required."]);
    exit;
}

$discussion_id = (int) $data['discussion_id'];
$username = trim($data['username']);
$commentText = trim($data['comment']);

// Get user ID from username
$userStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["error" => "User not found."]);
    $userStmt->close();
    exit;
}

$user_id = $userResult->fetch_assoc()['id'];
$userStmt->close();

// Insert the comment
$insertStmt = $conn->prepare("INSERT INTO comments (discussion_id, user_id, comment) VALUES (?, ?, ?)");
$insertStmt->bind_param("iis", $discussion_id, $user_id, $commentText);

if ($insertStmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Comment added successfully.",
        "comment_id" => $insertStmt->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to add comment."]);
}

$insertStmt->close();
$conn->close();
?>

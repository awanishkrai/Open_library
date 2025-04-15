<?php
// Allow requests from any origin (for frontend integration)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include DB connection
require_once '../../db.php';

// Get the JSON data from the request body
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['club_id']) || !isset($data['username'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing club_id or username"]);
    exit;
}

$club_id = (int) $data['club_id'];
$username = trim($data['username']);

// Fetch user ID using the username
$user_query = $conn->prepare("SELECT id FROM users WHERE username = ?");
$user_query->bind_param("s", $username);
$user_query->execute();
$user_result = $user_query->get_result();

if ($user_result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["error" => "User not found"]);
    exit;
}

$user = $user_result->fetch_assoc();
$user_id = (int) $user['id'];

// Check if user is already a member of the club
$check = $conn->prepare("SELECT id FROM club_members WHERE club_id = ? AND user_id = ?");
$check->bind_param("ii", $club_id, $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["message" => "Already a member"]);
    exit;
}

// Add user to the club
$stmt = $conn->prepare("INSERT INTO club_members (club_id, user_id) VALUES (?, ?)");
$stmt->bind_param("ii", $club_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Joined club successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to join club"]);
}

// Close connections
$stmt->close();
$check->close();
$user_query->close();
$conn->close();
?>

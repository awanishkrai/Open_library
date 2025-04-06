<?php
// Allow requests from any origin (for frontend integration)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include DB connection
require_once '../../db.php';

// Get the JSON data from the request body
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['club_id']) || !isset($data['user_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing club_id or user_id"]);
    exit;
}

$club_id = (int) $data['club_id'];
$user_id = (int) $data['user_id'];

// Check if user is already a member
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
$conn->close();
?>

<?php
require_once '../db.php';
header('Content-Type: application/json');

// Read JSON input
$input = json_decode(file_get_contents("php://input"), true);

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}

// Validate input fields
if (!isset($input['username']) || !isset($input['password'])) {
    echo json_encode(["success" => false, "message" => "Missing credentials"]);
    exit;
}

$username = $input['username'];
$password = password_hash($input['password'], PASSWORD_DEFAULT);

// Check if the username already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Username already taken"]);
    exit;
}

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "User registered successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Registration failed"]);
}
exit;
?>

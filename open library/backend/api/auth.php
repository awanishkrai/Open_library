<?php
session_start();
require_once '../db.php';
header('Content-Type: application/json');

// Read JSON input
$input = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

// Validate input
if (!isset($input['username']) || !isset($input['password'])) {
    echo json_encode(["success" => false, "message" => "Missing credentials"]);
    exit;
}

$username = $input['username'];
$password = $input['password'];

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];

        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "user" => [
                "id" => $row['id'],
                "username" => $row['username']
            ]
        ]);
        exit;
    } else {
        echo json_encode(["success" => false, "message" => "Invalid password"]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
}
?>

<?php
// discussion.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database connection
$host = "localhost";
$db = "open_library";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

// Handle GET: fetch comments
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (!isset($_GET['book_id'])) {
        echo json_encode([]);
        exit();
    }

    $book_id = intval($_GET['book_id']);
    $sql = "SELECT username, comment, created_at FROM discussions WHERE book_id = ? ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }

    echo json_encode($comments);
    exit();
}

// Handle POST: add a comment
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        !isset($data["username"]) ||
        !isset($data["book_id"]) ||
        !isset($data["comment"])
    ) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Missing data ."]);
        exit();
    }

    $username = htmlspecialchars(strip_tags($data["username"]));
    $book_id = intval($data["book_id"]);
    $comment = htmlspecialchars(strip_tags($data["comment"]));

    $sql = "INSERT INTO discussions (book_id, username, comment, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $book_id, $username, $comment);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Failed to save comment."]);
    }

    exit();
}

// Fallback for unsupported methods
http_response_code(405);
echo json_encode(["error" => "Method not allowed."]);
exit();
?>

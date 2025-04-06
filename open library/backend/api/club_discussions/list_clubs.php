<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Database connection
$conn = new mysqli("localhost", "root", "", "open_library");

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Query book_clubs table
$sql = "SELECT id, name, description, created_at FROM book_clubs ORDER BY created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit();
}

$clubs = [];
while ($row = $result->fetch_assoc()) {
    $clubs[] = $row;
}

echo json_encode(["success" => true, "clubs" => $clubs]);

$conn->close();
?>

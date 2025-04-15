<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "../db.php";

if (!isset($_GET["id"])) {
    echo json_encode(["error" => "Book ID is required"]);
    exit();
}

$id = intval($_GET["id"]);

// Prepare statement
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "Book not found"]);
}

$stmt->close();
$conn->close();
?>

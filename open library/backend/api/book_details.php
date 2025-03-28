<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "../db.php";

if (!isset($_GET["id"])) {
    echo json_encode(["error" => "Book ID is required"]);
    exit();
}

$id = intval($_GET["id"]);
$sql = "SELECT * FROM books WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "Book not found"]);
}
?>

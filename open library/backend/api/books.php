<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "../db.php";

$sql = "SELECT * FROM books";
$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

$books = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($books);

$conn->close();
?>

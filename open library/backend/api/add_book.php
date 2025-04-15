<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

include "../db.php";

if (
    !isset($_POST["title"]) ||
    !isset($_POST["author"]) ||
    !isset($_POST["year"]) ||
    !isset($_FILES["pdf"]) ||
    !isset($_FILES["image"])
) {
    echo json_encode(["error" => "Missing required fields or files"]);
    exit();
}

$title = $_POST["title"];
$author = $_POST["author"];
$year = intval($_POST["year"]);


$uploadDir = "C:/xampp/htdocs/new proj/open library/uploads/";

// Make sure upload directory exists
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true); // create if not exists
}

// ----- Upload image -----
$image = $_FILES["image"];
$imageFilename = uniqid() . "_" . basename($image["name"]);
$imageTarget = $uploadDir . $imageFilename;
$imagePathInDb = "uploads/" . $imageFilename;

if (!move_uploaded_file($image["tmp_name"], $imageTarget)) {
    echo json_encode(["error" => "Failed to upload image"]);
    exit();
}

// ----- Upload PDF -----
$pdf = $_FILES["pdf"];
$pdfFilename = uniqid() . "_" . basename($pdf["name"]);
$pdfTarget = $uploadDir . $pdfFilename;
$pdfPathInDb = "uploads/" . $pdfFilename;

if (!move_uploaded_file($pdf["tmp_name"], $pdfTarget)) {
    echo json_encode(["error" => "Failed to upload PDF"]);
    exit();
}

// ----- Insert into database -----
$stmt = $conn->prepare("INSERT INTO books (title, author, year, image_path, pdf_path) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssiss", $title, $author, $year, $imagePathInDb, $pdfPathInDb);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Book added successfully",
        "id" => $stmt->insert_id,
        "image_path" => $imagePathInDb,
        "pdf_path" => $pdfPathInDb
    ]);
} else {
    echo json_encode(["error" => "Database insert failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

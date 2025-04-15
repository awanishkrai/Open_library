<?php
// --- CORS Setup ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

// --- Database Connection ---
$host = "localhost";
$db = "open_library";
$user = "root";
$pass = ""; // set your password here

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

header("Content-Type: application/json");

// --- POST: Add or Update Rating ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    $username = $conn->real_escape_string($input["username"]);
    $book_id = (int)$input["book_id"];
    $rating = (int)$input["rating"];

    if (!$username || !$book_id || $rating < 1 || $rating > 5) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid input"]);
        exit;
    }

    // Check if user already rated
    $check = $conn->prepare("SELECT id FROM ratings WHERE username = ? AND book_id = ?");
    $check->bind_param("si", $username, $book_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update rating
        $stmt = $conn->prepare("UPDATE ratings SET rating = ? WHERE username = ? AND book_id = ?");
        $stmt->bind_param("isi", $rating, $username, $book_id);
    } else {
        // Insert new rating
        $stmt = $conn->prepare("INSERT INTO ratings (username, book_id, rating) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $username, $book_id, $rating);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Rating saved successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to save rating"]);
    }

    $stmt->close();
}

// --- GET: Fetch average rating and count ---
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["book_id"])) {
    $book_id = (int)$_GET["book_id"];

    $stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total FROM ratings WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ratingData = $result->fetch_assoc();

    echo json_encode([
        "average" => round($ratingData["avg_rating"], 2),
        "count" => (int)$ratingData["total"]
    ]);

    $stmt->close();
}

$conn->close();
?>

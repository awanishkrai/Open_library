<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "../db.php";

// Handle preflight request
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// ============= ✅ POST: Add to Readlist =============
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input["username"], $input["book_id"])) {
        echo json_encode(["error" => "Username and Book ID are required"]);
        exit();
    }

    $username = $input["username"];
    $book_id = intval($input["book_id"]);
    $status = $input["status"] ?? "to-read";
    $notes = $input["notes"] ?? "";

    $stmt = $conn->prepare("INSERT INTO readlist (username, book_id, status, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $username, $book_id, $status, $notes);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Book added to reading list"]);
    } else {
        echo json_encode(["error" => "Failed to add book: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// ============= ✏️ PUT: Update Status or Notes =============
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input["readlist_id"])) {
        echo json_encode(["error" => "readlist_id is required"]);
        exit();
    }

    $readlist_id = intval($input["readlist_id"]);
    $status = $input["status"] ?? null;
    $notes = $input["notes"] ?? null;

    $fields = [];
    $params = [];
    $types = "";

    if ($status !== null) {
        $fields[] = "status = ?";
        $params[] = $status;
        $types .= "s";
    }
    if ($notes !== null) {
        $fields[] = "notes = ?";
        $params[] = $notes;
        $types .= "s";
    }

    if (empty($fields)) {
        echo json_encode(["error" => "No data to update"]);
        exit();
    }

    $params[] = $readlist_id;
    $types .= "i";

    $query = "UPDATE readlist SET " . implode(", ", $fields) . " WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Readlist updated"]);
    } else {
        echo json_encode(["error" => "Failed to update: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// ============= 📥 GET: Fetch Readlist for a User =============
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (!isset($_GET["username"])) {
        echo json_encode(["error" => "Username is required"]);
        exit();
    }

    $username = $_GET["username"];

    $stmt = $conn->prepare("
        SELECT 
            rl.id AS readlist_id,
            b.id AS book_id,
            b.title,
            b.author,
            b.year,
            b.image_path,
            b.pdf_path,
            rl.status,
            rl.notes,
            rl.added_at
        FROM readlist rl
        JOIN books b ON rl.book_id = b.id
        WHERE rl.username = ?
        ORDER BY rl.added_at DESC
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $readlist = [];
    while ($row = $result->fetch_assoc()) {
        $readlist[] = $row;
    }

    echo json_encode(["username" => $username, "readlist" => $readlist]);

    $stmt->close();
    $conn->close();
    exit();
}

// ============= ❌ DELETE: Remove from Readlist =============
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input["readlist_id"])) {
        echo json_encode(["error" => "readlist_id is required"]);
        exit();
    }

    $readlist_id = intval($input["readlist_id"]);

    $stmt = $conn->prepare("DELETE FROM readlist WHERE id = ?");
    $stmt->bind_param("i", $readlist_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Book removed from readlist"]);
    } else {
        echo json_encode(["error" => "Failed to delete: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// ============= 🚫 Unsupported Method =============
http_response_code(405);
echo json_encode(["error" => "Method not allowed"]);
?>
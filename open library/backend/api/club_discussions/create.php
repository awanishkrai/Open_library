<?php
// Enable cross-origin requests (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Include database connection
include "../../db.php";

// Handle preflight request (CORS)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// ============= ✅ POST: Create Book Club =============

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the input data (assumes JSON input)
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate the required fields (name, description, and username)
    if (!isset($data['name'], $data['description'], $data['username']) || empty($data['name']) || empty($data['description']) || empty($data['username'])) {
        echo json_encode(["error" => "Name, description, and username are required."]);
        exit();
    }

    // Extract values from the request
    $name = trim($data['name']);
    $description = trim($data['description']);
    $username = trim($data['username']);

    // Fetch the user ID based on the provided username
    $userStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $userStmt->bind_param("s", $username);
    $userStmt->execute();
    $userResult = $userStmt->get_result();

    if ($userResult->num_rows === 0) {
        echo json_encode(["error" => "User not found."]);
        $userStmt->close();
        exit();
    }

    // Get the user ID
    $user_id = $userResult->fetch_assoc()['id'];
    $userStmt->close();

    // Insert the new book club into the book_clubs table
    $stmt = $conn->prepare("INSERT INTO book_clubs (name, description, owner_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $description, $user_id);

    if ($stmt->execute()) {
        $club_id = $stmt->insert_id;

        // Add the user as a member of the new club in the club_members table
        $joinStmt = $conn->prepare("INSERT INTO club_members (club_id, user_id) VALUES (?, ?)");
        $joinStmt->bind_param("ii", $club_id, $user_id);
        $joinStmt->execute();
        $joinStmt->close();

        // Return success response
        echo json_encode([
            "success" => true,
            "club_id" => $club_id,
            "message" => "Book club created and user added as member."
        ]);
    } else {
        echo json_encode(["error" => "Failed to create club: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// ============= 🚫 Unsupported Method =============

http_response_code(405);
echo json_encode(["error" => "Method not allowed"]);
?>

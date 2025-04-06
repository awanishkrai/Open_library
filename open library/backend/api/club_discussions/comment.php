<?php
require_once '../../db.php';
$data = json_decode(file_get_contents("php://input"), true);

$discussion_id = $data['discussion_id'];
$user_id = $data['user_id'];
$comment = $data['comment'];

$stmt = $conn->prepare("INSERT INTO comments (discussion_id, user_id, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $discussion_id, $user_id, $comment);

if ($stmt->execute()) {
    echo json_encode(["message" => "Comment posted"]);
} else {
    echo json_encode(["error" => "Failed to post comment"]);
}
?>

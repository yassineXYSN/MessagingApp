<?php
// get_new_messages.php
include 'database.php'; // Your database connection

$last = $_POST['last'] ?? 0;
$user = $_POST['user'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM message WHERE number > ? ORDER BY number ASC");
$stmt->bind_param("i", $last);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

header('Content-Type: application/json');
echo json_encode($messages);
?>
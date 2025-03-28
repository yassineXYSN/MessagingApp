<?php
// send_message.php
include 'database.php'; // Your database connection file

// Get POST data
$message = $_POST['message'] ?? '';
$user1 = $_POST['user1'] ?? 0;
$user2 = $_POST['user2'] ?? 0;

// Basic validation
if (empty($message)) {
    die(json_encode(['success' => false, 'error' => 'Empty message']));
}

try {
    // Get current maximum number
    $result = $conn->query("SELECT MAX(number) AS max_number FROM message");
    $row = $result->fetch_assoc();
    $new_number = ($row['max_number'] ?? 0) + 1;

    // Insert new message
    $stmt = $conn->prepare("INSERT INTO message 
                          (message, user1, user2, number) 
                          VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siii", $message, $user1, $user2, $new_number);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'new_number' => $new_number]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>
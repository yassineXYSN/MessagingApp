<?php
// loadmessages.php
require_once 'database.php';

$result = $conn->query("SELECT * FROM message ORDER BY number ASC");
$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
?>
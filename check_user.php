<?php

// check_user.php
require_once 'database.php';

if (isset($_POST['username'])) {
    $userInput = $conn->real_escape_string($_POST['username']);

    $query = "SELECT iduser FROM user WHERE name = '$userInput' LIMIT 1";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['iduser'];
        echo $row['iduser'];
    } else {
        echo "not_found"; // No user found
    }
}

?>

<?php

// database.php

$hostname = 'yassine.mysql.database.azure.com';
$username = 'yassine';
$password = 'lamia123L'; // Replace with your actual password
$database = 'messages'; // Replace with your database name
$port = 3306;

// Create connection
$conn = new mysqli($hostname, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8 (recommended)
$conn->set_charset("utf8mb4");

// Optional: Function to close connection
function closeConnection() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}

/*

// Sample query
$result = $conn->query("SELECT * FROM your_table");
while($row = $result->fetch_assoc()) {
    // Process data
}

// Close connection when done (optional)
closeConnection();
*/
?>
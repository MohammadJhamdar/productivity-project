<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "phpproject";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Drop the database
$sql_drop_database = "DROP DATABASE IF EXISTS $dbname";

if ($conn->query($sql_drop_database) === TRUE) {
    echo "Database deleted successfully\n";
} else {
    echo "Error deleting database: " . $conn->error . "\n";
}

// Close the connection
$conn->close();

?>
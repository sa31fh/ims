<?php
$conn = null;

function connect_to_db() {
    global $conn;
    if ($conn) {
        return; 
    }

    $servername = "localhost";
    $user_name = "root";
    $dbname = "inventory_system";

    $conn = new mysqli($servername, "root", null, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}

?>

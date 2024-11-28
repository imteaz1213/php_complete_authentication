<?php

$host = "localhost";      
$username = "root";       
$password = "";           
$dbname = "example_db";   


$conn = mysqli_connect($host, $username, $password, $dbname);


if (!$conn) {
    die("Connection failed: " . $conn->connect_error);
}
?>

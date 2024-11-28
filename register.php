<?php

include 'db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    
    if (empty($username) || empty($email) || empty($hashed_password)) {
        echo "Please fill all the fields!";
    } else {
        
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        
        
        if ($stmt = $conn->prepare($sql)) {
            
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            
            if ($stmt->execute()) {
                echo "Registration successful!";
            } else {
                echo "Error: " . $stmt->error;
            }

            
            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Close the database connection
    $conn->close();
}
?>



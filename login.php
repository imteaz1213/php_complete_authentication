<?php

include 'db.php';

session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form input values
    $username = $_POST['username'];
    $password = $_POST['password'];

  
    if (empty($username) || empty($password)) {
        echo "Please fill all fields!";
    } else {
        
        $sql = "SELECT id, username, password FROM users WHERE username = '$username'";

        
        $result = mysqli_query($conn, $sql);

      
        if (mysqli_num_rows($result) > 0) {
            
            $user = mysqli_fetch_assoc($result);

            
            if ($user['password'] == $password) {
               
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                echo "Login successful!";
            } else {
               
                echo "Invalid  password.";
            }
        } else {
           
            echo "Invalid username.";
        }
    }
}

mysqli_close($conn);
?>

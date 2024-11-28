<?php

include 'db.php'; 
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo json_encode(["error" => "Please fill all fields!"]);
        exit;
    }


    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                 $_SESSION['loggedin'] = true;
                 $_SESSION['id'] = $user['id'];
                 $_SESSION['username'] = $user['username'];

                echo json_encode([
                    "success" => true,
                    "message" => "Login successful!"
                ]);
            } else {
        
                echo json_encode(["error" => "Invalid username or password."]);
            }
        } else {
        
            echo json_encode(["error" => "Invalid username or password."]);
        }

        $stmt->close();
    } else {
    
        error_log("Database error: " . $conn->error, 3, 'error_log.log');
        echo json_encode(["error" => "An error occurred. Please try again later."]);
    }
}

$conn->close();
?>

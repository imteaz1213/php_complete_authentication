<?php

require 'vendor/autoload.php'; 
include 'db.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = 'your-secret-key'; 
$issuer = 'your-domain.com';   

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(["error" => "Please fill all the fields!"]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Invalid email format!"]);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $check_sql = "SELECT id FROM users WHERE email = ? OR username = ?";
    if ($check_stmt = $conn->prepare($check_sql)) {
        $check_stmt->bind_param("ss", $email, $username);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) {
            echo json_encode(["error" => "Username or email already exists!"]);
            $check_stmt->close();
            exit;
        }
        $check_stmt->close();
    }

    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        if ($stmt->execute()) {
            
            $userId = $stmt->insert_id; 
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600; 

            $payload = [
                'iss' => $issuer,        
                'iat' => $issuedAt,      
                'exp' => $expirationTime, 
                'user' => [
                    'id' => $userId,
                    'username' => $username,
                    'email' => $email,
                ],
            ];

            $jwt = JWT::encode($payload, $secretKey, 'HS256'); // Create JWT token

            echo json_encode([
                "success" => true,
                "message" => "Registration successful!",
                "token" => $jwt,
            ]);
        } else {
            error_log("Database error: " . $stmt->error, 3, 'error_log.log');
            echo json_encode(["error" => "An error occurred. Please try again later."]);
        }
        $stmt->close();
    } else {
        error_log("Database error: " . $conn->error, 3, 'error_log.log');
        echo json_encode(["error" => "An error occurred. Please try again later."]);
    }

    $conn->close();
}
?>

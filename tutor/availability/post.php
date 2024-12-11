<?php

// Import your config.php file
require_once "./../conn.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

    $data = json_decode(file_get_contents('php://input'), true);

// Function to handle errors
function handleError($message) {
    http_response_code(400);
    echo json_encode(['error' => $message]);
    exit;
}
   

    if (!isset($data['tutor_id'], $data['available_date'], $data['available_time'], $data['available_up_to'], $data['status'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO `availability`(`tutor_id`, `available_date`, `available_time`, `available_up_to`, `status`) VALUES (:tutor_id, :available_date, :available_time, :available_up_to, :status)");
        
        $stmt->execute([
            ':tutor_id' => $data['tutor_id'],
            ':available_date' => $data['available_date'],
            ':available_time' => $data['available_time'],
            ':available_up_to' => $data['available_up_to'],
            ':status' => $data['status']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Availability inserted successfully']);
        } else {
            handleError("Failed to insert availability");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
?>
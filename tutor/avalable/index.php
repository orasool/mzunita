<?php
header('Content-Type: application/json');
// Import your config.php file
require_once "./../conn.php";


// Create a PDO instance

// Function to handle errors
function handleError($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

echo json_encode(['message'=> $_SERVER['REQUEST_METHOD']]);
    die();
// POST request handler for inserting a new availability
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);



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
}




// PUT request handler for updating an availability
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['tutor_id']) && isset($_GET['available_date'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['available_time'], $data['available_up_to'], $data['status'])) {
        handleError("Missing required fields");
    }

    try {
        $tutorId = $_GET['tutor_id'];
        $availableDate = $_GET['available_date'];
        $stmt = $pdo->prepare("UPDATE `availability` SET available_time = :available_time, available_up_to = :available_up_to, status = :status WHERE tutor_id = :tutor_id AND available_date = :available_date");
        
        $stmt->execute([
            ':tutor_id' => $tutorId,
            ':available_date' => $availableDate,
            ':available_time' => $data['available_time'],
            ':available_up_to' => $data['available_up_to'],
            ':status' => $data['status']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Availability updated successfully']);
        } else {
            handleError("Failed to update availability");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting an availability
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['tutor_id']) && isset($_GET['available_date'])) {
    try {
        $tutorId = $_GET['tutor_id'];
        $availableDate = $_GET['available_date'];
        $stmt = $pdo->prepare("DELETE FROM `availability` WHERE tutor_id = :tutor_id AND available_date = :available_date");
        $stmt->execute([':tutor_id' => $tutorId, ':available_date' => $availableDate]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Availability deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Availability not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
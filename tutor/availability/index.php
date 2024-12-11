<?php

// Import your config.php file
require_once "./../conn.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Function to handle errors
function handleError($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Handle POST request to insert a new availability
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

        break;

    case 'GET':
        // Handle GET request to fetch availability of a given tutor
        if (!isset($_GET['tutor_id'])) {
            handleError("Tutor ID is required");
        }

        $tutorId = $_GET['tutor_id'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM `availability` WHERE tutor_id = :tutor_id ORDER BY available_date, available_time");
            $stmt->execute([':tutor_id' => $tutorId]);

            $availabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($availabilities) {
                http_response_code(200);
                echo json_encode($availabilities);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'No availability found for this tutor']);
            }
        } catch (PDOException $e) {
            handleError("Database error: " . $e->getMessage());
        }

        break;

    case 'PUT':
        // Handle PUT request to update availability
        if (!isset($_GET['tutor_id']) || !isset($_GET['available_date'])) {
            handleError("Tutor ID and Available Date are required");
        }

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

        break;

    case 'DELETE':
        // Handle DELETE request to delete an availability
        if (!isset($_GET['tutor_id']) || !isset($_GET['available_date'])) {
            handleError("Tutor ID and Available Date are required");
        }

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

        break;

    default:
        // Handle unsupported HTTP methods
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

// Close the PDO connection
$pdo = null;

?>

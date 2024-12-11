<?php

// Import your db_connection.php file
require_once("./../conn.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Create a PDO instance

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Function to handle errors
function handleError($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

// POST request handler for creating a new status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['status_name']) || !isset($data['description'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO status_lookup (status_name, description)
                                VALUES (:status_name, :description)");
        
        $stmt->execute([
            ':status_name' => $data['status_name'],
            ':description' => $data['description']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Status inserted successfully']);
        } else {
            handleError("Failed to insert status");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all statuses
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM status_lookup ORDER BY status_name");
        
        $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($statuses);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single status by name
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['status_name'])) {
    try {
        $statusName = $_GET['status_name'];
        $stmt = $pdo->prepare("SELECT * FROM status_lookup WHERE status_name = :status_name");
        $stmt->execute([':status_name' => $statusName]);
        
        $status = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($status) {
            http_response_code(200);
            echo json_encode($status);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Status not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a status
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['status_name'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['status_name']) || !isset($data['description'])) {
        handleError("Missing required fields");
    }

    try {
        $statusName = $_GET['status_name'];
        $stmt = $pdo->prepare("UPDATE status_lookup SET status_name = :status_name, description = :description WHERE status_name = :old_status_name");
        
        $stmt->execute([
            ':status_name' => $data['status_name'],
            ':description' => $data['description'],
            ':old_status_name' => $statusName
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Status updated successfully']);
        } else {
            handleError("Failed to update status");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a status
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['status_name'])) {
    try {
        $statusName = $_GET['status_name'];
        $stmt = $pdo->prepare("DELETE FROM status_lookup WHERE status_name = :status_name");
        $stmt->execute([':status_name' => $statusName]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Status deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Status not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
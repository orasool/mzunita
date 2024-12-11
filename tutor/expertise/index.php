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

// POST request handler for creating a new expertise
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['expertise_name']) || !isset($data['description'])) {
        handleError("Missing required fields: expertise_name, description");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO expertise (expertise_name, description) VALUES (:expertise_name, :description)");
        
        $stmt->execute([
            ':expertise_name' => $data['expertise_name'],
            ':description' => $data['description']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Expertise inserted successfully']);
        } else {
            handleError("Failed to insert expertise");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all expertise
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM expertise ORDER BY expertise_name ASC");
        
        $expertises = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($expertises);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single expertise by expertise_name
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['expertise_name'])) {
    try {
        $expertiseName = $_GET['expertise_name'];
        $stmt = $pdo->prepare("SELECT * FROM expertise WHERE expertise_name = :expertise_name");
        $stmt->execute([':expertise_name' => $expertiseName]);
        
        $expertise = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($expertise) {
            http_response_code(200);
            echo json_encode($expertise);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Expertise not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating an expertise
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['expertise_name'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['expertise_name']) || !isset($data['description'])) {
        handleError("Missing required fields: expertise_name, description");
    }

    try {
        $expertiseName = $_GET['expertise_name'];
        $stmt = $pdo->prepare("UPDATE expertise SET expertise_name = :expertise_name, description = :description WHERE expertise_name = :expertise_name");
        
        $stmt->execute([
            ':expertise_name' => $expertiseName,
            ':description' => $data['description']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Expertise updated successfully']);
        } else {
            handleError("Failed to update expertise");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting an expertise
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['expertise_name'])) {
    try {
        $expertiseName = $_GET['expertise_name'];
        $stmt = $pdo->prepare("DELETE FROM expertise WHERE expertise_name = :expertise_name");
        $stmt->execute([':expertise_name' => $expertiseName]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Expertise deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Expertise not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
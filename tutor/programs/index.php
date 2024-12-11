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

// POST request handler for creating a new program
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['program_name']) || !isset($data['description'])) {
        handleError("Missing required fields: program_name and description");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO programs (program_name, description) VALUES (:program_name, :description)");
        
        $stmt->execute([
            ':program_name' => $data['program_name'],
            ':description' => $data['description']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Program inserted successfully']);
        } else {
            handleError("Failed to insert program");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all programs
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM programs ORDER BY program_name");
        
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($programs);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single program by program_name
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['program_name'])) {
    try {
        $programName = $_GET['program_name'];
        $stmt = $pdo->prepare("SELECT * FROM programs WHERE program_name = :program_name");
        $stmt->execute([':program_name' => $programName]);
        
        $program = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($program) {
            http_response_code(200);
            echo json_encode($program);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Program not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a program
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['program_name'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['program_name']) || !isset($data['description'])) {
        handleError("Missing required fields: program_name and description");
    }

    try {
        $oldProgramName = $_GET['program_name'];
        $newProgramName = $data['program_name'];
        $stmt = $pdo->prepare("UPDATE programs SET program_name = :new_program_name, description = :description WHERE program_name = :old_program_name");
        
        $stmt->execute([
            ':new_program_name' => $newProgramName,
            ':description' => $data['description'],
            ':old_program_name' => $oldProgramName
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Program updated successfully']);
        } else {
            handleError("Failed to update program");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a program
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['program_name'])) {
    try {
        $programName = $_GET['program_name'];
        $stmt = $pdo->prepare("DELETE FROM programs WHERE program_name = :program_name");
        $stmt->execute([':program_name' => $programName]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Program deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Program not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
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

// POST request handler for creating a new qualification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['qualification_name']) || !isset($data['description'])) {
        handleError("Missing required fields: qualification_name and description");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO qualifications (qualification_name, description) VALUES (:qualification_name, :description)");
        
        $stmt->execute([
            ':qualification_name' => $data['qualification_name'],
            ':description' => $data['description']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Qualification inserted successfully']);
        } else {
            handleError("Failed to insert qualification");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all qualifications
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM qualifications ORDER BY qualification_name");
        
        $qualifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($qualifications);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single qualification by qualification_name
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['qualification_name'])) {
    try {
        $qualificationName = $_GET['qualification_name'];
        $stmt = $pdo->prepare("SELECT * FROM qualifications WHERE qualification_name = :qualification_name");
        $stmt->execute([':qualification_name' => $qualificationName]);
        
        $qualification = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($qualification) {
            http_response_code(200);
            echo json_encode($qualification);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Qualification not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a qualification
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['qualification_name'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['qualification_name']) || !isset($data['description'])) {
        handleError("Missing required fields: qualification_name and description");
    }

    try {
        $oldQualificationName = $_GET['qualification_name'];
        $newQualificationName = $data['qualification_name'];
        $stmt = $pdo->prepare("UPDATE qualifications SET qualification_name = :new_qualification_name, description = :description WHERE qualification_name = :old_qualification_name");
        
        $stmt->execute([
            ':new_qualification_name' => $newQualificationName,
            ':description' => $data['description'],
            ':old_qualification_name' => $oldQualificationName
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Qualification updated successfully']);
        } else {
            handleError("Failed to update qualification");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a qualification
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['qualification_name'])) {
    try {
        $qualificationName = $_GET['qualification_name'];
        $stmt = $pdo->prepare("DELETE FROM qualifications WHERE qualification_name = :qualification_name");
        $stmt->execute([':qualification_name' => $qualificationName]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Qualification deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Qualification not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
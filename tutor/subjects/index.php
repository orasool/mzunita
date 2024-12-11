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

// POST request handler for creating a new subject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['subject_name']) || !isset($data['description'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO subjects (subject_name, description)
                                VALUES (:subject_name, :description)");
        
        $stmt->execute([
            ':subject_name' => $data['subject_name'],
            ':description' => $data['description']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Subject inserted successfully']);
        } else {
            handleError("Failed to insert subject");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all subjects
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM subjects ORDER BY subject_name");
        
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($subjects);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single subject by name
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['subject_name'])) {
    try {
        $subjectName = $_GET['subject_name'];
        $stmt = $pdo->prepare("SELECT * FROM subjects WHERE subject_name = :subject_name");
        $stmt->execute([':subject_name' => $subjectName]);
        
        $subject = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($subject) {
            http_response_code(200);
            echo json_encode($subject);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Subject not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a subject
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['subject_name'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['subject_name']) || !isset($data['description'])) {
        handleError("Missing required fields");
    }

    try {
        $subjectName = $_GET['subject_name'];
        $stmt = $pdo->prepare("UPDATE subjects SET subject_name = :subject_name, description = :description WHERE subject_name = :old_subject_name");
        
        $stmt->execute([
            ':subject_name' => $data['subject_name'],
            ':description' => $data['description'],
            ':old_subject_name' => $subjectName
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Subject updated successfully']);
        } else {
            handleError("Failed to update subject");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a subject
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['subject_name'])) {
    try {
        $subjectName = $_GET['subject_name'];
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE subject_name = :subject_name");
        $stmt->execute([':subject_name' => $subjectName]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Subject deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Subject not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
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

// POST request handler for creating a new tutor qualification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['tutor_id']) || !isset($data['qualification_id'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO tutor_qualifications (tutor_id, qualification_id)
                                VALUES (:tutor_id, :qualification_id)");
        
        $stmt->execute([
            ':tutor_id' => $data['tutor_id'],
            ':qualification_id' => $data['qualification_id']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Tutor qualification inserted successfully']);
        } else {
            handleError("Failed to insert tutor qualification");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all tutor qualifications
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM tutor_qualifications ORDER BY tutor_id");
        
        $tutorQualifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($tutorQualifications);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching tutor qualifications by tutor ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tutor_id'])) {
    try {
        $tutorId = $_GET['tutor_id'];
        $stmt = $pdo->prepare("SELECT * FROM tutor_qualifications WHERE tutor_id = :tutor_id");
        $stmt->execute([':tutor_id' => $tutorId]);
        
        $tutorQualification = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($tutorQualification) {
            http_response_code(200);
            echo json_encode($tutorQualification);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Tutor qualification not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a tutor qualification
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['tutor_id']) && isset($_GET['qualification_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['qualification_id'])) {
        handleError("Missing required fields");
    }

    try {
        $tutorId = $_GET['tutor_id'];
        $qualificationId = $_GET['qualification_id'];
        $stmt = $pdo->prepare("UPDATE tutor_qualifications SET qualification_id = :new_qualification_id WHERE tutor_id = :tutor_id AND qualification_id = :old_qualification_id");
        
        $stmt->execute([
            ':new_qualification_id' => $data['qualification_id'],
            ':tutor_id' => $tutorId,
            ':old_qualification_id' => $qualificationId
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Tutor qualification updated successfully']);
        } else {
            handleError("Failed to update tutor qualification");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a tutor qualification
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['tutor_id']) && isset($_GET['qualification_id'])) {
    try {
        $tutorId = $_GET['tutor_id'];
        $qualificationId = $_GET['qualification_id'];
        $stmt = $pdo->prepare("DELETE FROM tutor_qualifications WHERE tutor_id = :tutor_id AND qualification_id = :qualification_id");
        $stmt->execute([':tutor_id' => $tutorId, ':qualification_id' => $qualificationId]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Tutor qualification deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Tutor qualification not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
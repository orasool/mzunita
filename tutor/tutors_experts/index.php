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

// POST request handler for creating a new tutor expertise
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['tutor_id']) || !isset($data['expertise_id'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO tutor_expertise (tutor_id, expertise_id)
                                VALUES (:tutor_id, :expertise_id)");
        
        $stmt->execute([
            ':tutor_id' => $data['tutor_id'],
            ':expertise_id' => $data['expertise_id']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Tutor expertise inserted successfully']);
        } else {
            handleError("Failed to insert tutor expertise");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all tutor expertise
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM tutor_expertise ORDER BY tutor_id");
        
        $tutorExpertises = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($tutorExpertises);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching tutor expertise by tutor ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tutor_id'])) {
    try {
        $tutorId = $_GET['tutor_id'];
        $stmt = $pdo->prepare("SELECT * FROM tutor_expertise WHERE tutor_id = :tutor_id");
        $stmt->execute([':tutor_id' => $tutorId]);
        
        $tutorExpertise = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($tutorExpertise) {
            http_response_code(200);
            echo json_encode($tutorExpertise);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Tutor expertise not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a tutor expertise
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['tutor_id']) && isset($_GET['expertise_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['expertise_id'])) {
        handleError("Missing required fields");
    }

    try {
        $tutorId = $_GET['tutor_id'];
        $expertiseId = $_GET['expertise_id'];
        $stmt = $pdo->prepare("UPDATE tutor_expertise SET expertise_id = :expertise_id WHERE tutor_id = :tutor_id AND expertise_id = :old_expertise_id");
        
        $stmt->execute([
            ':expertise_id' => $data['expertise_id'],
            ':tutor_id' => $tutorId,
            ':old_expertise_id' => $expertiseId
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Tutor expertise updated successfully']);
        } else {
            handleError("Failed to update tutor expertise");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a tutor expertise
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['tutor_id']) && isset($_GET['expertise_id'])) {
    try {
        $tutorId = $_GET['tutor_id'];
        $expertiseId = $_GET['expertise_id'];
        $stmt = $pdo->prepare("DELETE FROM tutor_expertise WHERE tutor_id = :tutor_id AND expertise_id = :expertise_id");
        $stmt->execute([':tutor_id' => $tutorId, ':expertise_id' => $expertiseId]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Tutor expertise deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Tutor expertise not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
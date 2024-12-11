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

// POST request handler for creating a new feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['session_id']) || !isset($data['rating']) || !isset($data['comments']) || !isset($data['created_at'])) {
        handleError("Missing required fields: session_id, rating, comments, created_at");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO feedback (session_id, rating, comments, created_at) VALUES (:session_id, :rating, :comments, :created_at)");
        
        $stmt->execute([
            ':session_id' => $data['session_id'],
            ':rating' => $data['rating'],
            ':comments' => $data['comments'],
            ':created_at' => $data['created_at']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Feedback inserted successfully']);
        } else {
            handleError("Failed to insert feedback");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all feedback
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC");
        
        $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($feedback);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching feedback by session_id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['session_id'])) {
    try {
        $sessionId = $_GET['session_id'];
        $stmt = $pdo->prepare("SELECT * FROM feedback WHERE session_id = :session_id ORDER BY created_at DESC");
        $stmt->execute([':session_id' => $sessionId]);
        
        $feedback = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($feedback) {
            http_response_code(200);
            echo json_encode($feedback);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'No feedback found for this session']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating feedback
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['id'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['session_id']) || !isset($data['rating']) || !isset($data['comments']) || !isset($data['created_at'])) {
        handleError("Missing required fields: session_id, rating, comments, created_at");
    }

    try {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("UPDATE feedback SET session_id = :session_id, rating = :rating, comments = :comments, created_at = :created_at WHERE id = :id");
        
        $stmt->execute([
            ':id' => $id,
            ':session_id' => $data['session_id'],
            ':rating' => $data['rating'],
            ':comments' => $data['comments'],
            ':created_at' => $data['created_at']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Feedback updated successfully']);
        } else {
            handleError("Failed to update feedback");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting feedback
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM feedback WHERE id = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Feedback deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Feedback not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
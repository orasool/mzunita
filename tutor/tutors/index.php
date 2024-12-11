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

// POST request handler for creating a new tutor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name']) || !isset($data['email']) || !isset($data['phone_number']) ||
        !isset($data['created_at']) || !isset($data['password_hash'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO tutors (name, email, phone_number, created_at, password_hash)
                                VALUES (:name, :email, :phone_number, :created_at, :password_hash)");
        
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone_number' => $data['phone_number'],
            ':created_at' => $data['created_at'],
            ':password_hash' => $data['password_hash']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Tutor inserted successfully']);
        } else {
            handleError("Failed to insert tutor");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all tutors
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM tutors ORDER BY name");
        
        $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($tutors);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single tutor by name
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['name'])) {
    try {
        $name = $_GET['name'];
        $stmt = $pdo->prepare("SELECT * FROM tutors WHERE name = :name");
        $stmt->execute([':name' => $name]);
        
        $tutor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tutor) {
            http_response_code(200);
            echo json_encode($tutor);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Tutor not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a tutor
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['name'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name']) || !isset($data['email']) || !isset($data['phone_number']) ||
        !isset($data['created_at']) || !isset($data['password_hash'])) {
        handleError("Missing required fields");
    }

    try {
        $name = $_GET['name'];
        $stmt = $pdo->prepare("UPDATE tutors SET name = :name, email = :email, phone_number = :phone_number, created_at = :created_at, password_hash = :password_hash WHERE name = :name");
        
        $stmt->execute([
            ':name' => $name,
            ':email' => $data['email'],
            ':phone_number' => $data['phone_number'],
            ':created_at' => $data['created_at'],
            ':password_hash' => $data['password_hash']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Tutor updated successfully']);
        } else {
            handleError("Failed to update tutor");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a tutor
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['name'])) {
    try {
        $name = $_GET['name'];
        $stmt = $pdo->prepare("DELETE FROM tutors WHERE name = :name");
        $stmt->execute([':name' => $name]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Tutor deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Tutor not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
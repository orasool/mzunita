<?php

// Import db_connection.php file
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

// POST request handler for creating a new admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name']) || !isset($data['email']) || !isset($data['password_hash']) || !isset($data['created_at'])) {
        handleError("Missing required fields: name, email, password_hash, created_at");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO admins (name, email, password_hash, created_at) VALUES (:name, :email, :password_hash, :created_at)");
        
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':created_at' => $data['created_at']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Admin inserted successfully']);
        } else {
            handleError("Failed to insert admin");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}



// GET request handler for fetching a single admin by email
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['email'])) {
    try {
        $email = $_GET['email'];
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");
        $stmt->execute([':email' => $email]);
        
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            http_response_code(200);
            echo json_encode($admin);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Admin not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    } finally {
        // Close the PDO connection
        $pdo = null;
    }
}
// GET request handler for fetching a single admin by email

// PUT request handler for updating an admin
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['email'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name']) || !isset($data['password_hash']) || !isset($data['created_at'])) {
        handleError("Missing required fields: name, password_hash, created_at");
    }

    try {
        $email = $_GET['email'];
        $stmt = $pdo->prepare("UPDATE admins SET name = :name, password_hash = :password_hash, created_at = :created_at WHERE email = :email");
        
        $stmt->execute([
            ':email' => $email,
            ':name' => $data['name'],
            ':password_hash' => $data['password_hash'],
            ':created_at' => $data['created_at']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Admin updated successfully']);
        } else {
            handleError("Failed to update admin");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting an admin
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['email'])) {
    try {
        $email = $_GET['email'];
        $stmt = $pdo->prepare("DELETE FROM admins WHERE email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Admin deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Admin not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
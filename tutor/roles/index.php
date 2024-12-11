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

// POST request handler for creating a new role
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['role_name'])) {
        handleError("Missing required field: role_name");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO roles (role_name) VALUES (:role_name)");
        
        $stmt->execute([
            ':role_name' => $data['role_name']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Role inserted successfully']);
        } else {
            handleError("Failed to insert role");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all roles
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM roles ORDER BY role_name");
        
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($roles);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single role by role_name
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['role_name'])) {
    try {
        $roleName = $_GET['role_name'];
        $stmt = $pdo->prepare("SELECT * FROM roles WHERE role_name = :role_name");
        $stmt->execute([':role_name' => $roleName]);
        
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($role) {
            http_response_code(200);
            echo json_encode($role);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Role not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a role
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['role_name'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['role_name'])) {
        handleError("Missing required field: role_name");
    }

    try {
        $oldRoleName = $_GET['role_name'];
        $newRoleName = $data['role_name'];
        $stmt = $pdo->prepare("UPDATE roles SET role_name = :new_role_name WHERE role_name = :old_role_name");
        
        $stmt->execute([
            ':new_role_name' => $newRoleName,
            ':old_role_name' => $oldRoleName
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Role updated successfully']);
        } else {
            handleError("Failed to update role");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a role
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['role_name'])) {
    try {
        $roleName = $_GET['role_name'];
        $stmt = $pdo->prepare("DELETE FROM roles WHERE role_name = :role_name");
        $stmt->execute([':role_name' => $roleName]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Role deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Role not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
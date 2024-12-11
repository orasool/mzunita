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



// GET request handler for fetching all messages
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['sender_id']) && !isset($_GET['receiver_id'])) {
    try {
        $stmt = $pdo->query("SELECT * FROM messages ORDER BY sent_at ASC");
        
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($messages);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching messages between two users
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['sender_id']) && isset($_GET['receiver_id'])) {
    try {
        $senderId = $_GET['sender_id'];
        $receiverId = $_GET['receiver_id'];
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE (sender_id = :sender_id AND receiver_id = :receiver_id) OR (sender_id = :receiver_id AND receiver_id = :sender_id) ORDER BY sent_at DESC");
        $stmt->execute([':sender_id' => $senderId, ':receiver_id' => $receiverId]);
        
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($messages) > 0) {
            http_response_code(200);
            echo json_encode($messages);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'No messages found between these users']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single message by its ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        $message = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($message) {
            http_response_code(200);
            echo json_encode($message);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Message not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a message
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['id'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['sender_id']) || !isset($data['receiver_id']) || !isset($data['message_content']) || !isset($data['sent_at'])) {
        handleError("Missing required fields: sender_id, receiver_id, message_content, sent_at");
    }

    try {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("UPDATE messages SET sender_id = :sender_id, receiver_id = :receiver_id, message_content = :message_content, sent_at = :sent_at WHERE id = :id");
        
        $stmt->execute([
            ':id' => $id,
            ':sender_id' => $data['sender_id'],
            ':receiver_id' => $data['receiver_id'],
            ':message_content' => $data['message_content'],
            ':sent_at' => $data['sent_at']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Message updated successfully']);
        } else {
            handleError("Failed to update message");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a message
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Message deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Message not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
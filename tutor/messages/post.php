<?php
// Import your db_connection.php file
require_once("./../conn.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Create a PDO instance
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Function to handle errors
function handleError($message) {
    http_response_code(400);
    echo json_encode(['error' => $message]);
    exit;
}


 try {
        // Get the request body
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Debug: Print the received data
        error_log("Received data: " . json_encode($data));

        if (!isset($data['message_content']) || !isset($data['sender_id']) || !isset($data['receiver_id']) || !isset($data['sent_at'])) {
            throw new Exception("Missing required fields");
        }

        // Validate sender_id and receiver_id
        if (!is_numeric($data['sender_id']) || !is_numeric($data['receiver_id'])) {
            throw new Exception("Invalid sender_id or receiver_id");
        }

        $pdo->beginTransaction();
        $transactionStarted = true;

        // Insert conversation
        $stmt = $pdo->prepare("
            INSERT INTO conversations(subject, started_at, last_message_at)
            VALUES (:subject, :started_at, :last_message_at)
        ");
        $stmt->execute([
            ':subject' => 'New Conversation',
            ':started_at' => date('Y-m-d H:i:s'),
            ':last_message_at' => date('Y-m-d H:i:s')
        ]);
        $conversation_id = $pdo->lastInsertId();
        
        error_log("Conversation inserted successfully");

        // Insert participants
        $stmt = $pdo->prepare("
            INSERT INTO participants(conversation_id, tutor_id, student_id, role)
            VALUES (:conversation_id, :tutor_id, :student_id, :role)
        ");
        $stmt->execute([
            ':conversation_id' => $conversation_id,
            ':tutor_id' => $data['sender_id'],
            ':student_id' => $data['receiver_id'],
            ':role' => 'tutor'
        ]);

        error_log("Participants inserted successfully");

        // Insert message
        $stmt = $pdo->prepare("
            INSERT INTO messages(conversation_id, sender_id, sender_role, message_content, sent_at)
            VALUES (:conversation_id, :sender_id, :sender_role, :message_content, :sent_at)
        ");
        $stmt->execute([
            ':conversation_id' => $conversation_id,
            ':sender_id' => $data['sender_id'],
            ':sender_role' => 'tutor',
            ':message_content' => $data['message_content'],
            ':sent_at' => $data['sent_at']
        ]);

        error_log("Message inserted successfully");

        $pdo->commit();

        http_response_code(201);
        echo json_encode([
            'message' => 'Conversation started and message sent successfully',
            'conversation_id' => $conversation_id,
            'sender_id' => $data['sender_id'],
            'receiver_id' => $data['receiver_id']
        ]);
    } catch (Exception $e) {
        if (isset($transactionStarted) && $transactionStarted) {
            $pdo->rollBack();
        }
        handleError("An error occurred: " . $e->getMessage());
    }
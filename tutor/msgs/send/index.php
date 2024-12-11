<?php
require_once("./../../conn.php");

// Validate input data
$data = json_decode(file_get_contents('php://input'), true);

// Example data for sending a message
$chat_id = $data['chat_id']; // Existing chat ID
$sender_type = $data['sender_type']; // Can be 'student' or 'tutor'
$sender_id = $data['sender_id']; // ID of the student or tutor sending the message
$message_text = $data['message_text']; // Message content

try {
    // Prepare the SQL statement
    $stmt = $pdo->prepare("
        INSERT INTO Messages (chat_id, sender_type, sender_id, message_text)
        VALUES (:chat_id, :sender_type, :sender_id, :message_text);
    ");

    // Bind parameters and execute the query
    $stmt->execute([
        'chat_id' => $chat_id,
        'sender_type' => $sender_type,
        'sender_id' => $sender_id,
        'message_text' => $message_text
    ]);

    echo "Message sent successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

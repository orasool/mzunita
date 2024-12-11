<?php
require_once("./../../conn.php");

// Validate input data
$data = json_decode(file_get_contents('php://input'), true);

// Example data for starting a new chat
$student_id = $_GET['student_id'];
$tutor_id = $_GET['tutor_id'];

$first_message = $_GET['first_message'];

try {
    // Begin a transaction
    $pdo->beginTransaction();

    // Insert a new chat session
    $stmt = $pdo->prepare("
        INSERT INTO Chats (student_id, tutor_id)
        VALUES (:student_id, :tutor_id);
    ");
    $stmt->execute(['student_id' => $student_id, 'tutor_id' => $tutor_id]);

    // Get the last inserted chat_id
    $chat_id = $pdo->lastInsertId();

    // Insert the first message in the new chat
    $stmt = $pdo->prepare("
        INSERT INTO Messages (chat_id, sender_type, sender_id, message_text)
        VALUES (:chat_id, 'student', :sender_id, :message_text);
    ");
    $stmt->execute([
        'chat_id' => $chat_id,
        'sender_id' => $student_id,
        'message_text' => $first_message
    ]);

    // Commit the transaction
    $pdo->commit();

    echo "New chat started and message sent successfully!";
} catch (PDOException $e) {
    // Rollback transaction if any error occurs
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>

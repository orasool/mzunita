<?php

// Import your db_connection.php file
require_once("./../../conn.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

function getChatsWithStudentDetails($pdo, $tutor_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT c.chat_id, c.created_at, s.student_id, s.registration_number, s.name AS student_name, s.email AS student_email, s.phone_number AS student_phone
            FROM chats c
            LEFT JOIN students s ON c.student_id = s.student_id
            WHERE c.tutor_id = :tutor_id
            ORDER BY c.created_at DESC
        ");

        $stmt->bindParam(':tutor_id', $tutor_id);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return null;
    }
}
$data = json_decode(file_get_contents('php://input'), true);


// Usage example
$tutor_id = $_GET['tutor_id']; // Replace with actual tutor ID
$chats = getChatsWithStudentDetails($pdo, $tutor_id);

if ($chats !== null) {
    echo json_encode(
        
        $chats
    );
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No chats found or database error occurred.'
    ]);
}


?>
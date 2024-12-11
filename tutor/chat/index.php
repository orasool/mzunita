<?php

// Import your db_connection.php file
require_once("./../conn.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

function getChatsWithTutorDetails($pdo, $student_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT c.chat_id, c.created_at, t.tutor_id, t.name AS tutor_name, t.email AS tutor_email, t.phone_number AS tutor_phone
            FROM chats c
            LEFT JOIN tutors t ON c.tutor_id = t.tutor_id
            WHERE c.student_id = :student_id
            ORDER BY c.created_at DESC
        ");

        $stmt->bindParam(':student_id', $student_id);
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
$student_id = $_GET['student_id']; // Replace with actual student ID

$chats = getChatsWithTutorDetails($pdo, $student_id);

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

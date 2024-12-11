<?php

// Import your db_connection.php file
require_once("./../../conn.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Set PDO error mode to exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Function to handle errors
function handleError($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

// GET request handler for fetching notifications by tutor_id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tutor_id'])) {
    try {
        $tutorId = $_GET['tutor_id'];

        // Fetch notifications for the given tutor_id ordered by date descending
        $stmt = $pdo->prepare("SELECT  notification_id, student_id, tutor_id, notification, date 
                               FROM notifications 
                               WHERE tutor_id = :tutor_id 
                               ORDER BY date DESC");
        $stmt->execute([':tutor_id' => $tutorId]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($notifications) {
            http_response_code(200);
            echo json_encode($notifications);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'No notifications found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}
?>

<?php

// Import your db_connection.php file
require_once("./../conn.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Set error reporting
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Function to handle errors and send a proper JSON response
function handleError($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

// Decode the JSON input


// Required fields for validation
$requiredFields = ['tutor_id', 'student_id', 'subject_id', 'session_date', 'session_time','availability_id'];
foreach ($requiredFields as $field) {
    if (empty($_GET[$field])) {
        handleError("Missing required field: $field", 400);
    }
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Insert the session
    $stmt = $pdo->prepare("
        INSERT INTO sessions (tutor_id, student_id, subject_id, session_date, session_time, status, feedback_id)
        VALUES (:tutor_id, :student_id, :subject_id, :session_date, :session_time, :status, :feedback_id)
    ");

    $sessionInserted = $stmt->execute([
        ':tutor_id' => $_GET['tutor_id'],
        ':student_id' => $_GET['student_id'],
        ':subject_id' => $_GET['subject_id'],
        ':session_date' => $_GET['session_date'],
        ':session_time' => $_GET['session_time'],
        ':status' => $_GET['status'] ?? 1, // Default to 1 if status is not provided
        ':feedback_id' => $_GET['feedback_id'] ?? null
    ]);

    if (!$sessionInserted) {
        throw new Exception("Failed to insert session");
    }

    // Prepare and insert a notification
    $sessionMessage = "Session on " . $_GET['session_date'] . " at " . $_GET['session_time'] . " booked successfully.";
    $notificationStmt = $pdo->prepare("
        INSERT INTO notifications (student_id, tutor_id, notification, date)
        VALUES (:student_id, :tutor_id, :notification, :date)
    ");

    $notificationInserted = $notificationStmt->execute([
        ':student_id' => $_GET['student_id'],
        ':tutor_id' => $_GET['tutor_id'],
        ':notification' => $sessionMessage,
        ':date' => date('Y-m-d H:i:s') // Current timestamp
    ]);

    if (!$notificationInserted) {
        throw new Exception("Failed to insert notification");
    }

    // Update tutor availability
    $updateTutorStmt = $pdo->prepare("
        UPDATE availability SET status = 'Booked' WHERE  availability_id = :availability_id
    ");

    $availabilityUpdated = $updateTutorStmt->execute([
        ':availability_id' => $_GET['availability_id']
    ]);

    if (!$availabilityUpdated) {
        throw new Exception("Failed to update tutor availability");
    }

    // Commit the transaction
    $pdo->commit();

    // Send success response
    http_response_code(201);
    echo json_encode(['message' => 'Session booked, notification sent, and tutor availability updated successfully']);

} catch (PDOException $e) {
    // Rollback the transaction in case of database error
    $pdo->rollBack();
    handleError("Database error: " . $e->getMessage(), 500);
} catch (Exception $e) {
    // Rollback the transaction in case of other errors
    $pdo->rollBack();
    handleError($e->getMessage(), 500);
} finally {
    // Close the PDO connection
    $pdo = null;
}

?>

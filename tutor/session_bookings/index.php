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

// POST request handler for creating a new session booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['student_id']) || !isset($data['tutor_id']) || !isset($data['session_id']) || !isset($data['booked_at'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO session_bookings (student_id, tutor_id, session_id, booked_at)
                                VALUES (:student_id, :tutor_id, :session_id, :booked_at)");
        
        $stmt->execute([
            ':student_id' => $data['student_id'],
            ':tutor_id' => $data['tutor_id'],
            ':session_id' => $data['session_id'],
            ':booked_at' => $data['booked_at']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Session booking inserted successfully']);
        } else {
            handleError("Failed to insert session booking");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}



if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $studentId = $_GET['student_id'];

        // Prepare the main query to join all necessary tables
        $stmt = $pdo->prepare("
            SELECT 
                sb.*,
                t.name AS tutor_name,
                s.name AS student_name,
                se.session_id AS session_id,
                se.session_date AS session_date,
                se.session_time AS session_time,
                se.status AS session_status,
                se.feedback_id AS feedback_id,
                se.created_at AS created_at,
                su.subject_name AS subject_name
            FROM 
                session_bookings sb
            LEFT JOIN 
                tutors t ON sb.tutor_id = t.tutor_id
            LEFT JOIN 
                students s ON sb.student_id = s.student_id
            LEFT JOIN 
                sessions se ON sb.session_id = se.session_id
            LEFT JOIN 
                subjects su ON se.subject_id = su.subject_id
            WHERE 
                sb.student_id = :student_id
        ");

        $stmt->execute(['student_id' => $studentId]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($result)) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'No sessions found for the specified student']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}
// PUT request handler for updating a session booking
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['student_id']) && isset($_GET['session_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['student_id']) || !isset($data['tutor_id']) || !isset($data['session_id']) || !isset($data['booked_at'])) {
        handleError("Missing required fields");
    }

    try {
        $studentId = $_GET['student_id'];
        $sessionId = $_GET['session_id'];
        $stmt = $pdo->prepare("UPDATE session_bookings SET student_id = :student_id, tutor_id = :tutor_id, session_id = :session_id, booked_at = :booked_at 
                                WHERE student_id = :old_student_id AND session_id = :old_session_id");
        
        $stmt->execute([
            ':student_id' => $data['student_id'],
            ':tutor_id' => $data['tutor_id'],
            ':session_id' => $data['session_id'],
            ':booked_at' => $data['booked_at'],
            ':old_student_id' => $studentId,
            ':old_session_id' => $sessionId
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Session booking updated successfully']);
        } else {
            handleError("Failed to update session booking");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a session booking
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['student_id']) && isset($_GET['session_id'])) {
    try {
        $studentId = $_GET['student_id'];
        $sessionId = $_GET['session_id'];
        $stmt = $pdo->prepare("DELETE FROM session_bookings WHERE student_id = :student_id AND session_id = :session_id");
        $stmt->execute([':student_id' => $studentId, ':session_id' => $sessionId]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Session booking deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Session booking not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
<?php

// Import your db_connection.php file
require_once("./../conn.php");

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

// GET request handler for fetching sessions with tutor and student information
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        
        // Query to join sessions, tutors, and students tables
        $stmt = $pdo->prepare("
            SELECT s.session_id, s.tutor_id, s.student_id, s.subject_id, s.session_date, s.session_time, s.status, s.feedback_id, s.created_at,
                   t.name AS tutor_name, t.email AS tutor_email, t.phone_number AS tutor_phone, t.password_hash AS tutor_password_hash, 
                   t.qualification, t.expertise, t.year_of_graduation, t.years_experience,
                   st.registration_number, st.name AS student_name, st.email AS student_email, st.phone_number AS student_phone, 
                   st.program_id, st.department_id, st.year_of_study, st.academic_level, st.date_of_birth, st.nationality, 
                   st.language, st.technical_skills, st.hobbies, st.goals_motivation, sts.status_id,sts.status_name, sts.description AS status_description
            FROM sessions s
            JOIN tutors t ON s.tutor_id = t.tutor_id
            JOIN students st ON s.student_id = st.student_id
            JOIN status_lookup sts ON s.status = sts.status_id
           
            ORDER BY s.session_date DESC, s.session_time DESC
        ");
        $stmt->execute();
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($sessions) {
            http_response_code(200);
            echo json_encode($sessions);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'No sessions found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}
?>

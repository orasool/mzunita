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



// GET request handler for fetching all sessions

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $student_id = $_GET['student_id'];
      
        // Fetch all sessions
     $stmt = $pdo->prepare("SELECT * FROM sessions WHERE student_id=:student_id AND status =1");
        $stmt->execute(['student_id' => $student_id]);
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Initialize arrays to store results
        $tutors = [];
        $students = [];
        $subjects= [];

        // Loop through each session
        foreach ($sessions as &$session) {
            // Fetch tutor details
            $tutorStmt = $pdo->prepare("SELECT name FROM tutors WHERE tutor_id = ?");
            $tutorStmt->execute([$session['tutor_id']]);
            $tutor = $tutorStmt->fetch(PDO::FETCH_ASSOC);
            
            // Fetch student details
            $studentStmt = $pdo->prepare("SELECT name FROM students WHERE student_id = ?");
            $studentStmt->execute([$session['student_id']]);
            $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

            // Fetch subject details
            $subjectStmt = $pdo->prepare("SELECT subject_name FROM subjects WHERE subject_id = ?");
            $subjectStmt->execute([$session['subject_id']]);
            $subject = $subjectStmt->fetch(PDO::FETCH_ASSOC);


            // Merge session, tutor, subject ,and student details
            $mergedSession = array_merge($session, ['tutor_name' => $tutor['name'], 'student_name' => $student['name'], 'subject_name'=> $subject['subject_name']]);
            
            // Store merged session in result array
            $result[] = $mergedSession;

            // Store tutor and student details separately
            $tutors[$session['tutor_id']] = $tutor['name'];
            $students[$session['student_id']] = $student['name'];
            $subject[$session['subject_id']] = $subject['subject_name'];
        }

        // Combine all results
        $combinedResult = array_map(function($session) use (&$tutors, &$students) {
            return [
                'session_id' => $session['session_id'],
                'tutor_id' => $session['tutor_id'],
                'tutor_name' => $tutors[$session['tutor_id']],
                'student_id' => $session['student_id'],
                'student_name' => $students[$session['student_id']],
                'subject_id' => $session['subject_id'],
                'subject_name' => $session['subject_name'],
                'session_date' => $session['session_date'],
                'session_time' => $session['session_time'],
                'status' => $session['status'],
                'feedback_id' => $session['feedback_id'],
                'created_at' => $session['created_at']
            ];
        }, $result);

        http_response_code(200);
        echo json_encode($combinedResult);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}




?>
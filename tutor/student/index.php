<?php


header("Access-Control-Allow-Origin: *");

header('Content-Type: *');

header('Access-Control-Allow-Heagers: *');

header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: * ");



require_once("./../conn.php");

$method = $_SERVER['REQUEST_METHOD'];



switch ($method) {
case 'GET':
    if (isset($_GET['student_id'])) {
        $student_id = $_GET['student_id'];
        
        $stmt = $pdo->prepare("SELECT s.*, p.program_name, p.description
                              FROM students s
                              LEFT JOIN programs p ON s.program_id = p.program_id
                              LEFT JOIN departments d on s.department_id = d.department_id
                              WHERE s.student_id = :student_id");
        
        $stmt->execute([':student_id' => $student_id]);
        
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($student) {
            http_response_code(200);
            echo json_encode($student);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Student not found']);
        }
    } else {
        echo json_encode(['error' => 'No student_id provided']);
    }
    break;
       default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
  }
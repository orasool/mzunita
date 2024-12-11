<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: *");

require_once("./../conn.php");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['tutor_id'])) {
            $tutor_id = $_GET['tutor_id'];
            
            // Prepare the SQL query to fetch tutor details, expertise, and qualifications
            $query = "
                SELECT 
                   t.*,
                    q.*,
                    e.*
                 
                FROM tutors t
                
                LEFT JOIN qualifications q ON t.qualification = q.qualification_id
              
                LEFT JOIN expertise e ON t.expertise = e.expertise_id
                WHERE t.tutor_id = :tutor_id
                
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute([':tutor_id' => $tutor_id]);

            $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tutor) {
                http_response_code(200);
                echo json_encode($tutor);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Tutor not found']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'No tutor_id provided']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

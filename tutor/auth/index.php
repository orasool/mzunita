<?php


header("Access-Control-Allow-Origin: *");

header('Content-Type: *');

header('Access-Control-Allow-Heagers: *');

header("Access-Control-Allow-Methods: * ");
header("Access-Control-Allow-Headers: * ");



require_once("./../conn.php");

$method = $_SERVER['REQUEST_METHOD'];



switch ($method) {
case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (!isset($data['email']) || !isset($data['password'])) {
            echo json_encode(['error' => 'Missing email or password']);
            break;
        }
    
        $email = $data['email'];
        $password = $data['password'];
    
        try {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE email = :email");
            $stmt->execute([':email' => $email]);
    
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$student) {
                echo json_encode(['error' => 'User not found']);
                break;
            }
    
            if (md5($password) !== $student['password_hash']) {
                echo json_encode(['error' => 'Incorrect password']);
                break;
            }
    
            http_response_code(200);
            echo json_encode(true);
    
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        break;



case 'GET':
 

if (isset($_GET['email']) && isset($_GET['password'])) {
        $email = $_GET['email'];
        $password = $_GET['password'];



  $stmt = $pdo->query("SELECT * FROM students  WHERE email= $email");
        
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        
        $result = $students[0];


if (md5($password) == $result['password_hash']) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}

  }else{
    echo json_encode(['error' => 'no data passed']);
  }
    break;


       default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
  }
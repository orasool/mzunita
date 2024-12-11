<?php


header("Access-Control-Allow-Origin: *");

header('Content-Type: *');

header('Access-Control-Allow-Heagers: *');

header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: * ");



require_once("./../../conn.php");

$method = $_SERVER['REQUEST_METHOD'];



switch ($method) {
case 'GET':
    if (isset($_GET['email']) && isset($_GET['password'])) {
        $email = $_GET['email'];
        $password = $_GET['password'];

        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");

      
        http_response_code(200);
        
      
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
        if (md5($password) === $admin[0]['password_hash'] ) {
            echo json_encode($admin[0]);
        } else {
            echo json_encode(false);
        }
    } else {
        echo json_encode(['error' => 'no data passed']);
    }
    break;

       default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
  }
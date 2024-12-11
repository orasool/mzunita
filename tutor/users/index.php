<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: *");

require './../conn.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query('SELECT * FROM students');
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $registration_number = $data['registration_number'];
        $name = $data['name'];
        $program = $data['program'];
        $email = $data['email'];
        $phone_number = $data['phone_number'];

        $stmt = $pdo->prepare('INSERT INTO students (registration_number, name, program, email, phone_number, created_at) 
                              VALUES (:registration_number, :name, :program, :email, :phone_number, NOW())');
        $stmt->execute([
            ':registration_number' => $registration_number,
            ':name' => $name,
            ':program' => $program,
            ':email' => $email,
            ':phone_number' => $phone_number
        ]);

        echo json_encode(['message' => 'Student added successfully']);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $registration_number = $data['registration_number'] ?? null;
        $name = $data['name'] ?? null;
        $program = $data['program'] ?? null;
        $email = $data['email'] ?? null;
        $phone_number = $data['phone_number'] ?? null;

        $update_fields = [];
        if ($registration_number !== null) {
            $update_fields[] = 'registration_number = :registration_number';
        }
        if ($name !== null) {
            $update_fields[] = 'name = :name';
        }
        if ($program !== null) {
            $update_fields[] = 'program = :program';
        }
        if ($email !== null) {
            $update_fields[] = 'email = :email';
        }
        if ($phone_number !== null) {
            $update_fields[] = 'phone_number = :phone_number';
        }

        $query = implode(', ', $update_fields);
        $stmt = $pdo->prepare("UPDATE students SET $query WHERE id = :id");
        $stmt->execute([
            ':id' => $id,
            ':registration_number' => $registration_number,
            ':name' => $name,
            ':program' => $program,
            ':email' => $email,
            ':phone_number' => $phone_number
        ]);

        echo json_encode(['message' => 'Student updated successfully']);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $stmt = $pdo->prepare('DELETE FROM students WHERE id = :id');
        $stmt->execute([':id' => $id]);
        echo json_encode(['message' => 'Student deleted successfully']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
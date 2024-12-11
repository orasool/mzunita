<?php

require_once "./../../conn.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

function handleError($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);


    // Register a student
    if (!isset($_GET['registration_number']) || !isset($_GET['full_name']) || !isset($_GET['email']) || !isset($_GET['phone_number']) ||
        !isset($_GET['password']) || !isset($_GET['department_id']) || !isset($_GET['program_id']) ||
        !isset($_GET['year_of_study']) || !isset($_GET['academic_level']) || !isset($_GET['age']) ||
        !isset($_GET['nationality']) || !isset($_GET['language']) || !isset($_GET['technical_skills']) ||
        !isset($_GET['hobbies']) || !isset($_GET['goals_motivation'])) {
        handleError("Missing required fields");
    }

    try {
        $password_hash = md5($_GET['password']);
        $stmt = $pdo->prepare("INSERT INTO students (registration_number, name, email, phone_number, password_hash, department_id, program_id, year_of_study, academic_level, date_of_birth, nationality, language, technical_skills, hobbies, goals_motivation, created_at)
                               VALUES (:registration_number, :full_name, :email, :phone_number, :password_hash, :department_id, :program_id, :year_of_study, :academic_level, :age, :nationality, :language, :technical_skills, :hobbies, :goals_motivation, NOW())");

        $stmt->execute([
            ':registration_number' => $_GET['registration_number'],
            ':full_name' => $_GET['full_name'],
            ':email' => $_GET['email'],
            ':phone_number' => $_GET['phone_number'],
            ':password_hash' => $password_hash,
            ':department_id' => $_GET['department_id'],
            ':program_id' => $_GET['program_id'],
            ':year_of_study' => $_GET['year_of_study'],
            ':academic_level' => $_GET['academic_level'],
            ':age' => $_GET['age'],
            ':nationality' => $_GET['nationality'],
            ':language' => $_GET['language'],
            ':technical_skills' => $_GET['technical_skills'],
            ':hobbies' => $_GET['hobbies'],
            ':goals_motivation' => $_GET['goals_motivation']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Student registered successfully']);
        } else {
            handleError("Failed to register student");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

// PUT and DELETE requests...

$pdo = null;
?>

<?php

require_once "./../../conn.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

function handleError($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

// Check if required GET parameters are present
// if (!isset($_GET['tutor_id']) || !isset($_GET['name']) || !isset($_GET['phone_number']) || 
//     !isset($_GET['password']) || !isset($_GET['qualification']) || !isset($_GET['expertise']) ||
//     !isset($_GET['year_of_graduation']) || !isset($_GET['years_experience'])) {
//     handleError("Missing required fields");
// }
try {
    // Hash password using md5()
    $password_hash = md5($_GET['password']);
    
    // Prepare SQL statement to insert tutor data
    $stmt = $pdo->prepare("INSERT INTO tutors (email, name, phone_number, password_hash, qualification, expertise, year_of_graduation, years_experience, created_at)
                           VALUES (:email, :name, :phone_number, :password_hash, :qualification, :expertise, :year_of_graduation, :years_experience, NOW())");

    // Execute statement with data from $_GET
    $stmt->execute([
        ':email' => $_GET['tutor_id'],
        ':name' => $_GET['name'],
        ':phone_number' => $_GET['phone_number'],
        ':password_hash' => $password_hash,
        ':qualification' => $_GET['qualification'],
        ':expertise' => $_GET['expertise'],
        ':year_of_graduation' => $_GET['year_of_graduation'],
        ':years_experience' => $_GET['years_experience']
    ]);

    // Check if the insert was successful
    if ($stmt->rowCount() > 0) {
        http_response_code(201);
        echo json_encode(['message' => 'Tutor registered successfully']);
    } else {
        handleError("Failed to register tutor");
    }
} catch (PDOException $e) {
    handleError("Database error: " . $e->getMessage());
}

// Close the PDO connection
$pdo = null;
?>

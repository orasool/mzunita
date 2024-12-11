<?php

// Import your config.php file
require_once "./../../conn.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Function to handle errors
function handleError($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Handle GET request to fetch all tutor availability
        try {
            $stmt = $pdo->prepare("
                SELECT 
                    a.*,
                    t.tutor_id,
                    t.name,
                    t.email,
                    t.phone_number,
                    t.created_at,
                    t.password_hash,
                    q.qualification_name AS qualification,
                    e.expertise_name AS expertise,
                    e.*,
                    t.year_of_graduation,
                    t.years_experience
                FROM 
                    availability a
                JOIN tutors t ON a.tutor_id = t.tutor_id
                LEFT JOIN qualifications q ON t.qualification = q.qualification_id
                LEFT JOIN expertise e ON t.expertise = e.expertise_id
                WHERE status = 'Available'
                ORDER BY 
                    t.name,
                    a.available_date,
                    a.available_time
            ");

            $stmt->execute();

            $availabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($availabilities) {
                http_response_code(200);
                echo json_encode($availabilities);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'No availability found']);
            }
        } catch (PDOException $e) {
            handleError("Database error: " . $e->getMessage());
        }

        break;

    // Other cases (POST, PUT, DELETE) remain unchanged
    case 'POST':
        // ... (unchanged)
    case 'PUT':
        // ... (unchanged)
    case 'DELETE':
        // ... (unchanged)

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

// Close the PDO connection
$pdo = null;

?>

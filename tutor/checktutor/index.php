<?php

// Import your config.php file
require_once "./../conn.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Function to handle errors
function handleError($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the expertise_name from the query parameters
    

   

    try {
        // Step 1: Search for expertise_id using expertise_name in the expertise table
        $stmt = $pdo->prepare("SELECT expertise_id FROM expertise");
        $stmt->execute();

        $expertise = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$expertise) {
            http_response_code(404);
            echo json_encode(['message' => 'No expertise found with that name']);
            exit;
        }

        $expertiseId = $expertise['expertise_id'];

        // Step 2: Find all tutor IDs from tutor_expertise table with that expertise_id
        $stmt = $pdo->prepare("SELECT tutor_id FROM tutor_expertise WHERE expertise_id = :expertise_id");
        $stmt->execute([':expertise_id' => $expertiseId]);

        $tutorIds = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Fetch only the tutor IDs

        if (empty($tutorIds)) {
            http_response_code(404);
            echo json_encode(['message' => 'No tutors found for that expertise']);
            exit;
        }

        // Step 3: Check the availability table for those tutor IDs with status 'Available'
        $tutorIdsPlaceholder = implode(',', array_fill(0, count($tutorIds), '?')); // Create a placeholder for IN clause
        $stmt = $pdo->prepare("
            SELECT tutor_id, available_date, available_time , availability_id
            FROM availability 
            WHERE tutor_id IN ($tutorIdsPlaceholder) AND status = 'Available'
        ");
        $stmt->execute($tutorIds);

        $availableTutors = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch tutor IDs along with availability data

        if (empty($availableTutors)) {
            http_response_code(404);
            echo json_encode(['message' => 'No available tutors found for that expertise']);
            exit;
        }

        // Step 4: Retrieve the tutor names from tutors table based on available tutor IDs
        $availableTutorIds = array_column($availableTutors, 'tutor_id');
        $availableTutorIdsPlaceholder = implode(',', array_fill(0, count($availableTutorIds), '?'));

        $stmt = $pdo->prepare("SELECT tutor_id, name FROM tutors WHERE tutor_id IN ($availableTutorIdsPlaceholder)");
        $stmt->execute($availableTutorIds);

        $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Merge the tutor names with their availability data
        $result = [];
        foreach ($availableTutors as $availability) {
            foreach ($tutors as $tutor) {
                if ($tutor['tutor_id'] == $availability['tutor_id']) {
                    $result[] = [
                        'tutor_id'=>$tutor['tutor_id'],
                        'name' => $tutor['name'],
                        'available_date' => $availability['available_date'],
                        'available_time' => $availability['available_time'],
                        'available_id'=> $availability['availability_id']
                    ];
                }
            }
        }

        // Return the list of available tutors with their availability
        http_response_code(200);
        echo json_encode($result);

    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>

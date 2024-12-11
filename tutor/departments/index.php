<?php

// Import your db_connection.php file
require_once "./../conn.php";

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

// GET request handler for fetching all departments with associated programs
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Query to join departments and programs tables
        $sql = "
            SELECT d.department_id, d.department_name, d.department_description,d.department_code,
                   p.program_id, p.program_name
            FROM departments d
            LEFT JOIN programs p ON d.department_id = p.department_id
            ORDER BY d.department_id, p.program_id
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group results by department
        $groupedResults = [];
        foreach ($results as $row) {
            $deptId = $row['department_id'];
            if (!isset($groupedResults[$deptId])) {
                $groupedResults[$deptId] = [
                    'department_id' => $deptId,
                    'department_name' => $row['department_name'],
                    'department_description' => $row['department_description'],
                    'department_code' => $row['department_code'],
                    'programs' => []
                ];
            }
            if ($row['program_id'] !== null) {
                $groupedResults[$deptId]['programs'][] = [
                    'program_id' => $row['program_id'],
                    'program_name' => $row['program_name']
                ];
            }
        }

        http_response_code(200);
        echo json_encode(array_values($groupedResults));
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
<?php
// Import your db_connection.php file
require_once("./../conn.php");

// Validate input data
$data = json_decode(file_get_contents('php://input'), true);

$session_id = $data['session_id'];
 

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Get session details
        $stmt = $pdo->prepare("SELECT * FROM sessions WHERE session_id = ?");
        $stmt->execute([$session_id]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$session) {
            throw new Exception("Session not found");
        }

        // Update session status
        $stmt = $pdo->prepare("UPDATE sessions SET status = 3 WHERE session_id = ?");
        $stmt->execute([$session_id]);


        // Update availability status
        $stmt = $pdo->prepare("
            UPDATE availability 
            SET status = 'Available' 
            WHERE tutor_id = ? AND available_date = ? AND available_time = ?
        ");
        $stmt->execute([
            $session['tutor_id'],
            $session['session_date'],
            $session['session_time']
        ]);

        // Commit transaction
        $pdo->commit();

        return true;
    } catch (Exception $e) {
        // Rollback transaction if there was an error
        $pdo->rollBack();
        echo "An error occurred: " . $e->getMessage();
        return false;
    } finally {
        // Close the connection
        $pdo = null;
    }

echo json_encode(['message' => "cancelled"]);

?>
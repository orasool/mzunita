<?php
// Import your db_connection.php file
require_once("./../conn.php");
// Validate input data
$data = json_decode(file_get_contents('php://input'), true);



$chat_id = $_GET['chat_id']; // Example chat ID (You can pass it dynamically)

try {
    // Prepare the SQL query
    $stmt = $pdo->prepare("
        SELECT *
        FROM Messages m
        JOIN Chats c ON m.chat_id = c.chat_id
        WHERE c.chat_id = :chat_id
        ORDER BY m.sent_at;
    ");

    // Execute the query
    $stmt->execute(['chat_id' => $chat_id]);

    // Fetch and display the results
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  echo json_encode($messages);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


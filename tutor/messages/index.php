<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Import your db_connection.php file
require_once("./../conn.php");

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

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];



// Switch to handle different request methods
switch ($method) {
    
    // POST request handler for creating a new message
   case 'POST':
    try {
        // Get the request body
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Debug: Print the received data
        error_log("Received data: " . json_encode($data));

        if (!isset($data['message_content']) || !isset($data['sender_id']) || !isset($data['receiver_id']) || !isset($data['sent_at'])) {
            throw new Exception("Missing required fields");
        }

        // Validate sender_id and receiver_id
        if (!is_numeric($data['sender_id']) || !is_numeric($data['receiver_id'])) {
            throw new Exception("Invalid sender_id or receiver_id");
        }

        $pdo->beginTransaction();
        $transactionStarted = true;

        // Insert conversation
        $stmt = $pdo->prepare("
            INSERT INTO conversations(subject, started_at, last_message_at)
            VALUES (:subject, :started_at, :last_message_at)
        ");
        $stmt->execute([
            ':subject' => 'New Conversation',
            ':started_at' => date('Y-m-d H:i:s'),
            ':last_message_at' => date('Y-m-d H:i:s')
        ]);
        $conversation_id = $pdo->lastInsertId();
        
        error_log("Conversation inserted successfully");

        // Insert participants
        $stmt = $pdo->prepare("
            INSERT INTO participants(conversation_id, tutor_id, student_id, role)
            VALUES (:conversation_id, :tutor_id, :student_id, :role)
        ");
        $stmt->execute([
            ':conversation_id' => $conversation_id,
            ':tutor_id' => $data['sender_id'],
            ':student_id' => $data['receiver_id'],
            ':role' => 'tutor'
        ]);

        error_log("Participants inserted successfully");

        // Insert message
        $stmt = $pdo->prepare("
            INSERT INTO messages(conversation_id, sender_id, sender_role, message_content, sent_at)
            VALUES (:conversation_id, :sender_id, :sender_role, :message_content, :sent_at)
        ");
        $stmt->execute([
            ':conversation_id' => $conversation_id,
            ':sender_id' => $data['sender_id'],
            ':sender_role' => 'tutor',
            ':message_content' => $data['message_content'],
            ':sent_at' => $data['sent_at']
        ]);

        error_log("Message inserted successfully");

        $pdo->commit();

        http_response_code(201);
        echo json_encode([
            'message' => 'Conversation started and message sent successfully',
            'conversation_id' => $conversation_id,
            'sender_id' => $data['sender_id'],
            'receiver_id' => $data['receiver_id']
        ]);
    } catch (Exception $e) {
        if (isset($transactionStarted) && $transactionStarted) {
            $pdo->rollBack();
        }
        handleError("An error occurred: " . $e->getMessage());
    }
    break;
    // GET request handler


    // GET request handler
    case 'GET':
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'get_conversations':
                    try {
                        $stmt = $pdo->query("
                            SELECT c.id, c.subject, c.started_at, c.last_message_at,
                                   COUNT(DISTINCT p.conversation_id) as participant_count,
                                   COUNT(m.id) as message_count
                            FROM conversations c
                            LEFT JOIN participants p ON c.id = p.conversation_id
                            LEFT JOIN messages m ON c.id = m.conversation_id
                            GROUP BY c.id
                            ORDER BY c.last_message_at DESC
                        ");
                        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        http_response_code(200);
                        echo json_encode($conversations);
                    } catch (PDOException $e) {
                        handleError("Database error: " . $e->getMessage());
                    }
                    break;

                case 'get_messages':
                    if (isset($_GET['conversation_id'])) {
                        try {
                            $conversation_id = $_GET['conversation_id'];
                            $stmt = $pdo->prepare("
                                SELECT m.id, m.conversation_id, m.sender_id, m.sender_role, m.message_content, m.sent_at,
                                       COALESCE(t.name, s.name) AS sender_name
                                FROM messages m
                                LEFT JOIN tutors t ON m.sender_id = t.tutor_id
                                LEFT JOIN students s ON m.sender_id = s.student_id
                                WHERE m.conversation_id = :conversation_id
                                ORDER BY m.sent_at ASC
                            ");
                            $stmt->execute([':conversation_id' => $conversation_id]);
                            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            http_response_code(200);
                            echo json_encode($messages);
                        } catch (PDOException $e) {
                            handleError("Database error: " . $e->getMessage());
                        }
                    } else {
                        handleError("Missing required parameter: conversation_id");
                    }
                    break;

                case 'get_participants':
                    if (isset($_GET['conversation_id'])) {
                        try {
                            $conversation_id = $_GET['conversation_id'];
                            $stmt = $pdo->prepare("
                                SELECT p.conversation_id, p.tutor_id, p.student_id, p.role,
                                       COALESCE(t.name, s.name) AS participant_name
                                FROM participants p
                                LEFT JOIN tutors t ON p.tutor_id = t.tutor_id
                                LEFT JOIN students s ON p.student_id = s.student_id
                                WHERE p.conversation_id = :conversation_id
                            ");
                            $stmt->execute([':conversation_id' => $conversation_id]);
                            $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            http_response_code(200);
                            echo json_encode($participants);
                        } catch (PDOException $e) {
                            handleError("Database error: " . $e->getMessage());
                        }
                    } else {
                        handleError("Missing required parameter: conversation_id");
                    }
                    break;

                case 'get_student_messages':
                    if (isset($_GET['student_id'])) {
                        try {
                            $user_id = $_GET['student_id'];
                            $stmt = $pdo->prepare("
                                SELECT m.message_id, m.conversation_id, m.sender_id, m.sender_role, m.message_content, m.sent_at,
                                       c.subject AS conversation_subject,
                                       COALESCE(t.name, s.name) AS sender_name
                                FROM messages m
                                LEFT JOIN conversations c ON m.conversation_id = c.conversation_id
                                LEFT JOIN tutors t ON m.sender_id = t.tutor_id
                                LEFT JOIN students s ON m.sender_id = s.student_id
                                WHERE m.sender_id = :user_id
                                ORDER BY m.sent_at DESC
                            ");
                            $stmt->execute([':user_id' => $user_id]);
                            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            http_response_code(200);
                            echo json_encode($messages);
                        } catch (PDOException $e) {
                            handleError("Database error: " . $e->getMessage());
                        }
                    } else {
                        handleError("Missing required parameter: user_id");
                    }
                    break;

                case 'get_tutor_messages':
                    if (isset($_GET['user_id'])) {
                        try {
                            $user_id = $_GET['user_id'];
                            $stmt = $pdo->prepare("
                                SELECT m.id, m.conversation_id, m.sender_id, m.sender_role, m.message_content, m.sent_at,
                                       c.subject AS conversation_subject,
                                       COALESCE(t.name, s.name) AS sender_name
                                FROM messages m
                                LEFT JOIN conversations c ON m.conversation_id = c.id
                                LEFT JOIN tutors t ON m.sender_id = t.tutor_id
                                LEFT JOIN students s ON m.sender_id = s.student_id
                                WHERE m.receiver_id = :user_id OR m.sender_id = :user_id
                                ORDER BY m.sent_at DESC
                            ");
                            $stmt->execute([':user_id' => $user_id]);
                            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            http_response_code(200);
                            echo json_encode($messages);
                        } catch (PDOException $e) {
                            handleError("Database error: " . $e->getMessage());
                        }
                    } else {
                        handleError("Missing required parameter: user_id");
                    }
                    break;

                case 'get_conversation_details':
                    if (isset($_GET['conversation_id'])) {
                        try {
                            $conversation_id = $_GET['conversation_id'];
                            $stmt = $pdo->prepare("
                                SELECT c.id, c.subject, c.started_at, c.last_message_at,
                                       COUNT(DISTINCT p.conversation_id) as participant_count,
                                       COUNT(m.id) as message_count
                                FROM conversations c
                                LEFT JOIN participants p ON c.id = p.conversation_id
                                LEFT JOIN messages m ON c.id = m.conversation_id
                                WHERE c.id = :conversation_id
                                GROUP BY c.id
                            ");
                            $stmt->execute([':conversation_id' => $conversation_id]);
                            $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
                            http_response_code(200);
                            echo json_encode($conversation);
                        } catch (PDOException $e) {
                            handleError("Database error: " . $e->getMessage());
                        }
                    } else {
                        handleError("Missing required parameter: conversation_id");
                    }
                    break;

                default:
                    handleError("Invalid action specified");
            }
        } else {
            handleError("Missing required parameter: action");
        }
        break;

    // PUT request handler for updating a message
    case 'PUT':
        if (isset($_GET['id'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['sender_id']) || !isset($data['receiver_id']) || !isset($data['message_content']) || !isset($data['sent_at'])) {
                handleError("Missing required fields: sender_id, receiver_id, message_content, sent_at");
            }
            try {
                $id = $_GET['id'];
                $stmt = $pdo->prepare("
                    UPDATE messages 
                    SET sender_id = :sender_id, receiver_id = :receiver_id, message_content = :message_content, sent_at = :sent_at 
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':id' => $id,
                    ':sender_id' => $data['sender_id'],
                    ':receiver_id' => $data['receiver_id'],
                    ':message_content' => $data['message_content'],
                    ':sent_at' => $data['sent_at']
                ]);
                if ($stmt->rowCount() > 0) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Message updated successfully']);
                } else {
                    handleError("Failed to update message");
                }
            } catch (PDOException $e) {
                handleError("Database error: " . $e->getMessage());
            }
        } else {
            handleError("Missing required parameter: id");
        }
        break;

    // DELETE request handler for deleting a message
    case 'DELETE':
        if (isset($_GET['id'])) {
            try {
                $id = $_GET['id'];
                $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
                $stmt->execute([':id' => $id]);
                if ($stmt->rowCount() > 0) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Message deleted successfully']);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Message not found']);
                }
            } catch (PDOException $e) {
                handleError("Database error: " . $e->getMessage());
            }
        } else {
            handleError("Missing required parameter: id");
        }
        break;

    // Default case for unsupported methods
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}


// Close the PDO connection
$pdo = null;

?>

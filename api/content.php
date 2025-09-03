<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            getContent($db);
            break;
        case 'POST':
            updateContent($db);
            break;
        case 'PUT':
            updateContent($db);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getContent($db) {
    $stmt = $db->query("SELECT key_name, content FROM content");
    $content = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $content[$row['key_name']] = $row['content'];
    }
    
    echo json_encode($content);
}

function updateContent($db) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }
    
    $db->beginTransaction();
    
    try {
        $stmt = $db->prepare("INSERT OR REPLACE INTO content (key_name, content, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP)");
        
        foreach ($input as $key => $value) {
            $stmt->execute([$key, $value]);
        }
        
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Content updated successfully']);
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}
?>
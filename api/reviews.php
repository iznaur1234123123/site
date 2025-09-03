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
            getReviews($db);
            break;
        case 'POST':
            addReview($db);
            break;
        case 'PUT':
            updateReview($db);
            break;
        case 'DELETE':
            deleteReview($db);
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

function getReviews($db) {
    $stmt = $db->query("SELECT * FROM reviews ORDER BY created_at DESC");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($reviews);
}

function addReview($db) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['name']) || !isset($input['text']) || !isset($input['event'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }
    
    $stmt = $db->prepare("INSERT INTO reviews (name, text, event, avatar, rating) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $input['name'],
        $input['text'],
        $input['event'],
        $input['avatar'] ?? 'uploads/default-avatar.jpg',
        $input['rating'] ?? 5
    ]);
    
    echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
}

function updateReview($db) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing ID']);
        return;
    }
    
    $stmt = $db->prepare("UPDATE reviews SET name = ?, text = ?, event = ?, avatar = ?, rating = ? WHERE id = ?");
    $stmt->execute([
        $input['name'],
        $input['text'],
        $input['event'],
        $input['avatar'] ?? 'uploads/default-avatar.jpg',
        $input['rating'] ?? 5,
        $input['id']
    ]);
    
    echo json_encode(['success' => true]);
}

function deleteReview($db) {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing ID']);
        return;
    }
    
    $stmt = $db->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true]);
}
?>
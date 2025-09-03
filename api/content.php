<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/database.php';

try {
    $db = new Database();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Получение контента
        $content = $db->getContent();
        echo json_encode($content, JSON_UNESCAPED_UNICODE);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Обновление контента (только для авторизованных пользователей)
        session_start();
        
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Необходима авторизация']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['key']) && isset($input['value'])) {
            $success = $db->updateContent($input['key'], $input['value']);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Контент обновлен']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка обновления']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        }
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сервера: ' . $e->getMessage()]);
}
?>

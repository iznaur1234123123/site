<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/database.php';

try {
    $db = new Database();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Получение портфолио
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        
        $portfolio = $db->getPortfolio($offset, $limit);
        echo json_encode($portfolio, JSON_UNESCAPED_UNICODE);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Добавление нового элемента портфолио
        session_start();
        
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Необходима авторизация']);
            exit;
        }
        
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $category = $_POST['category'] ?? 'general';
        
        // Обработка загруженного изображения
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../uploads/portfolio/';
            
            // Создаем директорию если её нет
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            // Проверяем тип файла
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array(strtolower($file_extension), $allowed_types)) {
                echo json_encode(['success' => false, 'message' => 'Неподдерживаемый тип файла']);
                exit;
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $image_url = 'uploads/portfolio/' . $file_name;
                
                $success = $db->addPortfolioItem($title, $description, $image_url, $category);
                
                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Элемент добавлен в портфолио']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Ошибка добавления в базу данных']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка загрузки файла']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Изображение не выбрано']);
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Удаление элемента портфолио
        session_start();
        
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Необходима авторизация']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        
        if ($id > 0) {
            $success = $db->deletePortfolioItem($id);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Элемент удален']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка удаления']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        }
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сервера: ' . $e->getMessage()]);
}
?>

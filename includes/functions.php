<?php
require_once 'config/database.php';

// Функция для получения контента из базы данных
function getContent() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM content");
        $content = [];
        
        while ($row = $stmt->fetch()) {
            $content[$row['section']] = $row;
        }
        
        return $content;
    } catch(PDOException $e) {
        return [];
    }
}

// Функция для обновления контента
function updateContent($section, $data) {
    global $pdo;
    
    try {
        $sql = "UPDATE content SET title = ?, subtitle = ?, description = ?, content = ?, image = ?, button_text = ?, button_link = ? WHERE section = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['subtitle'],
            $data['description'],
            $data['content'],
            $data['image'],
            $data['button_text'],
            $data['button_link'],
            $section
        ]);
    } catch(PDOException $e) {
        return false;
    }
}

// Функция для проверки авторизации админа
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Функция для авторизации админа
function loginAdmin($username, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            return true;
        }
        
        return false;
    } catch(PDOException $e) {
        return false;
    }
}

// Функция для выхода из админ-панели
function logoutAdmin() {
    session_destroy();
    header('Location: admin/login.php');
    exit;
}

// Функция для сохранения сообщения из контактной формы
function saveContactMessage($name, $email, $phone, $message) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $phone, $message]);
    } catch(PDOException $e) {
        return false;
    }
}

// Функция для получения всех сообщений
function getContactMessages() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

// Функция для безопасного вывода HTML
function safeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
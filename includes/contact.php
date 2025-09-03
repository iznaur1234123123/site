<?php
header('Content-Type: application/json');

require_once 'config/database.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
    exit;
}

// Получаем данные из формы
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

// Валидация
$errors = [];

if (empty($name)) {
    $errors[] = 'Имя обязательно для заполнения';
}

if (empty($email)) {
    $errors[] = 'Email обязателен для заполнения';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Некорректный email адрес';
}

if (empty($message)) {
    $errors[] = 'Сообщение обязательно для заполнения';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Сохраняем сообщение в базу данных
if (saveContactMessage($name, $email, $phone, $message)) {
    // Отправляем email уведомление (опционально)
    $to = 'info@bulatdokuev.com'; // Замените на реальный email
    $subject = 'Новое сообщение с сайта от ' . $name;
    $emailMessage = "
        Имя: $name
        Email: $email
        Телефон: $phone
        
        Сообщение:
        $message
    ";
    
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // mail($to, $subject, $emailMessage, $headers); // Раскомментируйте для отправки email
    
    echo json_encode(['success' => true, 'message' => 'Сообщение успешно отправлено!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении сообщения']);
}
?>
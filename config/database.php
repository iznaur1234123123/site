<?php
// Конфигурация базы данных
$host = 'localhost';
$dbname = 'bulat_website';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Создание таблиц если они не существуют
$createTables = "
CREATE TABLE IF NOT EXISTS content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    description TEXT,
    content TEXT,
    image VARCHAR(255),
    button_text VARCHAR(100),
    button_link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

try {
    $pdo->exec($createTables);
    
    // Добавляем начальный контент
    $initialContent = [
        ['hero', 'Булат Докуев', 'Профессиональный ведущий мероприятий', 'Создаю незабываемые моменты для ваших особенных событий'],
        ['about', 'О ведущем', 'Профессионал с многолетним опытом', 'Булат Докуев - опытный ведущий с более чем 10-летним стажем работы в сфере организации и проведения мероприятий. Специализируюсь на корпоративных событиях, свадьбах, юбилеях и других торжественных мероприятиях.'],
        ['services', 'Услуги', 'Полный спектр услуг ведущего', ''],
        ['portfolio', 'Портфолио', 'Примеры проведенных мероприятий', ''],
        ['contact', 'Контакты', 'Свяжитесь со мной для обсуждения вашего мероприятия', '']
    ];
    
    foreach ($initialContent as $content) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO content (section, title, subtitle, description) VALUES (?, ?, ?, ?)");
        $stmt->execute($content);
    }
    
    // Создаем админа по умолчанию
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO admins (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $adminPassword, 'admin@bulatdokuev.com']);
    
} catch(PDOException $e) {
    // Таблицы уже существуют или другая ошибка
}
?>
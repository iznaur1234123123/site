<?php
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$db = new Database();

// Получаем статистику
$content_count = count($db->getContent());
$portfolio_count = count($db->getPortfolio(0, 1000));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель управления - Булат Докуев</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Боковая панель -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>Панель управления</h2>
                <p>Булат Докуев</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Главная</a></li>
                <li><a href="content.php"><i class="fas fa-edit"></i> Контент</a></li>
                <li><a href="portfolio.php"><i class="fas fa-images"></i> Портфолио</a></li>
                <li><a href="files.php"><i class="fas fa-folder"></i> Файлы</a></li>
                <li><a href="upload_image.php"><i class="fas fa-camera"></i> Изображения</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Настройки</a></li>
                <li><a href="../" target="_blank"><i class="fas fa-eye"></i> Просмотр сайта</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Выход</a></li>
            </ul>
        </nav>

        <!-- Основной контент -->
        <main class="main-content">
            <header class="content-header">
                <h1>Добро пожаловать в панель управления</h1>
                <div class="user-info">
                    <span>Администратор</span>
                    <a href="logout.php" class="btn btn-outline">Выйти</a>
                </div>
            </header>

            <!-- Статистические карточки -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $content_count ?></h3>
                        <p>Элементов контента</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $portfolio_count ?></h3>
                        <p>Работ в портфолио</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Online</h3>
                        <p>Сайт активен</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <h3>100%</h3>
                        <p>Готовность сайта</p>
                    </div>
                </div>
            </div>

            <!-- Быстрые действия -->
            <div class="quick-actions">
                <h2>Быстрые действия</h2>
                <div class="actions-grid">
                    <a href="content.php" class="action-card">
                        <i class="fas fa-edit"></i>
                        <h3>Редактировать контент</h3>
                        <p>Изменить тексты на сайте</p>
                    </a>
                    <a href="portfolio.php" class="action-card">
                        <i class="fas fa-plus"></i>
                        <h3>Добавить работу</h3>
                        <p>Загрузить новое фото в портфолио</p>
                    </a>
                    <a href="upload_image.php" class="action-card">
                        <i class="fas fa-camera"></i>
                        <h3>Загрузить фото</h3>
                        <p>Обновить изображения сайта</p>
                    </a>
                    <a href="settings.php" class="action-card">
                        <i class="fas fa-cog"></i>
                        <h3>Настройки</h3>
                        <p>Безопасность и профиль</p>
                    </a>
                </div>
            </div>


        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>

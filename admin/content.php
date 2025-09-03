<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$db = new Database();

// Обработка обновления контента
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key = $_POST['key'] ?? '';
    $value = $_POST['value'] ?? '';
    
    if ($key && $value !== '') {
        if ($db->updateContent($key, $value)) {
            $success_message = 'Контент успешно обновлен!';
        } else {
            $error_message = 'Ошибка обновления контента';
        }
    }
}

$content = $db->getContent();

// Группировка контента по секциям
$sections = [
    'hero' => ['title' => 'Главный экран', 'items' => []],
    'about' => ['title' => 'О ведущем', 'items' => []],
    'services' => ['title' => 'Услуги', 'items' => []],
    'portfolio' => ['title' => 'Портфолио', 'items' => []],
    'contact' => ['title' => 'Контакты', 'items' => []],
    'social' => ['title' => 'Социальные сети', 'items' => []]
];

foreach ($content as $key => $value) {
    $section = 'other';
    foreach (array_keys($sections) as $sec) {
        if (strpos($key, $sec) === 0) {
            $section = $sec;
            break;
        }
    }
    
    if ($section === 'other') {
        if (!isset($sections['other'])) {
            $sections['other'] = ['title' => 'Прочее', 'items' => []];
        }
    }
    
    $sections[$section]['items'][$key] = $value;
}

// Удаляем пустые секции
$sections = array_filter($sections, function($section) {
    return !empty($section['items']);
});
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление контентом - Админ панель</title>
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
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Главная</a></li>
                <li><a href="content.php" class="active"><i class="fas fa-edit"></i> Контент</a></li>
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
                <h1>Управление контентом</h1>
                <div class="user-info">
                    <span>Администратор</span>
                    <a href="logout.php" class="btn btn-outline">Выйти</a>
                </div>
            </header>

            <?php if (isset($success_message)): ?>
            <div class="notification success show" id="notification">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success_message) ?>
            </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
            <div class="notification error show" id="notification">
                <i class="fas fa-exclamation-triangle"></i>
                <?= htmlspecialchars($error_message) ?>
            </div>
            <?php endif; ?>

            <div class="content-sections">
                <?php foreach ($sections as $section_key => $section): ?>
                <div class="section-block">
                    <h2 class="section-title">
                        <i class="fas fa-<?= getSectionIcon($section_key) ?>"></i>
                        <?= htmlspecialchars($section['title']) ?>
                    </h2>
                    
                    <div class="content-grid">
                        <?php foreach ($section['items'] as $key => $value): ?>
                        <div class="content-card">
                            <form method="POST" action="">
                                <input type="hidden" name="key" value="<?= htmlspecialchars($key) ?>">
                                
                                <div class="form-group">
                                    <label for="<?= htmlspecialchars($key) ?>">
                                        <?= formatKeyName($key) ?>
                                        <small>(<?= htmlspecialchars($key) ?>)</small>
                                    </label>
                                    
                                    <?php if (isLongText($value)): ?>
                                    <textarea name="value" id="<?= htmlspecialchars($key) ?>" rows="4"><?= htmlspecialchars($value) ?></textarea>
                                    <?php else: ?>
                                    <input type="text" name="value" id="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Сохранить
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Добавление нового элемента -->
            <div class="section-block">
                <h2 class="section-title">
                    <i class="fas fa-plus"></i>
                    Добавить новый элемент
                </h2>
                
                <div class="content-card">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="new_key">Ключ</label>
                            <input type="text" name="key" id="new_key" placeholder="например: hero_new_text" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_value">Значение</label>
                            <textarea name="value" id="new_value" rows="3" placeholder="Введите текст..." required></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Добавить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
    <script>
        // Автоматическое скрытие уведомлений
        setTimeout(function() {
            const notification = document.getElementById('notification');
            if (notification) {
                notification.classList.remove('show');
            }
        }, 3000);
        
        // Автосохранение при потере фокуса
        document.querySelectorAll('input[name="value"], textarea[name="value"]').forEach(field => {
            let originalValue = field.value;
            
            field.addEventListener('blur', function() {
                if (this.value !== originalValue && this.value.trim() !== '') {
                    // Показываем индикатор сохранения
                    this.style.borderColor = '#f59e0b';
                    
                    // Отправляем форму
                    this.closest('form').submit();
                }
            });
        });
    </script>
</body>
</html>

<?php
function getSectionIcon($section) {
    $icons = [
        'hero' => 'home',
        'about' => 'user',
        'services' => 'briefcase',
        'portfolio' => 'images',
        'contact' => 'envelope',
        'social' => 'share-alt'
    ];
    return $icons[$section] ?? 'file-text';
}

function formatKeyName($key) {
    $names = [
        'hero_title' => 'Заголовок',
        'hero_subtitle' => 'Подзаголовок',
        'hero_description' => 'Описание',
        'hero_photo' => 'Фото',
        'about_title' => 'Заголовок секции',
        'about_subtitle' => 'Подзаголовок секции',
        'contact_phone' => 'Телефон',
        'contact_email' => 'Email',
        'contact_city' => 'Город'
    ];
    
    if (isset($names[$key])) {
        return $names[$key];
    }
    
    // Автоматическое форматирование
    $formatted = str_replace('_', ' ', $key);
    $formatted = ucfirst($formatted);
    
    return $formatted;
}

function isLongText($text) {
    return strlen($text) > 100 || strpos($text, ' ') !== false;
}
?>

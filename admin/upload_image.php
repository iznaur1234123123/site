<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$db = new Database();

$success_message = '';
$error_message = '';

// Обработка загрузки изображения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && isset($_POST['content_key'])) {
    $content_key = $_POST['content_key'];
    $description = $_POST['description'] ?? '';
    
    if ($_FILES['image']['error'] === 0) {
        $upload_dir = '../uploads/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $safe_name = $content_key . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $safe_name;
        
        // Проверка типа файла
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array(strtolower($file_extension), $allowed_types)) {
            $error_message = 'Неподдерживаемый тип файла. Разрешены: JPG, PNG, GIF, WebP';
        } else {
            // Проверка размера (максимум 10MB)
            if ($_FILES['image']['size'] > 10 * 1024 * 1024) {
                $error_message = 'Файл слишком большой (максимум 10MB)';
            } else {
                // Удаляем старое изображение если есть
                $old_image = $db->getContent($content_key);
                if ($old_image && file_exists('../' . $old_image)) {
                    unlink('../' . $old_image);
                }
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                    $image_url = 'uploads/' . $safe_name;
                    
                    if ($db->updateContent($content_key, $image_url)) {
                        $success_message = 'Изображение успешно загружено!';
                    } else {
                        $error_message = 'Ошибка сохранения в базу данных';
                    }
                } else {
                    $error_message = 'Ошибка загрузки файла';
                }
            }
        }
    } else {
        $error_message = 'Выберите изображение для загрузки';
    }
}

// Получаем список изображений для управления
$image_content_keys = [
    'hero_photo' => 'Главное фото ведущего',
    'about_photo' => 'Фото в разделе "О себе"',
    'services_bg' => 'Фон секции услуг',
    'contact_bg' => 'Фон секции контактов'
];

$current_images = [];
foreach ($image_content_keys as $key => $title) {
    $current_images[$key] = $db->getContent($key);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление изображениями - Админ панель</title>
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
                <li><a href="content.php"><i class="fas fa-edit"></i> Контент</a></li>
                <li><a href="portfolio.php"><i class="fas fa-images"></i> Портфолио</a></li>
                <li><a href="files.php"><i class="fas fa-folder"></i> Файлы</a></li>
                <li><a href="upload_image.php" class="active"><i class="fas fa-camera"></i> Изображения</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Настройки</a></li>
                <li><a href="../" target="_blank"><i class="fas fa-eye"></i> Просмотр сайта</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Выход</a></li>
            </ul>
        </nav>

        <!-- Основной контент -->
        <main class="main-content">
            <header class="content-header">
                <h1>Управление изображениями сайта</h1>
                <div class="user-info">
                    <span>Администратор</span>
                    <a href="logout.php" class="btn btn-outline">Выйти</a>
                </div>
            </header>

            <?php if ($success_message): ?>
            <div class="notification success show" id="notification">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success_message) ?>
            </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
            <div class="notification error show" id="notification">
                <i class="fas fa-exclamation-triangle"></i>
                <?= htmlspecialchars($error_message) ?>
            </div>
            <?php endif; ?>

            <div class="images-grid">
                <?php foreach ($image_content_keys as $key => $title): ?>
                <div class="image-card">
                    <h3>
                        <i class="fas fa-image"></i>
                        <?= htmlspecialchars($title) ?>
                    </h3>
                    
                    <div class="current-image">
                        <?php if (!empty($current_images[$key])): ?>
                        <img src="../<?= htmlspecialchars($current_images[$key]) ?>" 
                             alt="<?= htmlspecialchars($title) ?>"
                             onerror="this.src='https://via.placeholder.com/300x200/667eea/white?text=Изображение+не+найдено'">
                        <p class="image-path">
                            <small>Текущий файл: <?= htmlspecialchars($current_images[$key]) ?></small>
                        </p>
                        <?php else: ?>
                        <div class="no-image">
                            <i class="fas fa-image"></i>
                            <p>Изображение не загружено</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data" class="upload-form">
                        <input type="hidden" name="content_key" value="<?= htmlspecialchars($key) ?>">
                        
                        <div class="form-group">
                            <label for="image_<?= $key ?>">
                                <i class="fas fa-upload"></i>
                                Выбрать новое изображение
                            </label>
                            <input type="file" name="image" id="image_<?= $key ?>" 
                                   accept="image/*" required onchange="previewImage(this, '<?= $key ?>')">
                            <small>Поддерживаемые форматы: JPG, PNG, GIF, WebP (максимум 10MB)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="description_<?= $key ?>">Описание (необязательно)</label>
                            <input type="text" name="description" id="description_<?= $key ?>" 
                                   placeholder="Краткое описание изображения">
                        </div>
                        
                        <div class="image-preview" id="preview_<?= $key ?>" style="display: none;">
                            <h4>Предварительный просмотр:</h4>
                            <img id="preview_img_<?= $key ?>" src="" alt="Предварительный просмотр">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i>
                                Загрузить изображение
                            </button>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Информация и советы -->
            <div class="tips-section">
                <h2><i class="fas fa-lightbulb"></i> Советы по загрузке изображений</h2>
                <div class="tips-grid">
                    <div class="tip-card">
                        <h4>Размеры изображений</h4>
                        <ul>
                            <li><strong>Главное фото:</strong> 500x500px или больше</li>
                            <li><strong>Фон секций:</strong> 1920x1080px для лучшего качества</li>
                            <li><strong>Портфолио:</strong> 400x300px минимум</li>
                        </ul>
                    </div>
                    <div class="tip-card">
                        <h4>Качество и форматы</h4>
                        <ul>
                            <li><strong>Формат:</strong> JPG для фото, PNG для графики</li>
                            <li><strong>Размер файла:</strong> до 10MB</li>
                            <li><strong>Качество:</strong> оптимизируйте для веба</li>
                        </ul>
                    </div>
                    <div class="tip-card">
                        <h4>Безопасность</h4>
                        <ul>
                            <li>Старые изображения автоматически удаляются</li>
                            <li>Файлы переименовываются для безопасности</li>
                            <li>Проверка типов файлов</li>
                        </ul>
                    </div>
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

        // Предварительный просмотр изображения
        function previewImage(input, key) {
            const file = input.files[0];
            const preview = document.getElementById('preview_' + key);
            const previewImg = document.getElementById('preview_img_' + key);
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }

        // Drag & Drop для загрузки
        document.querySelectorAll('.upload-form').forEach(form => {
            const fileInput = form.querySelector('input[type="file"]');
            
            form.addEventListener('dragover', (e) => {
                e.preventDefault();
                form.classList.add('dragover');
            });
            
            form.addEventListener('dragleave', () => {
                form.classList.remove('dragover');
            });
            
            form.addEventListener('drop', (e) => {
                e.preventDefault();
                form.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    const event = new Event('change', { bubbles: true });
                    fileInput.dispatchEvent(event);
                }
            });
        });
    </script>

    <style>
        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .image-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .image-card:hover {
            transform: translateY(-5px);
        }

        .image-card h3 {
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .current-image {
            margin-bottom: 25px;
            text-align: center;
        }

        .current-image img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .image-path {
            margin-top: 10px;
            color: #666;
        }

        .no-image {
            padding: 40px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            text-align: center;
            color: #6c757d;
        }

        .no-image i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .upload-form {
            border-top: 1px solid #eee;
            padding-top: 20px;
            transition: all 0.3s ease;
        }

        .upload-form.dragover {
            background: #f0f4ff;
            border: 2px dashed #667eea;
            border-radius: 10px;
        }

        .image-preview {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 150px;
            border-radius: 5px;
        }

        .tips-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .tips-section h2 {
            color: #333;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .tip-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .tip-card h4 {
            color: #333;
            margin-bottom: 15px;
        }

        .tip-card ul {
            list-style: none;
            padding: 0;
        }

        .tip-card li {
            padding: 5px 0;
            color: #555;
        }

        .tip-card li strong {
            color: #333;
        }

        @media (max-width: 768px) {
            .images-grid {
                grid-template-columns: 1fr;
            }
            
            .tips-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>

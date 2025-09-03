<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$db = new Database();

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        // Добавление нового элемента
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? 'general');
        
        if ($title && isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../uploads/portfolio/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                    $image_url = 'uploads/portfolio/' . $file_name;
                    
                    if ($db->addPortfolioItem($title, $description, $image_url, $category)) {
                        $success_message = 'Элемент успешно добавлен в портфолио!';
                    } else {
                        $error_message = 'Ошибка добавления в базу данных';
                    }
                } else {
                    $error_message = 'Ошибка загрузки файла';
                }
            } else {
                $error_message = 'Неподдерживаемый тип файла';
            }
        } else {
            $error_message = 'Заполните все поля и выберите изображение';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        // Удаление элемента
        $id = intval($_POST['id']);
        if ($id > 0) {
            if ($db->deletePortfolioItem($id)) {
                $success_message = 'Элемент удален!';
            } else {
                $error_message = 'Ошибка удаления';
            }
        }
    }
}

$portfolio = $db->getPortfolio(0, 50);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление портфолио - Админ панель</title>
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
                <li><a href="portfolio.php" class="active"><i class="fas fa-images"></i> Портфолио</a></li>
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
                <h1>Управление портфолио</h1>
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

            <!-- Форма добавления -->
            <div class="content-card" style="margin-bottom: 30px;">
                <h2>Добавить новую работу</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="title">Название работы</label>
                        <input type="text" name="title" id="title" required placeholder="Например: Свадьба Анны и Дмитрия">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Описание</label>
                        <textarea name="description" id="description" rows="3" placeholder="Краткое описание мероприятия"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Категория</label>
                        <select name="category" id="category">
                            <option value="wedding">Свадьба</option>
                            <option value="corporate">Корпоратив</option>
                            <option value="birthday">День рождения</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Изображение</label>
                        <input type="file" name="image" id="image" accept="image/*" required>
                        <small>Поддерживаемые форматы: JPG, PNG, GIF, WebP</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Добавить в портфолио
                        </button>
                    </div>
                </form>
            </div>

            <!-- Список портфолио -->
            <div class="portfolio-section">
                <h2>Текущие работы (<?= count($portfolio) ?>)</h2>
                
                <?php if (empty($portfolio)): ?>
                <div class="empty-state">
                    <i class="fas fa-images"></i>
                    <h3>Портфолио пусто</h3>
                    <p>Добавьте первую работу, используя форму выше</p>
                </div>
                <?php else: ?>
                <div class="portfolio-grid">
                    <?php foreach ($portfolio as $item): ?>
                    <div class="portfolio-item">
                        <img src="../<?= htmlspecialchars($item['image']) ?>" 
                             alt="<?= htmlspecialchars($item['title']) ?>"
                             onerror="this.src='https://via.placeholder.com/300x200/667eea/white?text=Изображение+не+найдено'">
                        
                        <div class="portfolio-info">
                            <h3><?= htmlspecialchars($item['title']) ?></h3>
                            <p><?= htmlspecialchars($item['description']) ?></p>
                            <span class="category-tag"><?= getCategoryName($item['category']) ?></span>
                            <small>Добавлено: <?= date('d.m.Y', strtotime($item['created_at'])) ?></small>
                            
                            <div class="portfolio-actions">
                                <button class="btn btn-sm btn-primary" onclick="editItem(<?= $item['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                    Редактировать
                                </button>
                                <form method="POST" action="" style="display: inline;" 
                                      onsubmit="return confirm('Вы уверены, что хотите удалить эту работу?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                        Удалить
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Модальное окно для редактирования -->
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Редактирование работы</h3>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="form-group">
                        <label for="edit_title">Название</label>
                        <input type="text" id="edit_title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Описание</label>
                        <textarea id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_category">Категория</label>
                        <select id="edit_category" name="category">
                            <option value="wedding">Свадьба</option>
                            <option value="corporate">Корпоратив</option>
                            <option value="birthday">День рождения</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="saveEdit()">Сохранить</button>
            </div>
        </div>
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
        document.getElementById('image').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Создаем превью если его нет
                    let preview = document.getElementById('image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'image-preview';
                        preview.style.cssText = 'max-width: 200px; max-height: 150px; margin-top: 10px; border-radius: 8px;';
                        document.getElementById('image').parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Функции для модального окна
        function editItem(id) {
            // Здесь можно загрузить данные элемента и показать модальное окно
            // Пока просто показываем alert
            alert('Функция редактирования будет добавлена в следующей версии');
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function saveEdit() {
            // Здесь логика сохранения изменений
            closeModal();
        }
    </script>

    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 20px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .category-tag {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #d1d5db;
        }
    </style>
</body>
</html>

<?php
function getCategoryName($category) {
    $names = [
        'wedding' => 'Свадьба',
        'corporate' => 'Корпоратив',
        'birthday' => 'День рождения',
        'other' => 'Другое'
    ];
    return $names[$category] ?? 'Не указано';
}
?>

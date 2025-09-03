<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Обработка загрузки файлов
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    $upload_dir = '../uploads/';
    $uploaded_files = [];
    $errors = [];
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['files']['error'][$key] === 0) {
            $file_name = $_FILES['files']['name'][$key];
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $safe_name = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file_name);
            $file_path = $upload_dir . $safe_name;
            
            // Проверка типа файла
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'mp4', 'avi', 'mov'];
            if (!in_array(strtolower($file_extension), $allowed_types)) {
                $errors[] = "Файл {$file_name} имеет неподдерживаемый формат";
                continue;
            }
            
            // Проверка размера (максимум 50MB)
            if ($_FILES['files']['size'][$key] > 50 * 1024 * 1024) {
                $errors[] = "Файл {$file_name} слишком большой (максимум 50MB)";
                continue;
            }
            
            if (move_uploaded_file($tmp_name, $file_path)) {
                $uploaded_files[] = $safe_name;
            } else {
                $errors[] = "Не удалось загрузить файл {$file_name}";
            }
        }
    }
    
    if (!empty($uploaded_files)) {
        $success_message = "Загружено файлов: " . count($uploaded_files);
    }
    
    if (!empty($errors)) {
        $error_message = implode('; ', $errors);
    }
}

// Обработка удаления файлов
if (isset($_GET['delete'])) {
    $file_to_delete = $_GET['delete'];
    $file_path = '../uploads/' . $file_to_delete;
    
    if (file_exists($file_path) && strpos($file_to_delete, '..') === false) {
        if (unlink($file_path)) {
            $success_message = "Файл удален";
        } else {
            $error_message = "Ошибка удаления файла";
        }
    }
}

// Получение списка файлов
function getFiles($dir) {
    $files = [];
    if (is_dir($dir)) {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item != '.' && $item != '..' && is_file($dir . '/' . $item)) {
                $file_path = $dir . '/' . $item;
                $files[] = [
                    'name' => $item,
                    'path' => $file_path,
                    'size' => filesize($file_path),
                    'modified' => filemtime($file_path),
                    'type' => pathinfo($item, PATHINFO_EXTENSION)
                ];
            }
        }
    }
    
    // Сортировка по дате изменения (новые сначала)
    usort($files, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
    
    return $files;
}

$files = getFiles('../uploads');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление файлами - Админ панель</title>
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
                <li><a href="files.php" class="active"><i class="fas fa-folder"></i> Файлы</a></li>
                <li><a href="upload_image.php"><i class="fas fa-camera"></i> Изображения</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Настройки</a></li>
                <li><a href="../" target="_blank"><i class="fas fa-eye"></i> Просмотр сайта</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Выход</a></li>
            </ul>
        </nav>

        <!-- Основной контент -->
        <main class="main-content">
            <header class="content-header">
                <h1>Управление файлами</h1>
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

            <!-- Область загрузки -->
            <div class="upload-section">
                <div class="upload-area" id="uploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h3>Перетащите файлы сюда или нажмите для выбора</h3>
                    <p>Поддерживаемые форматы: JPG, PNG, GIF, WebP, PDF, DOC, DOCX, MP4, AVI, MOV</p>
                    <p>Максимальный размер: 50MB</p>
                    <input type="file" id="fileInput" name="files[]" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.mp4,.avi,.mov" style="display: none;">
                </div>
                
                <form id="uploadForm" method="POST" enctype="multipart/form-data" style="display: none;">
                    <input type="file" name="files[]" multiple>
                    <button type="submit" class="btn btn-primary">Загрузить</button>
                </form>
            </div>

            <!-- Статистика -->
            <div class="stats-grid" style="margin: 30px 0;">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= count($files) ?></h3>
                        <p>Всего файлов</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hdd"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= formatFileSize(array_sum(array_column($files, 'size'))) ?></h3>
                        <p>Общий размер</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= count(array_filter($files, function($f) { return in_array($f['type'], ['jpg', 'jpeg', 'png', 'gif', 'webp']); })) ?></h3>
                        <p>Изображений</p>
                    </div>
                </div>
            </div>

            <!-- Список файлов -->
            <div class="files-section">
                <div class="section-header">
                    <h2>Загруженные файлы</h2>
                    <div class="view-controls">
                        <button class="btn btn-sm" onclick="toggleView('grid')" id="gridViewBtn">
                            <i class="fas fa-th"></i> Сетка
                        </button>
                        <button class="btn btn-sm btn-outline" onclick="toggleView('list')" id="listViewBtn">
                            <i class="fas fa-list"></i> Список
                        </button>
                    </div>
                </div>

                <?php if (empty($files)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>Файлов пока нет</h3>
                    <p>Загрузите первые файлы, используя область выше</p>
                </div>
                <?php else: ?>
                <div class="files-grid" id="filesContainer">
                    <?php foreach ($files as $file): ?>
                    <div class="file-item">
                        <?php if (isImage($file['type'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($file['name']) ?>" 
                             alt="<?= htmlspecialchars($file['name']) ?>"
                             onclick="viewImage('../uploads/<?= htmlspecialchars($file['name']) ?>')">
                        <?php else: ?>
                        <div class="file-icon">
                            <i class="fas fa-<?= getFileIcon($file['type']) ?>"></i>
                        </div>
                        <?php endif; ?>
                        
                        <div class="file-info">
                            <div class="file-name" title="<?= htmlspecialchars($file['name']) ?>">
                                <?= htmlspecialchars(mb_strlen($file['name']) > 20 ? mb_substr($file['name'], 0, 17) . '...' : $file['name']) ?>
                            </div>
                            <div class="file-meta">
                                <span><?= formatFileSize($file['size']) ?></span>
                                <span><?= date('d.m.Y H:i', $file['modified']) ?></span>
                            </div>
                        </div>
                        
                        <div class="file-actions">
                            <button class="btn btn-sm" onclick="copyFileUrl('../uploads/<?= htmlspecialchars($file['name']) ?>')" title="Копировать ссылку">
                                <i class="fas fa-copy"></i>
                            </button>
                            <a href="../uploads/<?= htmlspecialchars($file['name']) ?>" download class="btn btn-sm" title="Скачать">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="?delete=<?= urlencode($file['name']) ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Удалить файл?')" title="Удалить">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Модальное окно для просмотра изображений -->
    <div id="imageModal" class="modal" style="display: none;" onclick="closeImageModal()">
        <div class="modal-content image-modal-content">
            <img id="modalImage" src="" alt="">
            <button class="modal-close" onclick="closeImageModal()">&times;</button>
        </div>
    </div>

    <script src="js/admin.js"></script>
    <script>
        // Обработка загрузки файлов
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const uploadForm = document.getElementById('uploadForm');

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                uploadFiles(files);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                uploadFiles(e.target.files);
            }
        });

        function uploadFiles(files) {
            const formData = new FormData();
            
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }

            // Показываем прогресс
            uploadArea.innerHTML = '<i class="fas fa-spinner fa-spin"></i><h3>Загружаем файлы...</h3>';

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(() => {
                location.reload();
            })
            .catch(error => {
                console.error('Ошибка загрузки:', error);
                uploadArea.innerHTML = '<i class="fas fa-exclamation-triangle"></i><h3>Ошибка загрузки</h3>';
                setTimeout(() => location.reload(), 2000);
            });
        }

        // Переключение видов
        function toggleView(view) {
            const container = document.getElementById('filesContainer');
            const gridBtn = document.getElementById('gridViewBtn');
            const listBtn = document.getElementById('listViewBtn');

            if (view === 'grid') {
                container.className = 'files-grid';
                gridBtn.className = 'btn btn-sm';
                listBtn.className = 'btn btn-sm btn-outline';
            } else {
                container.className = 'files-list';
                gridBtn.className = 'btn btn-sm btn-outline';
                listBtn.className = 'btn btn-sm';
            }
        }

        // Просмотр изображений
        function viewImage(src) {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('modalImage');
            img.src = src;
            modal.style.display = 'flex';
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // Копирование ссылки
        function copyFileUrl(url) {
            const fullUrl = window.location.origin + '/' + url.replace('../', '');
            navigator.clipboard.writeText(fullUrl).then(() => {
                showNotification('Ссылка скопирована в буфер обмена', 'success');
            });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type} show`;
            notification.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 3000);
        }

        // Автоматическое скрытие уведомлений
        setTimeout(function() {
            const notification = document.getElementById('notification');
            if (notification) {
                notification.classList.remove('show');
            }
        }, 3000);
    </script>

    <style>
        .upload-area.dragover {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .files-list {
            display: block;
        }

        .files-list .file-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .files-list .file-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 15px;
        }

        .files-list .file-icon {
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .view-controls {
            display: flex;
            gap: 10px;
        }

        .file-meta {
            display: flex;
            gap: 10px;
            font-size: 0.8rem;
            color: #6b7280;
        }

        .image-modal-content {
            max-width: 90vw;
            max-height: 90vh;
            padding: 0;
            position: relative;
        }

        .image-modal-content img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 12px;
        }

        .modal {
            background: rgba(0, 0, 0, 0.8);
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
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}

function isImage($extension) {
    return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
}

function getFileIcon($extension) {
    $icons = [
        'pdf' => 'file-pdf',
        'doc' => 'file-word',
        'docx' => 'file-word',
        'mp4' => 'file-video',
        'avi' => 'file-video',
        'mov' => 'file-video'
    ];
    return $icons[strtolower($extension)] ?? 'file';
}
?>

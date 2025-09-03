<?php
session_start();
require_once '../includes/functions.php';

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$section = $_GET['section'] ?? '';
if (!$section) {
    header('Location: index.php');
    exit;
}

// Получаем контент секции
$content = getContent();
$sectionData = $content[$section] ?? null;

if (!$sectionData) {
    header('Location: index.php');
    exit;
}

$success = '';
$error = '';

// Обработка формы
if ($_POST) {
    $data = [
        'title' => $_POST['title'] ?? '',
        'subtitle' => $_POST['subtitle'] ?? '',
        'description' => $_POST['description'] ?? '',
        'content' => $_POST['content'] ?? '',
        'image' => $_POST['image'] ?? '',
        'button_text' => $_POST['button_text'] ?? '',
        'button_link' => $_POST['button_link'] ?? ''
    ];
    
    if (updateContent($section, $data)) {
        $success = 'Контент успешно обновлен!';
        // Обновляем данные
        $sectionData = array_merge($sectionData, $data);
    } else {
        $error = 'Ошибка при обновлении контента';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование - <?php echo htmlspecialchars($sectionData['title']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #1a1a1a;
            color: #ffffff;
            line-height: 1.6;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .admin-nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-logo h1 {
            color: #ff6b35;
            font-size: 1.5rem;
        }
        
        .admin-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
        }
        
        .btn-secondary {
            background: transparent;
            color: #b0b0b0;
            border: 1px solid rgba(255, 107, 53, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .edit-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 20px;
        }
        
        .edit-card {
            background: #2d2d2d;
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 107, 53, 0.1);
        }
        
        .edit-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .edit-header h1 {
            color: #ff6b35;
            margin-bottom: 0.5rem;
        }
        
        .edit-header p {
            color: #b0b0b0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ffffff;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 107, 53, 0.3);
            border-radius: 10px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ff6b35;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert-success {
            background: rgba(76, 175, 80, 0.1);
            border: 1px solid rgba(76, 175, 80, 0.3);
            color: #4CAF50;
        }
        
        .alert-error {
            background: rgba(244, 67, 54, 0.1);
            border: 1px solid rgba(244, 67, 54, 0.3);
            color: #f44336;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .preview-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .preview-title {
            color: #ff6b35;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .preview-content {
            background: rgba(26, 26, 26, 0.5);
            border-radius: 10px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 107, 53, 0.1);
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <nav class="admin-nav">
            <div class="admin-logo">
                <h1><i class="fas fa-edit"></i> Редактирование контента</h1>
            </div>
            <div class="admin-actions">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>
                <a href="../index.php" class="btn btn-primary" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Просмотр сайта
                </a>
            </div>
        </nav>
    </header>
    
    <main class="edit-container">
        <div class="edit-card">
            <div class="edit-header">
                <h1><?php echo htmlspecialchars($sectionData['title']); ?></h1>
                <p>Редактирование секции: <?php echo ucfirst($section); ?></p>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="title">Заголовок</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($sectionData['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="subtitle">Подзаголовок</label>
                    <input type="text" id="subtitle" name="subtitle" value="<?php echo htmlspecialchars($sectionData['subtitle']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Описание</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($sectionData['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="content">Дополнительный контент</label>
                    <textarea id="content" name="content"><?php echo htmlspecialchars($sectionData['content']); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="image">URL изображения</label>
                        <input type="url" id="image" name="image" value="<?php echo htmlspecialchars($sectionData['image']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="button_text">Текст кнопки</label>
                        <input type="text" id="button_text" name="button_text" value="<?php echo htmlspecialchars($sectionData['button_text']); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="button_link">Ссылка кнопки</label>
                    <input type="url" id="button_link" name="button_link" value="<?php echo htmlspecialchars($sectionData['button_link']); ?>">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Сохранить изменения
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Отмена
                    </a>
                </div>
            </form>
            
            <div class="preview-section">
                <h3 class="preview-title">Предварительный просмотр</h3>
                <div class="preview-content">
                    <h2><?php echo htmlspecialchars($sectionData['title']); ?></h2>
                    <?php if ($sectionData['subtitle']): ?>
                        <p style="color: #b0b0b0; font-size: 1.2rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($sectionData['subtitle']); ?></p>
                    <?php endif; ?>
                    <?php if ($sectionData['description']): ?>
                        <p style="color: #b0b0b0; margin-bottom: 1rem;"><?php echo htmlspecialchars($sectionData['description']); ?></p>
                    <?php endif; ?>
                    <?php if ($sectionData['content']): ?>
                        <div style="color: #b0b0b0;"><?php echo nl2br(htmlspecialchars($sectionData['content'])); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
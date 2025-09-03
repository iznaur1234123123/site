<?php
session_start();
require_once '../includes/functions.php';

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Обработка выхода
if (isset($_GET['logout'])) {
    logoutAdmin();
}

// Получаем контент
$content = getContent();
$messages = getContactMessages();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Булат Докуев</title>
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
        
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 20px;
        }
        
        .admin-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .admin-card {
            background: #2d2d2d;
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 107, 53, 0.1);
            transition: all 0.3s ease;
        }
        
        .admin-card:hover {
            border-color: rgba(255, 107, 53, 0.3);
            transform: translateY(-5px);
        }
        
        .admin-card h2 {
            color: #ff6b35;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 107, 53, 0.1);
            border: 1px solid rgba(255, 107, 53, 0.2);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #ff6b35;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #b0b0b0;
            font-size: 0.9rem;
        }
        
        .messages-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .message-item {
            background: rgba(26, 26, 26, 0.5);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 3px solid #ff6b35;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .message-name {
            font-weight: 600;
            color: #ffffff;
        }
        
        .message-date {
            color: #808080;
            font-size: 0.8rem;
        }
        
        .message-email {
            color: #ff6b35;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .message-text {
            color: #b0b0b0;
            font-size: 0.9rem;
        }
        
        .content-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .section-card {
            background: #2d2d2d;
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 107, 53, 0.1);
            transition: all 0.3s ease;
        }
        
        .section-card:hover {
            border-color: rgba(255, 107, 53, 0.3);
        }
        
        .section-title {
            color: #ff6b35;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .section-preview {
            color: #b0b0b0;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.4;
        }
        
        .section-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-small {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .content-sections {
                grid-template-columns: 1fr;
            }
            
            .admin-nav {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <nav class="admin-nav">
            <div class="admin-logo">
                <h1><i class="fas fa-crown"></i> Админ-панель</h1>
            </div>
            <div class="admin-actions">
                <a href="../index.php" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Просмотр сайта
                </a>
                <a href="?logout=1" class="btn btn-primary">
                    <i class="fas fa-sign-out-alt"></i> Выйти
                </a>
            </div>
        </nav>
    </header>
    
    <main class="admin-container">
        <div class="admin-grid">
            <div class="admin-card">
                <h2><i class="fas fa-chart-bar"></i> Статистика</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($content); ?></div>
                        <div class="stat-label">Секций контента</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($messages); ?></div>
                        <div class="stat-label">Сообщений</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Готовность</div>
                    </div>
                </div>
            </div>
            
            <div class="admin-card">
                <h2><i class="fas fa-envelope"></i> Последние сообщения</h2>
                <div class="messages-list">
                    <?php if (empty($messages)): ?>
                        <p style="color: #808080; text-align: center; padding: 2rem;">Нет сообщений</p>
                    <?php else: ?>
                        <?php foreach (array_slice($messages, 0, 5) as $message): ?>
                            <div class="message-item">
                                <div class="message-header">
                                    <div class="message-name"><?php echo htmlspecialchars($message['name']); ?></div>
                                    <div class="message-date"><?php echo date('d.m.Y H:i', strtotime($message['created_at'])); ?></div>
                                </div>
                                <div class="message-email"><?php echo htmlspecialchars($message['email']); ?></div>
                                <div class="message-text"><?php echo htmlspecialchars(substr($message['message'], 0, 100)) . (strlen($message['message']) > 100 ? '...' : ''); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="admin-card">
            <h2><i class="fas fa-edit"></i> Управление контентом</h2>
            <div class="content-sections">
                <?php foreach ($content as $section => $data): ?>
                    <div class="section-card">
                        <h3 class="section-title">
                            <i class="fas fa-<?php echo getSectionIcon($section); ?>"></i>
                            <?php echo htmlspecialchars($data['title']); ?>
                        </h3>
                        <div class="section-preview">
                            <?php echo htmlspecialchars(substr($data['description'] ?: $data['subtitle'], 0, 100)) . '...'; ?>
                        </div>
                        <div class="section-actions">
                            <a href="edit.php?section=<?php echo $section; ?>" class="btn btn-primary btn-small">
                                <i class="fas fa-edit"></i> Редактировать
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>
</html>

<?php
function getSectionIcon($section) {
    $icons = [
        'hero' => 'home',
        'about' => 'user',
        'services' => 'cogs',
        'portfolio' => 'images',
        'contact' => 'phone'
    ];
    return $icons[$section] ?? 'file';
}
?>
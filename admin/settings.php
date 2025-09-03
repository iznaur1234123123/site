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

// Обработка смены пароля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    // Валидация
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'Заполните все поля';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'Новые пароли не совпадают';
    } elseif (strlen($new_password) < 6) {
        $error_message = 'Пароль должен содержать минимум 6 символов';
    } else {
        // Проверяем текущий пароль
        $username = $_SESSION['admin_username'] ?? 'admin';
        
        if ($db->validateAdmin($username, $current_password)) {
            // Обновляем пароль
            if ($db->updateAdminPassword($username, $new_password)) {
                $success_message = 'Пароль успешно изменен!';
            } else {
                $error_message = 'Ошибка при изменении пароля';
            }
        } else {
            $error_message = 'Неверный текущий пароль';
        }
    }
}

// Обработка обновления профиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $new_username = trim($_POST['username'] ?? '');
    $current_username = $_SESSION['admin_username'] ?? 'admin';
    
    if (empty($new_username)) {
        $error_message = 'Логин не может быть пустым';
    } elseif ($new_username !== $current_username) {
        if ($db->updateAdminUsername($current_username, $new_username)) {
            $_SESSION['admin_username'] = $new_username;
            $success_message = 'Логин успешно изменен!';
        } else {
            $error_message = 'Ошибка при изменении логина';
        }
    }
}

$current_username = $_SESSION['admin_username'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки - Админ панель</title>
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
                <li><a href="upload_image.php"><i class="fas fa-camera"></i> Изображения</a></li>
                <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Настройки</a></li>
                <li><a href="../" target="_blank"><i class="fas fa-eye"></i> Просмотр сайта</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Выход</a></li>
            </ul>
        </nav>

        <!-- Основной контент -->
        <main class="main-content">
            <header class="content-header">
                <h1>Настройки профиля</h1>
                <div class="user-info">
                    <span>Администратор (<?= htmlspecialchars($current_username) ?>)</span>
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

            <div class="settings-grid">
                <!-- Смена пароля -->
                <div class="content-card">
                    <h2>
                        <i class="fas fa-lock"></i>
                        Изменение пароля
                    </h2>
                    <p class="card-description">
                        Для безопасности регулярно меняйте пароль. Используйте сложный пароль с буквами, цифрами и символами.
                    </p>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label for="current_password">
                                <i class="fas fa-key"></i>
                                Текущий пароль
                            </label>
                            <input type="password" name="current_password" id="current_password" required 
                                   placeholder="Введите текущий пароль">
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">
                                <i class="fas fa-lock"></i>
                                Новый пароль
                            </label>
                            <input type="password" name="new_password" id="new_password" required 
                                   placeholder="Введите новый пароль (минимум 6 символов)"
                                   minlength="6">
                            <small class="form-hint">Минимум 6 символов, рекомендуется использовать буквы, цифры и символы</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">
                                <i class="fas fa-lock"></i>
                                Подтверждение пароля
                            </label>
                            <input type="password" name="confirm_password" id="confirm_password" required 
                                   placeholder="Подтвердите новый пароль">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Изменить пароль
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Смена логина -->
                <div class="content-card">
                    <h2>
                        <i class="fas fa-user"></i>
                        Настройки профиля
                    </h2>
                    <p class="card-description">
                        Измените логин для входа в админ панель.
                    </p>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-group">
                            <label for="username">
                                <i class="fas fa-user"></i>
                                Логин
                            </label>
                            <input type="text" name="username" id="username" required 
                                   value="<?= htmlspecialchars($current_username) ?>"
                                   placeholder="Введите новый логин">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Информация о безопасности -->
                <div class="content-card">
                    <h2>
                        <i class="fas fa-shield-alt"></i>
                        Рекомендации по безопасности
                    </h2>
                    
                    <div class="security-tips">
                        <div class="tip-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Используйте сложный пароль длиной не менее 8 символов</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Включите буквы в разных регистрах, цифры и специальные символы</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Меняйте пароль каждые 3-6 месяцев</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Не используйте один пароль для разных сервисов</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Всегда выходите из админки после работы</span>
                        </div>
                    </div>
                </div>

                <!-- Информация о системе -->
                <div class="content-card">
                    <h2>
                        <i class="fas fa-info-circle"></i>
                        Информация о системе
                    </h2>
                    
                    <div class="system-info">
                        <div class="info-row">
                            <strong>Версия PHP:</strong>
                            <span><?= phpversion() ?></span>
                        </div>
                        <div class="info-row">
                            <strong>База данных:</strong>
                            <span>SQLite</span>
                        </div>
                        <div class="info-row">
                            <strong>Последний вход:</strong>
                            <span><?= date('d.m.Y H:i') ?></span>
                        </div>
                        <div class="info-row">
                            <strong>Размер базы данных:</strong>
                            <span><?= file_exists('../database/site.db') ? round(filesize('../database/site.db') / 1024, 2) . ' KB' : 'N/A' ?></span>
                        </div>
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

        // Проверка совпадения паролей
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && newPassword !== confirmPassword) {
                this.style.borderColor = '#ef4444';
                this.setCustomValidity('Пароли не совпадают');
            } else {
                this.style.borderColor = '';
                this.setCustomValidity('');
            }
        });

        // Индикатор силы пароля
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            
            // Удаляем предыдущий индикатор
            const existingIndicator = document.querySelector('.password-strength');
            if (existingIndicator) {
                existingIndicator.remove();
            }
            
            if (password.length > 0) {
                const indicator = document.createElement('div');
                indicator.className = 'password-strength';
                indicator.innerHTML = `
                    <div class="strength-bar strength-${strength.level}">
                        <div class="strength-fill"></div>
                    </div>
                    <span class="strength-text">Сила пароля: ${strength.text}</span>
                `;
                
                this.parentNode.appendChild(indicator);
            }
        });

        function calculatePasswordStrength(password) {
            let score = 0;
            
            if (password.length >= 8) score++;
            if (password.length >= 12) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            
            if (score < 3) return { level: 'weak', text: 'Слабый' };
            if (score < 5) return { level: 'medium', text: 'Средний' };
            return { level: 'strong', text: 'Сильный' };
        }
    </script>

    <style>
        .settings-grid {
            display: grid;
            gap: 30px;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        }

        .card-description {
            color: #6b7280;
            margin-bottom: 20px;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .form-hint {
            color: #6b7280;
            font-size: 0.8rem;
            margin-top: 5px;
            display: block;
        }

        .security-tips {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .tip-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .text-success {
            color: #10b981;
        }

        .system-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .password-strength {
            margin-top: 10px;
        }

        .strength-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
        }

        .strength-weak .strength-fill {
            width: 33%;
            background: #ef4444;
        }

        .strength-medium .strength-fill {
            width: 66%;
            background: #f59e0b;
        }

        .strength-strong .strength-fill {
            width: 100%;
            background: #10b981;
        }

        .strength-text {
            font-size: 0.8rem;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>

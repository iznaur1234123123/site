<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
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
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Панель управления</h2>
                <p>Булат Докуев</p>
            </div>
            <nav class="sidebar-nav">
                <a href="#dashboard" class="nav-item active" data-section="dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Главная</span>
                </a>
                <a href="#content" class="nav-item" data-section="content">
                    <i class="fas fa-edit"></i>
                    <span>Контент</span>
                </a>
                <a href="#portfolio" class="nav-item" data-section="portfolio">
                    <i class="fas fa-images"></i>
                    <span>Портфолио</span>
                </a>
                <a href="#reviews" class="nav-item" data-section="reviews">
                    <i class="fas fa-star"></i>
                    <span>Отзывы</span>
                </a>
                <a href="#settings" class="nav-item" data-section="settings">
                    <i class="fas fa-cog"></i>
                    <span>Настройки</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="../" class="view-site" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Посмотреть сайт</span>
                </a>
                <button class="logout-btn" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Выйти</span>
                </button>
            </div>
        </aside>

        <!-- Основной контент -->
        <main class="main-content">
            <header class="content-header">
                <h1 id="page-title">Главная</h1>
                <div class="header-actions">
                    <span class="welcome-text">Добро пожаловать, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</span>
                </div>
            </header>

            <div class="content-body">
                <!-- Dashboard -->
                <section id="dashboard" class="content-section active">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-images"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="portfolio-count">0</h3>
                                <p>Работ в портфолио</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="reviews-count">0</h3>
                                <p>Отзывов</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="content-count">0</h3>
                                <p>Элементов контента</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-actions">
                        <div class="action-card">
                            <h3>Быстрые действия</h3>
                            <div class="action-buttons">
                                <button class="btn btn-primary" onclick="showSection('content')">
                                    <i class="fas fa-edit"></i>
                                    Редактировать контент
                                </button>
                                <button class="btn btn-secondary" onclick="showSection('portfolio')">
                                    <i class="fas fa-plus"></i>
                                    Добавить работу
                                </button>
                                <button class="btn btn-secondary" onclick="showSection('reviews')">
                                    <i class="fas fa-star"></i>
                                    Управлять отзывами
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Content Management -->
                <section id="content" class="content-section">
                    <div class="section-header">
                        <h2>Управление контентом</h2>
                        <p>Редактируйте любой текст на сайте</p>
                    </div>
                    
                    <div class="content-editor">
                        <div class="editor-tabs">
                            <button class="tab-btn active" data-tab="hero">Главная</button>
                            <button class="tab-btn" data-tab="about">О себе</button>
                            <button class="tab-btn" data-tab="services">Услуги</button>
                            <button class="tab-btn" data-tab="contact">Контакты</button>
                        </div>
                        
                        <div class="tab-content">
                            <div id="hero-tab" class="tab-panel active">
                                <div class="form-group">
                                    <label>Заголовок главной страницы</label>
                                    <input type="text" id="hero_title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Подзаголовок</label>
                                    <input type="text" id="hero_subtitle" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Описание</label>
                                    <textarea id="hero_description" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                            
                            <div id="about-tab" class="tab-panel">
                                <div class="form-group">
                                    <label>Заголовок секции "О себе"</label>
                                    <input type="text" id="about_title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Подзаголовок</label>
                                    <input type="text" id="about_subtitle" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Заголовок карточки 1</label>
                                    <input type="text" id="about_card1_title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Текст карточки 1</label>
                                    <textarea id="about_card1_text" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Заголовок карточки 2</label>
                                    <input type="text" id="about_card2_title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Текст карточки 2</label>
                                    <textarea id="about_card2_text" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Заголовок карточки 3</label>
                                    <input type="text" id="about_card3_title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Текст карточки 3</label>
                                    <textarea id="about_card3_text" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <div id="services-tab" class="tab-panel">
                                <div class="form-group">
                                    <label>Заголовок секции "Услуги"</label>
                                    <input type="text" id="services_title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Подзаголовок</label>
                                    <input type="text" id="services_subtitle" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Название услуги 1</label>
                                    <input type="text" id="service1_title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Описание услуги 1</label>
                                    <textarea id="service1_description" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Название услуги 2</label>
                                    <input type="text" id="service2_title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Описание услуги 2</label>
                                    <textarea id="service2_description" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <div id="contact-tab" class="tab-panel">
                                <div class="form-group">
                                    <label>Телефон</label>
                                    <input type="text" id="contact_phone" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" id="contact_email" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Город</label>
                                    <input type="text" id="contact_city" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Instagram</label>
                                    <input type="url" id="social_instagram" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>VK</label>
                                    <input type="url" id="social_vk" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Telegram</label>
                                    <input type="url" id="social_telegram" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>WhatsApp</label>
                                    <input type="url" id="social_whatsapp" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button class="btn btn-primary" onclick="saveContent()">
                                <i class="fas fa-save"></i>
                                Сохранить изменения
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Portfolio Management -->
                <section id="portfolio" class="content-section">
                    <div class="section-header">
                        <h2>Управление портфолио</h2>
                        <button class="btn btn-primary" onclick="showAddPortfolioModal()">
                            <i class="fas fa-plus"></i>
                            Добавить работу
                        </button>
                    </div>
                    
                    <div class="portfolio-grid" id="portfolio-grid">
                        <!-- Портфолио будет загружено динамически -->
                    </div>
                </section>

                <!-- Reviews Management -->
                <section id="reviews" class="content-section">
                    <div class="section-header">
                        <h2>Управление отзывами</h2>
                        <button class="btn btn-primary" onclick="showAddReviewModal()">
                            <i class="fas fa-plus"></i>
                            Добавить отзыв
                        </button>
                    </div>
                    
                    <div class="reviews-grid" id="reviews-grid">
                        <!-- Отзывы будут загружены динамически -->
                    </div>
                </section>

                <!-- Settings -->
                <section id="settings" class="content-section">
                    <div class="section-header">
                        <h2>Настройки</h2>
                        <p>Управление аккаунтом и безопасностью</p>
                    </div>
                    
                    <div class="settings-content">
                        <div class="settings-card">
                            <h3>Смена пароля</h3>
                            <div class="form-group">
                                <label>Текущий пароль</label>
                                <input type="password" id="current-password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Новый пароль</label>
                                <input type="password" id="new-password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Подтвердите пароль</label>
                                <input type="password" id="confirm-password" class="form-control">
                            </div>
                            <button class="btn btn-primary" onclick="changePassword()">
                                <i class="fas fa-key"></i>
                                Изменить пароль
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Модальные окна -->
    <div id="modal-overlay" class="modal-overlay">
        <div class="modal" id="portfolio-modal">
            <div class="modal-header">
                <h3>Добавить работу в портфолио</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Название</label>
                    <input type="text" id="portfolio-title" class="form-control">
                </div>
                <div class="form-group">
                    <label>Описание</label>
                    <textarea id="portfolio-description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Изображение (URL)</label>
                    <input type="url" id="portfolio-image" class="form-control">
                </div>
                <div class="form-group">
                    <label>Категория</label>
                    <select id="portfolio-category" class="form-control">
                        <option value="wedding">Свадьба</option>
                        <option value="corporate">Корпоратив</option>
                        <option value="birthday">День рождения</option>
                        <option value="other">Другое</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Отмена</button>
                <button class="btn btn-primary" onclick="savePortfolio()">Сохранить</button>
            </div>
        </div>

        <div class="modal" id="review-modal">
            <div class="modal-header">
                <h3>Добавить отзыв</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Имя клиента</label>
                    <input type="text" id="review-name" class="form-control">
                </div>
                <div class="form-group">
                    <label>Текст отзыва</label>
                    <textarea id="review-text" class="form-control" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label>Тип мероприятия</label>
                    <input type="text" id="review-event" class="form-control">
                </div>
                <div class="form-group">
                    <label>Аватар (URL)</label>
                    <input type="url" id="review-avatar" class="form-control">
                </div>
                <div class="form-group">
                    <label>Рейтинг</label>
                    <select id="review-rating" class="form-control">
                        <option value="5">5 звезд</option>
                        <option value="4">4 звезды</option>
                        <option value="3">3 звезды</option>
                        <option value="2">2 звезды</option>
                        <option value="1">1 звезда</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Отмена</button>
                <button class="btn btn-primary" onclick="saveReview()">Сохранить</button>
            </div>
        </div>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Получаем контент из базы данных
$content = getContent();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $content['hero']['title'] ?? 'Булат Докуев - Профессиональный ведущий'; ?></title>
    <meta name="description" content="<?php echo $content['hero']['subtitle'] ?? 'Профессиональный ведущий мероприятий'; ?>">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>Булат Докуев</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="#home" class="nav-link">Главная</a></li>
                <li><a href="#about" class="nav-link">О ведущем</a></li>
                <li><a href="#services" class="nav-link">Услуги</a></li>
                <li><a href="#portfolio" class="nav-link">Портфолио</a></li>
                <li><a href="#contact" class="nav-link">Контакты</a></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-background">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-content">
            <div class="container">
                <h1 class="hero-title"><?php echo $content['hero']['title'] ?? 'Булат Докуев'; ?></h1>
                <p class="hero-subtitle"><?php echo $content['hero']['subtitle'] ?? 'Профессиональный ведущий мероприятий'; ?></p>
                <p class="hero-description"><?php echo $content['hero']['description'] ?? 'Создаю незабываемые моменты для ваших особенных событий'; ?></p>
                <div class="hero-buttons">
                    <a href="#contact" class="btn btn-primary">Связаться</a>
                    <a href="#portfolio" class="btn btn-secondary">Портфолио</a>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <div class="scroll-arrow"></div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php echo $content['about']['title'] ?? 'О ведущем'; ?></h2>
                <p class="section-subtitle"><?php echo $content['about']['subtitle'] ?? 'Профессионал с многолетним опытом'; ?></p>
            </div>
            <div class="about-content">
                <div class="about-text">
                    <p><?php echo $content['about']['description'] ?? 'Булат Докуев - опытный ведущий с более чем 10-летним стажем работы в сфере организации и проведения мероприятий. Специализируюсь на корпоративных событиях, свадьбах, юбилеях и других торжественных мероприятиях.'; ?></p>
                    <div class="about-stats">
                        <div class="stat">
                            <h3>500+</h3>
                            <p>Проведенных мероприятий</p>
                        </div>
                        <div class="stat">
                            <h3>10+</h3>
                            <p>Лет опыта</p>
                        </div>
                        <div class="stat">
                            <h3>100%</h3>
                            <p>Довольных клиентов</p>
                        </div>
                    </div>
                </div>
                <div class="about-image">
                    <img src="<?php echo $content['about']['image'] ?? 'assets/images/bulat-about.jpg'; ?>" alt="Булат Докуев">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php echo $content['services']['title'] ?? 'Услуги'; ?></h2>
                <p class="section-subtitle"><?php echo $content['services']['subtitle'] ?? 'Полный спектр услуг ведущего'; ?></p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-ring"></i>
                    </div>
                    <h3>Свадьбы</h3>
                    <p>Романтичные и незабываемые свадебные церемонии с индивидуальным подходом к каждой паре.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3>Корпоративы</h3>
                    <p>Профессиональное проведение корпоративных мероприятий, конференций и деловых встреч.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <h3>Юбилеи</h3>
                    <p>Торжественные мероприятия в честь знаменательных дат с особым вниманием к деталям.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Выпускные</h3>
                    <p>Празднование выпускных вечеров с созданием атмосферы радости и гордости за достижения.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" class="portfolio">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php echo $content['portfolio']['title'] ?? 'Портфолио'; ?></h2>
                <p class="section-subtitle"><?php echo $content['portfolio']['subtitle'] ?? 'Примеры проведенных мероприятий'; ?></p>
            </div>
            <div class="portfolio-grid">
                <div class="portfolio-item">
                    <img src="assets/images/portfolio-1.jpg" alt="Свадьба">
                    <div class="portfolio-overlay">
                        <h3>Свадьба Анны и Михаила</h3>
                        <p>Романтичная церемония в загородном клубе</p>
                    </div>
                </div>
                <div class="portfolio-item">
                    <img src="assets/images/portfolio-2.jpg" alt="Корпоратив">
                    <div class="portfolio-overlay">
                        <h3>Корпоратив IT-компании</h3>
                        <p>Новогодний корпоратив на 200 человек</p>
                    </div>
                </div>
                <div class="portfolio-item">
                    <img src="assets/images/portfolio-3.jpg" alt="Юбилей">
                    <div class="portfolio-overlay">
                        <h3>50-летие компании</h3>
                        <p>Торжественное мероприятие в честь юбилея</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php echo $content['contact']['title'] ?? 'Контакты'; ?></h2>
                <p class="section-subtitle"><?php echo $content['contact']['subtitle'] ?? 'Свяжитесь со мной для обсуждения вашего мероприятия'; ?></p>
            </div>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Телефон</h3>
                            <p><?php echo $content['contact']['phone'] ?? '+7 (999) 123-45-67'; ?></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p><?php echo $content['contact']['email'] ?? 'info@bulatdokuev.com'; ?></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Адрес</h3>
                            <p><?php echo $content['contact']['address'] ?? 'Москва, Россия'; ?></p>
                        </div>
                    </div>
                </div>
                <form class="contact-form" action="includes/contact.php" method="POST">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Ваше имя" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Ваш email" required>
                    </div>
                    <div class="form-group">
                        <input type="tel" name="phone" placeholder="Ваш телефон">
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Сообщение" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить сообщение</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3>Булат Докуев</h3>
                    <p>Профессиональный ведущий мероприятий</p>
                </div>
                <div class="footer-social">
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-vk"></i></a>
                    <a href="#"><i class="fab fa-telegram"></i></a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Булат Докуев. Все права защищены.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="assets/js/script.js"></script>
</body>
</html>
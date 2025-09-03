<?php
// Конфигурация базы данных
class Database {
    private $host = 'localhost';
    private $db_name = 'bulat_site';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Если база данных не существует, создаем SQLite
            try {
                $this->conn = new PDO("sqlite:database/site.db");
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->initDatabase();
            } catch(PDOException $e) {
                echo "Connection error: " . $e->getMessage();
            }
        }

        return $this->conn;
    }

    private function initDatabase() {
        // Создаем таблицы для SQLite
        $sql = "
        CREATE TABLE IF NOT EXISTS content (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            key_name TEXT UNIQUE NOT NULL,
            content TEXT NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS portfolio (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            image TEXT NOT NULL,
            category TEXT DEFAULT 'general',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            text TEXT NOT NULL,
            event TEXT NOT NULL,
            avatar TEXT DEFAULT 'uploads/default-avatar.jpg',
            rating INTEGER DEFAULT 5,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        ";

        $this->conn->exec($sql);
        $this->insertDefaultData();
    }

    private function insertDefaultData() {
        // Проверяем, есть ли уже данные
        $stmt = $this->conn->query("SELECT COUNT(*) FROM content");
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            // Вставляем данные по умолчанию
            $defaultContent = [
                'logo_text' => 'Булат Докуев',
                'hero_title' => 'Булат Докуев',
                'hero_subtitle' => 'Профессиональный ведущий мероприятий',
                'hero_description' => 'Создаю незабываемую атмосферу на ваших торжествах. Свадьбы, корпоративы, дни рождения - каждое событие становится особенным.',
                'about_title' => 'О ведущем',
                'about_subtitle' => 'Многолетний опыт и безграничная энергия',
                'about_card1_title' => 'Опыт работы',
                'about_card1_text' => 'Более 8 лет успешной работы ведущим на различных мероприятиях. За это время провел свыше 500 торжеств.',
                'about_card2_title' => 'Индивидуальный подход',
                'about_card2_text' => 'Каждое мероприятие уникально. Я тщательно изучаю пожелания клиентов и создаю персональный сценарий.',
                'about_card3_title' => 'Современные технологии',
                'about_card3_text' => 'Использую профессиональное звуковое и световое оборудование для создания незабываемой атмосферы.',
                'stat1_label' => 'Проведенных мероприятий',
                'stat2_label' => 'Лет опыта',
                'stat3_label' => 'Довольных клиентов',
                'services_title' => 'Услуги',
                'services_subtitle' => 'Профессиональное ведение любых мероприятий',
                'service1_title' => 'Свадьбы',
                'service1_description' => 'Создаю романтичную и торжественную атмосферу для самого важного дня в вашей жизни',
                'service1_feature1' => 'Церемония',
                'service1_feature2' => 'Банкет',
                'service1_feature3' => 'Развлечения',
                'service2_title' => 'Корпоративы',
                'service2_description' => 'Объединяю коллектив и создаю позитивную атмосферу на корпоративных мероприятиях',
                'service2_feature1' => 'Презентации',
                'service2_feature2' => 'Тимбилдинг',
                'service2_feature3' => 'Награждения',
                'service3_title' => 'Дни рождения',
                'service3_description' => 'Делаю день рождения незабываемым праздником для именинника и гостей',
                'service3_feature1' => 'Юбилеи',
                'service3_feature2' => 'Детские праздники',
                'service3_feature3' => 'Тематические вечеринки',
                'service4_title' => 'Особые события',
                'service4_description' => 'Провожу выпускные, открытия, фестивали и другие значимые мероприятия',
                'service4_feature1' => 'Выпускные',
                'service4_feature2' => 'Открытия',
                'service4_feature3' => 'Фестивали',
                'portfolio_title' => 'Портфолио',
                'portfolio_subtitle' => 'Яркие моменты с проведенных мероприятий',
                'reviews_title' => 'Отзывы клиентов',
                'reviews_subtitle' => 'Что говорят о моей работе',
                'contact_title' => 'Контакты',
                'contact_subtitle' => 'Как со мной связаться',
                'contact_phone' => '+7 (XXX) XXX-XX-XX',
                'contact_email' => 'bulat@example.com',
                'contact_city' => 'Москва',
                'social_instagram' => '#',
                'social_vk' => '#',
                'social_telegram' => '#',
                'social_whatsapp' => '#',
                'hero_photo' => 'uploads/hero-photo.jpg'
            ];

            $stmt = $this->conn->prepare("INSERT INTO content (key_name, content) VALUES (?, ?)");
            foreach ($defaultContent as $key => $value) {
                $stmt->execute([$key, $value]);
            }

            // Добавляем отзывы по умолчанию
            $defaultReviews = [
                ['Анна Петрова', 'Булат сделал нашу свадьбу незабываемой! Профессиональный подход, отличная организация и потрясающая атмосфера. Рекомендую всем!', 'Свадьба', 'uploads/avatar1.jpg'],
                ['Михаил Иванов', 'Отличный ведущий! Наш корпоратив прошел на высшем уровне. Все сотрудники остались довольны. Спасибо за профессионализм!', 'Корпоратив', 'uploads/avatar2.jpg'],
                ['Елена Смирнова', 'Булат провел мой день рождения просто потрясающе! Гости до сих пор вспоминают этот вечер. Очень рекомендую!', 'День рождения', 'uploads/avatar3.jpg']
            ];

            $stmt = $this->conn->prepare("INSERT INTO reviews (name, text, event, avatar) VALUES (?, ?, ?, ?)");
            foreach ($defaultReviews as $review) {
                $stmt->execute($review);
            }

            // Создаем администратора по умолчанию
            $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
            $stmt->execute(['admin', $adminPassword]);
        }
    }
}
?>
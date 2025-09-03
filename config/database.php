<?php
// Конфигурация базы данных
class Database {
    private $db_file;
    private $connection;
    
    public function __construct() {
        $this->db_file = __DIR__ . '/../database/site.db';
        $this->initDatabase();
    }
    
    private function initDatabase() {
        try {
            // Создаем папку для базы данных если её нет
            $db_dir = dirname($this->db_file);
            if (!is_dir($db_dir)) {
                mkdir($db_dir, 0755, true);
            }
            
            $this->connection = new PDO('sqlite:' . $this->db_file);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Создаем таблицы
            $this->createTables();
            
            // Заполняем начальными данными
            $this->insertInitialData();
            
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }
    
    private function createTables() {
        // Таблица для контента сайта
        $sql_content = "CREATE TABLE IF NOT EXISTS content (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            key_name TEXT UNIQUE NOT NULL,
            value TEXT NOT NULL,
            type TEXT DEFAULT 'text',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        // Таблица для портфолио
        $sql_portfolio = "CREATE TABLE IF NOT EXISTS portfolio (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            image TEXT NOT NULL,
            category TEXT DEFAULT 'general',
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        // Таблица для заявок
        $sql_contacts = "CREATE TABLE IF NOT EXISTS contact_requests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            phone TEXT NOT NULL,
            email TEXT,
            event_type TEXT,
            message TEXT,
            status TEXT DEFAULT 'new',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        // Таблица для пользователей админки
        $sql_users = "CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'admin',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->connection->exec($sql_content);
        $this->connection->exec($sql_portfolio);
        $this->connection->exec($sql_contacts);
        $this->connection->exec($sql_users);
    }
    
    private function insertInitialData() {
        // Проверяем, есть ли уже данные
        $stmt = $this->connection->query("SELECT COUNT(*) FROM content");
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            // Вставляем начальный контент
            $content_data = [
                ['hero_title', 'Булат Докуев'],
                ['hero_subtitle', 'Профессиональный ведущий мероприятий'],
                ['hero_description', 'Создаю незабываемую атмосферу на ваших торжествах. Свадьбы, корпоративы, дни рождения - каждое событие становится особенным.'],
                ['hero_photo', 'uploads/hero-photo.jpg'],
                
                ['about_title', 'О ведущем'],
                ['about_subtitle', 'Многолетний опыт и безграничная энергия'],
                ['about_card1_title', 'Опыт работы'],
                ['about_card1_text', 'Более 8 лет успешной работы ведущим на различных мероприятиях. За это время провел свыше 500 торжеств.'],
                ['about_card2_title', 'Индивидуальный подход'],
                ['about_card2_text', 'Каждое мероприятие уникально. Я тщательно изучаю пожелания клиентов и создаю персональный сценарий.'],
                ['about_card3_title', 'Современные технологии'],
                ['about_card3_text', 'Использую профессиональное звуковое и световое оборудование для создания незабываемой атмосферы.'],
                
                ['services_title', 'Услуги'],
                ['services_subtitle', 'Профессиональное ведение любых мероприятий'],
                ['service1_title', 'Свадьбы'],
                ['service1_description', 'Создаю романтичную и торжественную атмосферу для самого важного дня в вашей жизни'],
                ['service2_title', 'Корпоративы'],
                ['service2_description', 'Объединяю коллектив и создаю позитивную атмосферу на корпоративных мероприятиях'],
                ['service3_title', 'Дни рождения'],
                ['service3_description', 'Делаю день рождения незабываемым праздником для именинника и гостей'],
                ['service4_title', 'Особые события'],
                ['service4_description', 'Провожу выпускные, открытия, фестивали и другие значимые мероприятия'],
                
                ['portfolio_title', 'Портфолио'],
                ['portfolio_subtitle', 'Яркие моменты с проведенных мероприятий'],
                
                ['contact_title', 'Контакты'],
                ['contact_subtitle', 'Свяжитесь со мной для обсуждения вашего мероприятия'],
                ['contact_phone', '+7 (XXX) XXX-XX-XX'],
                ['contact_email', 'bulat@example.com'],
                ['contact_city', 'Москва'],
                ['social_instagram', 'https://instagram.com/bulat_dokuev'],
                ['social_vk', 'https://vk.com/bulat_dokuev'],
                ['social_telegram', 'https://t.me/bulat_dokuev'],
                ['social_whatsapp', 'https://wa.me/79000000000'],
                
                // Дополнительные изображения
                ['about_photo', ''],
                ['services_bg', ''],
                ['contact_bg', '']
            ];
            
            $stmt = $this->connection->prepare("INSERT INTO content (key_name, value) VALUES (?, ?)");
            foreach ($content_data as $item) {
                $stmt->execute($item);
            }
            
            // Создаем администратора по умолчанию (пароль: admin123)
            $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $this->connection->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
            $stmt->execute(['admin', $admin_password]);
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function getContent($key = null) {
        if ($key) {
            $stmt = $this->connection->prepare("SELECT value FROM content WHERE key_name = ?");
            $stmt->execute([$key]);
            return $stmt->fetchColumn();
        } else {
            $stmt = $this->connection->query("SELECT key_name, value FROM content");
            $result = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[$row['key_name']] = $row['value'];
            }
            return $result;
        }
    }
    
    public function updateContent($key, $value) {
        $stmt = $this->connection->prepare("
            INSERT OR REPLACE INTO content (key_name, value, updated_at) 
            VALUES (?, ?, CURRENT_TIMESTAMP)
        ");
        return $stmt->execute([$key, $value]);
    }
    
    public function addContactRequest($data) {
        $stmt = $this->connection->prepare("
            INSERT INTO contact_requests (name, phone, email, event_type, message) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['name'],
            $data['phone'],
            $data['email'],
            $data['event_type'],
            $data['message']
        ]);
    }
    
    public function getPortfolio($offset = 0, $limit = 10) {
        $stmt = $this->connection->prepare("
            SELECT * FROM portfolio 
            WHERE is_active = 1 
            ORDER BY sort_order DESC, created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addPortfolioItem($title, $description, $image, $category = 'general') {
        $stmt = $this->connection->prepare("
            INSERT INTO portfolio (title, description, image, category) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$title, $description, $image, $category]);
    }
    
    public function deletePortfolioItem($id) {
        $stmt = $this->connection->prepare("DELETE FROM portfolio WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function validateAdmin($username, $password) {
        $stmt = $this->connection->prepare("SELECT password FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $hash = $stmt->fetchColumn();
        
        if ($hash && password_verify($password, $hash)) {
            return true;
        }
        return false;
    }
    
    public function getContactRequests($limit = 50) {
        $stmt = $this->connection->prepare("
            SELECT * FROM contact_requests 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateAdminPassword($username, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $this->connection->prepare("
            UPDATE admin_users 
            SET password = ? 
            WHERE username = ?
        ");
        return $stmt->execute([$hashed_password, $username]);
    }
    
    public function updateAdminUsername($current_username, $new_username) {
        $stmt = $this->connection->prepare("
            UPDATE admin_users 
            SET username = ? 
            WHERE username = ?
        ");
        return $stmt->execute([$new_username, $current_username]);
    }
}
?>

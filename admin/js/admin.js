// JavaScript для админ панели
document.addEventListener('DOMContentLoaded', function() {
    initAdmin();
});

function initAdmin() {
    loadDashboardStats();
    loadContent();
    loadPortfolio();
    loadReviews();
    initTabs();
    initNavigation();
}

// Навигация по разделам
function initNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    const sections = document.querySelectorAll('.content-section');
    
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const sectionId = item.dataset.section;
            showSection(sectionId);
        });
    });
}

function showSection(sectionId) {
    // Убираем активный класс со всех элементов навигации
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Добавляем активный класс к выбранному элементу
    document.querySelector(`[data-section="${sectionId}"]`).classList.add('active');
    
    // Скрываем все секции
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Показываем выбранную секцию
    document.getElementById(sectionId).classList.add('active');
    
    // Обновляем заголовок
    const titles = {
        'dashboard': 'Главная',
        'content': 'Управление контентом',
        'portfolio': 'Управление портфолио',
        'reviews': 'Управление отзывами',
        'settings': 'Настройки'
    };
    
    document.getElementById('page-title').textContent = titles[sectionId];
}

// Инициализация вкладок
function initTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.dataset.tab;
            
            // Убираем активный класс со всех кнопок и панелей
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));
            
            // Добавляем активный класс к выбранным элементам
            btn.classList.add('active');
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
    });
}

// Загрузка статистики для дашборда
async function loadDashboardStats() {
    try {
        const [portfolioResponse, reviewsResponse, contentResponse] = await Promise.all([
            fetch('../api/portfolio.php'),
            fetch('../api/reviews.php'),
            fetch('../api/content.php')
        ]);
        
        const portfolio = await portfolioResponse.json();
        const reviews = await reviewsResponse.json();
        const content = await contentResponse.json();
        
        document.getElementById('portfolio-count').textContent = portfolio.length;
        document.getElementById('reviews-count').textContent = reviews.length;
        document.getElementById('content-count').textContent = Object.keys(content).length;
    } catch (error) {
        console.error('Ошибка загрузки статистики:', error);
    }
}

// Загрузка контента
async function loadContent() {
    try {
        const response = await fetch('../api/content.php');
        const content = await response.json();
        
        // Заполняем поля формы
        Object.keys(content).forEach(key => {
            const element = document.getElementById(key);
            if (element) {
                element.value = content[key];
            }
        });
    } catch (error) {
        console.error('Ошибка загрузки контента:', error);
    }
}

// Сохранение контента
async function saveContent() {
    const formData = {};
    
    // Собираем данные из всех полей формы
    document.querySelectorAll('#content .form-control').forEach(input => {
        if (input.id) {
            formData[input.id] = input.value;
        }
    });
    
    try {
        const response = await fetch('../api/content.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Контент успешно сохранен!', 'success');
        } else {
            showNotification('Ошибка сохранения: ' + result.error, 'error');
        }
    } catch (error) {
        showNotification('Ошибка соединения: ' + error.message, 'error');
    }
}

// Загрузка портфолио
async function loadPortfolio() {
    try {
        const response = await fetch('../api/portfolio.php');
        const portfolio = await response.json();
        
        const portfolioGrid = document.getElementById('portfolio-grid');
        portfolioGrid.innerHTML = '';
        
        portfolio.forEach(item => {
            const portfolioItem = document.createElement('div');
            portfolioItem.className = 'portfolio-item';
            portfolioItem.innerHTML = `
                <img src="${item.image}" alt="${item.title}" loading="lazy">
                <h3>${item.title}</h3>
                <p>${item.description}</p>
                <div class="item-actions">
                    <button class="btn btn-secondary btn-small" onclick="editPortfolio(${item.id})">
                        <i class="fas fa-edit"></i> Редактировать
                    </button>
                    <button class="btn btn-danger btn-small" onclick="deletePortfolio(${item.id})">
                        <i class="fas fa-trash"></i> Удалить
                    </button>
                </div>
            `;
            portfolioGrid.appendChild(portfolioItem);
        });
    } catch (error) {
        console.error('Ошибка загрузки портфолио:', error);
    }
}

// Показать модальное окно добавления портфолио
function showAddPortfolioModal() {
    document.getElementById('portfolio-modal').style.display = 'block';
    document.getElementById('modal-overlay').classList.add('active');
    
    // Очищаем поля
    document.getElementById('portfolio-title').value = '';
    document.getElementById('portfolio-description').value = '';
    document.getElementById('portfolio-image').value = '';
    document.getElementById('portfolio-category').value = 'wedding';
}

// Сохранение портфолио
async function savePortfolio() {
    const data = {
        title: document.getElementById('portfolio-title').value,
        description: document.getElementById('portfolio-description').value,
        image: document.getElementById('portfolio-image').value,
        category: document.getElementById('portfolio-category').value
    };
    
    if (!data.title || !data.description || !data.image) {
        showNotification('Заполните все обязательные поля', 'error');
        return;
    }
    
    try {
        const response = await fetch('../api/portfolio.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Работа добавлена в портфолио!', 'success');
            closeModal();
            loadPortfolio();
            loadDashboardStats();
        } else {
            showNotification('Ошибка сохранения: ' + result.error, 'error');
        }
    } catch (error) {
        showNotification('Ошибка соединения: ' + error.message, 'error');
    }
}

// Удаление портфолио
async function deletePortfolio(id) {
    if (!confirm('Вы уверены, что хотите удалить эту работу?')) {
        return;
    }
    
    try {
        const response = await fetch(`../api/portfolio.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Работа удалена!', 'success');
            loadPortfolio();
            loadDashboardStats();
        } else {
            showNotification('Ошибка удаления: ' + result.error, 'error');
        }
    } catch (error) {
        showNotification('Ошибка соединения: ' + error.message, 'error');
    }
}

// Загрузка отзывов
async function loadReviews() {
    try {
        const response = await fetch('../api/reviews.php');
        const reviews = await response.json();
        
        const reviewsGrid = document.getElementById('reviews-grid');
        reviewsGrid.innerHTML = '';
        
        reviews.forEach(review => {
            const reviewItem = document.createElement('div');
            reviewItem.className = 'review-item';
            reviewItem.innerHTML = `
                <h3>${review.name}</h3>
                <p>"${review.text}"</p>
                <p><strong>Мероприятие:</strong> ${review.event}</p>
                <p><strong>Рейтинг:</strong> ${'★'.repeat(review.rating)}</p>
                <div class="item-actions">
                    <button class="btn btn-secondary btn-small" onclick="editReview(${review.id})">
                        <i class="fas fa-edit"></i> Редактировать
                    </button>
                    <button class="btn btn-danger btn-small" onclick="deleteReview(${review.id})">
                        <i class="fas fa-trash"></i> Удалить
                    </button>
                </div>
            `;
            reviewsGrid.appendChild(reviewItem);
        });
    } catch (error) {
        console.error('Ошибка загрузки отзывов:', error);
    }
}

// Показать модальное окно добавления отзыва
function showAddReviewModal() {
    document.getElementById('review-modal').style.display = 'block';
    document.getElementById('modal-overlay').classList.add('active');
    
    // Очищаем поля
    document.getElementById('review-name').value = '';
    document.getElementById('review-text').value = '';
    document.getElementById('review-event').value = '';
    document.getElementById('review-avatar').value = '';
    document.getElementById('review-rating').value = '5';
}

// Сохранение отзыва
async function saveReview() {
    const data = {
        name: document.getElementById('review-name').value,
        text: document.getElementById('review-text').value,
        event: document.getElementById('review-event').value,
        avatar: document.getElementById('review-avatar').value,
        rating: parseInt(document.getElementById('review-rating').value)
    };
    
    if (!data.name || !data.text || !data.event) {
        showNotification('Заполните все обязательные поля', 'error');
        return;
    }
    
    try {
        const response = await fetch('../api/reviews.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Отзыв добавлен!', 'success');
            closeModal();
            loadReviews();
            loadDashboardStats();
        } else {
            showNotification('Ошибка сохранения: ' + result.error, 'error');
        }
    } catch (error) {
        showNotification('Ошибка соединения: ' + error.message, 'error');
    }
}

// Удаление отзыва
async function deleteReview(id) {
    if (!confirm('Вы уверены, что хотите удалить этот отзыв?')) {
        return;
    }
    
    try {
        const response = await fetch(`../api/reviews.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Отзыв удален!', 'success');
            loadReviews();
            loadDashboardStats();
        } else {
            showNotification('Ошибка удаления: ' + result.error, 'error');
        }
    } catch (error) {
        showNotification('Ошибка соединения: ' + error.message, 'error');
    }
}

// Смена пароля
async function changePassword() {
    const currentPassword = document.getElementById('current-password').value;
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    
    if (!currentPassword || !newPassword || !confirmPassword) {
        showNotification('Заполните все поля', 'error');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        showNotification('Пароли не совпадают', 'error');
        return;
    }
    
    if (newPassword.length < 6) {
        showNotification('Пароль должен содержать минимум 6 символов', 'error');
        return;
    }
    
    // Здесь должна быть логика смены пароля
    showNotification('Функция смены пароля будет добавлена в следующей версии', 'info');
}

// Закрытие модального окна
function closeModal() {
    document.getElementById('modal-overlay').classList.remove('active');
    document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
    });
}

// Выход из системы
async function logout() {
    if (!confirm('Вы уверены, что хотите выйти?')) {
        return;
    }
    
    try {
        const response = await fetch('../api/logout.php');
        const result = await response.json();
        
        if (result.success) {
            window.location.href = 'login.php';
        }
    } catch (error) {
        console.error('Ошибка выхода:', error);
        window.location.href = 'login.php';
    }
}

// Показ уведомлений
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        z-index: 3000;
        max-width: 400px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    const colors = {
        success: 'linear-gradient(135deg, #4CAF50, #45a049)',
        error: 'linear-gradient(135deg, #f44336, #d32f2f)',
        info: 'linear-gradient(135deg, #2196F3, #1976D2)',
        warning: 'linear-gradient(135deg, #ff9800, #f57c00)'
    };
    
    notification.style.background = colors[type] || colors.info;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Анимация появления
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Автоматическое скрытие
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 4000);
}

// Закрытие модального окна по клику на overlay
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        closeModal();
    }
});

// Закрытие модального окна по Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeModal();
    }
});
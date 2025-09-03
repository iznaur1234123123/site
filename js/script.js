// Основные функции сайта
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация
    initNavigation();
    initScrollAnimations();
    initMobileMenu();
    loadContent();
    loadPortfolio();
    loadReviews();
    
    // Плавная прокрутка к якорям
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const offsetTop = target.offsetTop - 80;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
});

// Навигация
function initNavigation() {
    const navbar = document.getElementById('navbar');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

// Мобильное меню
function initMobileMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
        
        // Закрытие меню при клике на ссылку
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            });
        });
    }
}

// Анимации при прокрутке
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                
                // Анимация счетчиков
                if (entry.target.classList.contains('stat-number')) {
                    animateNumber(entry.target);
                }
            }
        });
    }, observerOptions);
    
    // Наблюдаем за элементами
    document.querySelectorAll('.about-card, .service-card, .portfolio-item, .contact-item, .stat-item, .review-item').forEach(el => {
        observer.observe(el);
    });
}

// Анимация чисел
function animateNumber(element) {
    const target = parseInt(element.dataset.number);
    const duration = 2000;
    const start = performance.now();
    
    function updateNumber(currentTime) {
        const elapsed = currentTime - start;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.floor(progress * target);
        element.textContent = current + (element.textContent.includes('%') ? '%' : '+');
        
        if (progress < 1) {
            requestAnimationFrame(updateNumber);
        }
    }
    
    requestAnimationFrame(updateNumber);
}

// Загрузка контента из API
async function loadContent() {
    try {
        const response = await fetch('api/content.php');
        const content = await response.json();
        
        // Обновляем контент на странице
        Object.keys(content).forEach(key => {
            const elements = document.querySelectorAll(`[data-content="${key}"]`);
            elements.forEach(element => {
                if (element.tagName === 'IMG') {
                    element.src = content[key];
                } else {
                    element.textContent = content[key];
                }
            });
        });
    } catch (error) {
        console.log('Контент загружен по умолчанию');
    }
}

// Загрузка портфолио
async function loadPortfolio() {
    try {
        const response = await fetch('api/portfolio.php');
        const portfolio = await response.json();
        
        const portfolioGrid = document.getElementById('portfolio-grid');
        if (portfolioGrid) {
            portfolioGrid.innerHTML = '';
            
            portfolio.forEach(item => {
                const portfolioItem = document.createElement('div');
                portfolioItem.className = 'portfolio-item';
                portfolioItem.innerHTML = `
                    <img src="${item.image}" alt="${item.title}" loading="lazy">
                    <div class="portfolio-content">
                        <h3>${item.title}</h3>
                        <p>${item.description}</p>
                    </div>
                `;
                portfolioGrid.appendChild(portfolioItem);
            });
        }
    } catch (error) {
        console.log('Портфолио загружено по умолчанию');
        loadDefaultPortfolio();
    }
}

// Загрузка отзывов
async function loadReviews() {
    try {
        const response = await fetch('api/reviews.php');
        const reviews = await response.json();
        
        const reviewsGrid = document.getElementById('reviews-grid');
        if (reviewsGrid) {
            reviewsGrid.innerHTML = '';
            
            reviews.forEach(review => {
                const reviewItem = document.createElement('div');
                reviewItem.className = 'review-item';
                reviewItem.innerHTML = `
                    <div class="review-text">"${review.text}"</div>
                    <div class="review-author">
                        <img src="${review.avatar}" alt="${review.name}" loading="lazy">
                        <div class="review-author-info">
                            <h4>${review.name}</h4>
                            <p>${review.event}</p>
                        </div>
                    </div>
                `;
                reviewsGrid.appendChild(reviewItem);
            });
        }
    } catch (error) {
        console.log('Отзывы загружены по умолчанию');
        loadDefaultReviews();
    }
}

// Загрузка портфолио по умолчанию
function loadDefaultPortfolio() {
    const portfolioGrid = document.getElementById('portfolio-grid');
    if (portfolioGrid) {
        portfolioGrid.innerHTML = `
            <div class="portfolio-item">
                <img src="uploads/portfolio1.jpg" alt="Свадьба" loading="lazy">
                <div class="portfolio-content">
                    <h3>Свадьба Анны и Михаила</h3>
                    <p>Романтичная церемония в загородном клубе</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="uploads/portfolio2.jpg" alt="Корпоратив" loading="lazy">
                <div class="portfolio-content">
                    <h3>Корпоратив IT-компании</h3>
                    <p>Новогодний корпоратив с интерактивными играми</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="uploads/portfolio3.jpg" alt="День рождения" loading="lazy">
                <div class="portfolio-content">
                    <h3>Юбилей 50 лет</h3>
                    <p>Торжественный вечер в честь юбилея</p>
                </div>
            </div>
        `;
    }
}

// Загрузка отзывов по умолчанию
function loadDefaultReviews() {
    const reviewsGrid = document.getElementById('reviews-grid');
    if (reviewsGrid) {
        reviewsGrid.innerHTML = `
            <div class="review-item">
                <div class="review-text">"Булат сделал нашу свадьбу незабываемой! Профессиональный подход, отличная организация и потрясающая атмосфера. Рекомендую всем!"</div>
                <div class="review-author">
                    <img src="uploads/avatar1.jpg" alt="Анна" loading="lazy">
                    <div class="review-author-info">
                        <h4>Анна Петрова</h4>
                        <p>Свадьба</p>
                    </div>
                </div>
            </div>
            <div class="review-item">
                <div class="review-text">"Отличный ведущий! Наш корпоратив прошел на высшем уровне. Все сотрудники остались довольны. Спасибо за профессионализм!"</div>
                <div class="review-author">
                    <img src="uploads/avatar2.jpg" alt="Михаил" loading="lazy">
                    <div class="review-author-info">
                        <h4>Михаил Иванов</h4>
                        <p>Корпоратив</p>
                    </div>
                </div>
            </div>
            <div class="review-item">
                <div class="review-text">"Булат провел мой день рождения просто потрясающе! Гости до сих пор вспоминают этот вечер. Очень рекомендую!"</div>
                <div class="review-author">
                    <img src="uploads/avatar3.jpg" alt="Елена" loading="lazy">
                    <div class="review-author-info">
                        <h4>Елена Смирнова</h4>
                        <p>День рождения</p>
                    </div>
                </div>
            </div>
        `;
    }
}

// Загрузка дополнительного портфолио
function loadMorePortfolio() {
    const portfolioGrid = document.getElementById('portfolio-grid');
    if (portfolioGrid) {
        const additionalItems = [
            {
                image: 'uploads/portfolio4.jpg',
                title: 'Выпускной вечер',
                description: 'Торжественный выпускной в школе'
            },
            {
                image: 'uploads/portfolio5.jpg',
                title: 'Открытие ресторана',
                description: 'Презентация нового ресторана'
            },
            {
                image: 'uploads/portfolio6.jpg',
                title: 'Детский праздник',
                description: 'День рождения ребенка с анимацией'
            }
        ];
        
        additionalItems.forEach(item => {
            const portfolioItem = document.createElement('div');
            portfolioItem.className = 'portfolio-item';
            portfolioItem.innerHTML = `
                <img src="${item.image}" alt="${item.title}" loading="lazy">
                <div class="portfolio-content">
                    <h3>${item.title}</h3>
                    <p>${item.description}</p>
                </div>
            `;
            portfolioGrid.appendChild(portfolioItem);
        });
    }
}

// Параллакс эффект для hero секции
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const heroVideo = document.getElementById('hero-video');
    
    if (heroVideo) {
        heroVideo.style.transform = `translateY(${scrolled * 0.5}px)`;
    }
});

// Lazy loading для изображений
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// Инициализация после загрузки DOM
document.addEventListener('DOMContentLoaded', () => {
    initLazyLoading();
});
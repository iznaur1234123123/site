// Основные функции сайта
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация
    initNavigation();
    initScrollAnimations();
    initMobileMenu();
    loadContent();
    loadPortfolio();
    
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

// Ультра-современные анимации при прокрутке
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
                
                // Добавляем эффект свечения для карточек
                if (entry.target.classList.contains('service-card')) {
                    setTimeout(() => {
                        entry.target.style.animation = 'glowPulse 2s ease-in-out infinite';
                    }, 500);
                }
                
                // Анимация для заголовков секций
                if (entry.target.classList.contains('section-title')) {
                    entry.target.style.animation = 'titleGlow 3s ease-in-out infinite alternate';
                }
            }
        });
    }, observerOptions);
    
    // Наблюдаем за элементами
    document.querySelectorAll('.about-card, .service-card, .portfolio-item, .contact-item, .stat-item, .section-title').forEach(el => {
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



// Уведомления
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        background: ${type === 'success' ? '#28a745' : '#dc3545'};
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Загрузка контента
async function loadContent() {
    try {
        const response = await fetch('api/content.php');
        const content = await response.json();
        
        // Обновляем текстовый контент
        document.querySelectorAll('[data-content]').forEach(element => {
            const key = element.dataset.content;
            if (content[key]) {
                if (element.tagName === 'A') {
                    element.href = content[key];
                } else {
                    element.textContent = content[key];
                }
            }
        });
        
        // Обновляем изображения
        document.querySelectorAll('[data-image]').forEach(element => {
            const key = element.dataset.image;
            if (content[key]) {
                element.src = content[key];
            }
        });
        
    } catch (error) {
        console.log('Контент загружается из статических данных');
    }
}

// Загрузка портфолио
let portfolioOffset = 0;
const portfolioLimit = 6;

async function loadPortfolio() {
    try {
        const response = await fetch(`api/portfolio.php?offset=${portfolioOffset}&limit=${portfolioLimit}`);
        const portfolio = await response.json();
        
        const grid = document.getElementById('portfolio-grid');
        
        portfolio.forEach(item => {
            const portfolioItem = document.createElement('div');
            portfolioItem.className = 'portfolio-item';
            portfolioItem.innerHTML = `
                <img src="${item.image}" alt="${item.title}">
                <div class="portfolio-overlay">
                    <div class="portfolio-info">
                        <h3>${item.title}</h3>
                        <p>${item.description}</p>
                    </div>
                </div>
            `;
            grid.appendChild(portfolioItem);
        });
        
        portfolioOffset += portfolioLimit;
        
    } catch (error) {
        // Загружаем демо-контент
        loadDemoPortfolio();
    }
}

function loadDemoPortfolio() {
    const demoItems = [
        { title: 'Свадьба Анны и Дмитрия', description: 'Романтичная церемония в загородном клубе', image: 'uploads/portfolio1.jpg' },
        { title: 'Корпоратив IT-компании', description: 'Современный формат с интерактивными играми', image: 'uploads/portfolio2.jpg' },
        { title: 'Юбилей 50 лет', description: 'Семейный праздник с теплой атмосферой', image: 'uploads/portfolio3.jpg' },
        { title: 'Новогодний корпоратив', description: 'Яркое шоу с артистами и конкурсами', image: 'uploads/portfolio4.jpg' },
        { title: 'Детский день рождения', description: 'Веселый праздник с аниматорами', image: 'uploads/portfolio5.jpg' },
        { title: 'Выпускной вечер', description: 'Торжественное мероприятие для выпускников', image: 'uploads/portfolio6.jpg' }
    ];
    
    const grid = document.getElementById('portfolio-grid');
    
    demoItems.forEach(item => {
        const portfolioItem = document.createElement('div');
        portfolioItem.className = 'portfolio-item';
        portfolioItem.innerHTML = `
            <img src="${item.image}" alt="${item.title}" onerror="this.src='https://via.placeholder.com/400x300/667eea/white?text=${encodeURIComponent(item.title)}'">
            <div class="portfolio-overlay">
                <div class="portfolio-info">
                    <h3>${item.title}</h3>
                    <p>${item.description}</p>
                </div>
            </div>
        `;
        grid.appendChild(portfolioItem);
    });
}

function loadMorePortfolio() {
    loadPortfolio();
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

// Дополнительные интерактивные эффекты
function initAdvancedEffects() {
    // Эффект печатания для заголовков
    const typewriterElements = document.querySelectorAll('.hero-title .title-line');
    typewriterElements.forEach(element => {
        const text = element.textContent;
        element.textContent = '';
        element.style.borderRight = '2px solid var(--accent-primary)';
        
        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            } else {
                element.style.borderRight = 'none';
            }
        };
        
        setTimeout(typeWriter, 1000);
    });
    
    // Эффект морфинга для кнопок
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', () => {
            button.style.clipPath = 'polygon(0 0, 100% 0, 100% 100%, 0 100%)';
        });
        
        button.addEventListener('mouseleave', () => {
            button.style.clipPath = 'polygon(0 0, 100% 0, 100% 100%, 0 100%)';
        });
    });
    
    // Эффект волны при клике
    document.addEventListener('click', (e) => {
        const ripple = document.createElement('div');
        ripple.style.cssText = `
            position: fixed;
            width: 20px;
            height: 20px;
            background: radial-gradient(circle, rgba(0, 245, 255, 0.6) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            left: ${e.clientX - 10}px;
            top: ${e.clientY - 10}px;
            animation: ripple 0.6s ease-out forwards;
        `;
        
        document.body.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
}

// Оптимизированный эффект скролла
function initSmoothScroll() {
    let isScrolling = false;
    let scrollTimeout;
    
    window.addEventListener('scroll', () => {
        if (!isScrolling) {
            window.requestAnimationFrame(() => {
                // Добавляем эффект размытия при быстром скролле
                const scrollSpeed = Math.abs(window.scrollY - (window.lastScrollY || 0));
                if (scrollSpeed > 10) {
                    document.body.style.filter = 'blur(1px)';
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        document.body.style.filter = 'none';
                    }, 100);
                }
                
                window.lastScrollY = window.scrollY;
                isScrolling = false;
            });
        }
        isScrolling = true;
    });
}

// Оптимизация производительности
function optimizePerformance() {
    // Отключаем анимации на слабых устройствах
    if (navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4) {
        document.documentElement.style.setProperty('--transition', 'none');
        document.documentElement.style.setProperty('--transition-fast', 'none');
        document.documentElement.style.setProperty('--transition-slow', 'none');
    }
    
    // Оптимизация для мобильных устройств
    if (window.innerWidth < 768) {
        // Уменьшаем количество частиц
        const particles = document.querySelectorAll('.particles div');
        particles.forEach((particle, index) => {
            if (index > 20) {
                particle.style.display = 'none';
            }
        });
    }
}

// Инициализация после загрузки DOM
document.addEventListener('DOMContentLoaded', () => {
    initLazyLoading();
    initAdvancedEffects();
    initSmoothScroll();
    optimizePerformance();
});

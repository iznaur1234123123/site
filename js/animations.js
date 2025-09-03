// Ультра-современные анимации и эффекты
document.addEventListener('DOMContentLoaded', function() {
    initParticles();
    initTextAnimations();
    initScrollProgress();
    initSmoothReveal();
    initParallaxEffects();
    initMagneticButtons();
    initScrollReveal();
    initFloatingElements();
    initCursorEffects();
});

// Частицы на фоне
function initParticles() {
    const hero = document.querySelector('.hero');
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'particles';
    particlesContainer.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    `;
    
    hero.appendChild(particlesContainer);
    
    // Создание частиц
    for (let i = 0; i < 50; i++) {
        createParticle(particlesContainer);
    }
}

function createParticle(container) {
    const particle = document.createElement('div');
    particle.style.cssText = `
        position: absolute;
        width: 4px;
        height: 4px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        animation: float ${5 + Math.random() * 10}s infinite linear;
        left: ${Math.random() * 100}%;
        top: ${Math.random() * 100}%;
        opacity: ${0.3 + Math.random() * 0.7};
    `;
    
    container.appendChild(particle);
    
    // Анимация частицы
    const keyframes = `
        @keyframes float {
            0% {
                transform: translateY(0px) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }
    `;
    
    if (!document.querySelector('#particles-styles')) {
        const style = document.createElement('style');
        style.id = 'particles-styles';
        style.textContent = keyframes;
        document.head.appendChild(style);
    }
    
    // Пересоздание частицы после анимации
    setTimeout(() => {
        particle.remove();
        createParticle(container);
    }, (5 + Math.random() * 10) * 1000);
}

// Анимация текста по буквам
function initTextAnimations() {
    const textElements = document.querySelectorAll('.hero-title .title-line');
    
    textElements.forEach(element => {
        const text = element.textContent;
        element.innerHTML = '';
        
        // Разбиваем текст на буквы
        [...text].forEach((char, index) => {
            const span = document.createElement('span');
            span.textContent = char === ' ' ? '\u00A0' : char;
            span.style.cssText = `
                display: inline-block;
                animation: letterDrop 0.6s ease ${index * 0.1}s both;
            `;
            element.appendChild(span);
        });
    });
    
    // CSS для анимации букв
    const letterKeyframes = `
        @keyframes letterDrop {
            0% {
                opacity: 0;
                transform: translateY(-50px) rotateX(90deg);
            }
            100% {
                opacity: 1;
                transform: translateY(0) rotateX(0deg);
            }
        }
    `;
    
    if (!document.querySelector('#letter-styles')) {
        const style = document.createElement('style');
        style.id = 'letter-styles';
        style.textContent = letterKeyframes;
        document.head.appendChild(style);
    }
}



// Прогресс скролла
function initScrollProgress() {
    const progressBar = document.createElement('div');
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        z-index: 10000;
        transition: width 0.1s ease;
        width: 0%;
    `;
    
    document.body.appendChild(progressBar);
    
    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset;
        const documentHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / documentHeight) * 100;
        
        progressBar.style.width = scrollPercent + '%';
    });
}

// Плавное появление элементов
function initSmoothReveal() {
    const revealElements = document.querySelectorAll('.about-card, .service-card, .contact-item');
    
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.cssText += `
                        opacity: 1;
                        transform: translateY(0);
                        transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                    `;
                }, index * 100);
                
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    revealElements.forEach(element => {
        element.style.cssText += `
            opacity: 0;
            transform: translateY(50px);
        `;
        revealObserver.observe(element);
    });
}

// Параллакс для секций
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    
    // Параллакс для изображений в портфолио
    document.querySelectorAll('.portfolio-item img').forEach((img, index) => {
        const rate = scrolled * -0.5;
        img.style.transform = `translateY(${rate}px)`;
    });
    
    // Параллакс для иконок услуг
    document.querySelectorAll('.service-icon').forEach((icon, index) => {
        const rate = Math.sin(scrolled * 0.01 + index) * 10;
        icon.style.transform = `translateY(${rate}px)`;
    });
});

// Эффект печатающего текста
function typeWriter(element, text, speed = 50) {
    element.innerHTML = '';
    let i = 0;
    
    function typeChar() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(typeChar, speed);
        }
    }
    
    typeChar();
}

// Волновой эффект при клике
document.addEventListener('click', (e) => {
    const ripple = document.createElement('div');
    ripple.style.cssText = `
        position: absolute;
        border-radius: 50%;
        background: rgba(102, 126, 234, 0.3);
        transform: scale(0);
        animation: ripple-effect 0.6s linear;
        pointer-events: none;
        z-index: 9999;
    `;
    
    const rect = e.target.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
    ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
    
    e.target.style.position = 'relative';
    e.target.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
});

// CSS для эффекта волны
const rippleKeyframes = `
    @keyframes ripple-effect {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;

if (!document.querySelector('#ripple-styles')) {
    const style = document.createElement('style');
    style.id = 'ripple-styles';
    style.textContent = rippleKeyframes;
    document.head.appendChild(style);
}

// Магнитный эффект для кнопок
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('mousemove', (e) => {
        const rect = button.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;
        
        const distance = Math.sqrt(x * x + y * y);
        const maxDistance = Math.max(rect.width, rect.height);
        
        if (distance < maxDistance) {
            const strength = (maxDistance - distance) / maxDistance;
            const translateX = (x / maxDistance) * 20 * strength;
            const translateY = (y / maxDistance) * 20 * strength;
            
            button.style.transform = `translate(${translateX}px, ${translateY}px)`;
        }
    });
    
    button.addEventListener('mouseleave', () => {
        button.style.transform = 'translate(0, 0)';
    });
});

// Анимация появления при скролле с задержкой
function staggerAnimation(elements, className, delay = 100) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add(className);
                }, index * delay);
                observer.unobserve(entry.target);
            }
        });
    });
    
    elements.forEach(element => observer.observe(element));
}

// Применяем анимации с задержкой
document.addEventListener('DOMContentLoaded', () => {
    const serviceCards = document.querySelectorAll('.service-card');
    const portfolioItems = document.querySelectorAll('.portfolio-item');
    
    staggerAnimation(serviceCards, 'fade-in', 150);
    staggerAnimation(portfolioItems, 'fade-in', 200);
});

// Оптимизированные параллакс эффекты
function initParallaxEffects() {
    const parallaxElements = document.querySelectorAll('.hero-background, .hero-overlay');
    let ticking = false;
    
    function updateParallax() {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        
        parallaxElements.forEach(element => {
            element.style.transform = `translate3d(0, ${rate}px, 0)`;
        });
        
        ticking = false;
    }
    
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(updateParallax);
            ticking = true;
        }
    });
}

// Магнитные кнопки
function initMagneticButtons() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.addEventListener('mousemove', (e) => {
            const rect = button.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            button.style.transform = `translate(${x * 0.1}px, ${y * 0.1}px) scale(1.05)`;
        });
        
        button.addEventListener('mouseleave', () => {
            button.style.transform = 'translate(0, 0) scale(1)';
        });
    });
}

// Продвинутые анимации появления
function initScrollReveal() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                
                // Добавляем эффект волны
                if (entry.target.classList.contains('service-card')) {
                    setTimeout(() => {
                        entry.target.style.animation = 'waveIn 0.6s ease-out';
                    }, 100);
                }
            }
        });
    }, observerOptions);
    
    // Наблюдаем за элементами
    document.querySelectorAll('.about-card, .service-card, .portfolio-item, .contact-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(50px)';
        el.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        observer.observe(el);
    });
}

// Плавающие элементы
function initFloatingElements() {
    const floatingElements = document.querySelectorAll('.service-icon, .stat-item');
    
    floatingElements.forEach((element, index) => {
        const delay = index * 0.5;
        const duration = 3 + Math.random() * 2;
        
        element.style.animation = `float ${duration}s ease-in-out infinite`;
        element.style.animationDelay = `${delay}s`;
    });
}

// Эффекты курсора
function initCursorEffects() {
    const cursor = document.createElement('div');
    cursor.className = 'custom-cursor';
    cursor.style.cssText = `
        position: fixed;
        width: 20px;
        height: 20px;
        background: radial-gradient(circle, rgba(0, 245, 255, 0.8) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
        z-index: 9999;
        transition: all 0.1s ease;
        mix-blend-mode: difference;
    `;
    document.body.appendChild(cursor);
    
    document.addEventListener('mousemove', (e) => {
        cursor.style.left = e.clientX - 10 + 'px';
        cursor.style.top = e.clientY - 10 + 'px';
    });
    
    // Увеличение курсора при наведении на интерактивные элементы
    const interactiveElements = document.querySelectorAll('a, button, .btn');
    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', () => {
            cursor.style.transform = 'scale(2)';
            cursor.style.background = 'radial-gradient(circle, rgba(255, 0, 128, 0.8) 0%, transparent 70%)';
        });
        
        element.addEventListener('mouseleave', () => {
            cursor.style.transform = 'scale(1)';
            cursor.style.background = 'radial-gradient(circle, rgba(0, 245, 255, 0.8) 0%, transparent 70%)';
        });
    });
}


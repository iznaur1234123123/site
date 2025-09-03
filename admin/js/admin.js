// Административная панель - JavaScript функции
document.addEventListener('DOMContentLoaded', function() {
    initAdmin();
});

function initAdmin() {
    // Инициализация общих функций
    initNotifications();
    initMobileMenu();
    initAutoSave();
    initConfirmations();
    initTooltips();
}

// Управление уведомлениями
function initNotifications() {
    // Автоматическое скрытие уведомлений
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(notification => {
        if (notification.classList.contains('show')) {
            setTimeout(() => {
                hideNotification(notification);
            }, 5000);
        }
    });
}

function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    const icon = getNotificationIcon(type);
    notification.innerHTML = `<i class="${icon}"></i> ${message}`;
    
    document.body.appendChild(notification);
    
    // Показываем уведомление
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Автоматически скрываем
    setTimeout(() => {
        hideNotification(notification);
    }, duration);
    
    return notification;
}

function hideNotification(notification) {
    notification.classList.remove('show');
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

function getNotificationIcon(type) {
    const icons = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-triangle',
        'warning': 'fas fa-exclamation-circle',
        'info': 'fas fa-info-circle'
    };
    return icons[type] || icons['info'];
}

// Мобильное меню для админки
function initMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    // Создаем кнопку меню для мобильных устройств
    if (window.innerWidth <= 768) {
        const menuToggle = document.createElement('button');
        menuToggle.className = 'mobile-menu-toggle';
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        menuToggle.style.cssText = `
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px;
            cursor: pointer;
        `;
        
        document.body.appendChild(menuToggle);
        
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
        });
        
        // Закрытие меню при клике вне его
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
    }
}

// Автосохранение форм
function initAutoSave() {
    const forms = document.querySelectorAll('form[data-autosave]');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            let timer;
            const originalValue = input.value;
            
            input.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    if (input.value !== originalValue && input.value.trim() !== '') {
                        autoSaveForm(form);
                    }
                }, 2000);
            });
        });
    });
}

function autoSaveForm(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    
    if (submitBtn) {
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
        submitBtn.disabled = true;
    }
    
    fetch(form.action || '', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Изменения сохранены', 'success', 2000);
        } else {
            showNotification('Ошибка сохранения', 'error');
        }
    })
    .catch(error => {
        showNotification('Ошибка соединения', 'error');
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
}

// Подтверждения действий
function initConfirmations() {
    const dangerButtons = document.querySelectorAll('.btn-danger, [data-confirm]');
    
    dangerButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const message = button.dataset.confirm || 'Вы уверены?';
            
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });
}

// Подсказки
function initTooltips() {
    const elements = document.querySelectorAll('[title]');
    
    elements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const element = e.target;
    const title = element.getAttribute('title');
    
    if (!title) return;
    
    // Скрываем стандартную подсказку
    element.setAttribute('data-original-title', title);
    element.removeAttribute('title');
    
    const tooltip = document.createElement('div');
    tooltip.className = 'custom-tooltip';
    tooltip.textContent = title;
    tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        z-index: 10000;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    
    document.body.appendChild(tooltip);
    
    // Позиционируем подсказку
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
    
    setTimeout(() => {
        tooltip.style.opacity = '1';
    }, 100);
    
    element._tooltip = tooltip;
}

function hideTooltip(e) {
    const element = e.target;
    const tooltip = element._tooltip;
    
    if (tooltip) {
        tooltip.style.opacity = '0';
        setTimeout(() => {
            if (tooltip.parentNode) {
                tooltip.parentNode.removeChild(tooltip);
            }
        }, 300);
        delete element._tooltip;
    }
    
    // Возвращаем оригинальный title
    const originalTitle = element.getAttribute('data-original-title');
    if (originalTitle) {
        element.setAttribute('title', originalTitle);
        element.removeAttribute('data-original-title');
    }
}

// Утилиты для работы с API
async function apiRequest(url, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    const config = { ...defaultOptions, ...options };
    
    try {
        const response = await fetch(url, config);
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Ошибка запроса');
        }
        
        return data;
    } catch (error) {
        showNotification(error.message || 'Ошибка соединения', 'error');
        throw error;
    }
}

// Функции для работы с контентом
async function updateContent(key, value) {
    try {
        const data = await apiRequest('../api/content.php', {
            method: 'POST',
            body: JSON.stringify({ key, value })
        });
        
        if (data.success) {
            showNotification('Контент обновлен', 'success');
        }
        
        return data;
    } catch (error) {
        console.error('Ошибка обновления контента:', error);
    }
}

// Функции для работы с изображениями
function previewImage(input, previewContainer) {
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            let preview = previewContainer.querySelector('.image-preview');
            
            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'image-preview';
                preview.style.cssText = `
                    margin-top: 15px;
                    text-align: center;
                `;
                previewContainer.appendChild(preview);
            }
            
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Предварительный просмотр" 
                     style="max-width: 200px; max-height: 150px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <p style="margin-top: 10px; font-size: 0.9rem; color: #6b7280;">
                    ${file.name} (${formatFileSize(file.size)})
                </p>
            `;
        };
        
        reader.readAsDataURL(file);
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Функции для работы с таблицами
function sortTable(table, column, direction = 'asc') {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aVal = a.children[column].textContent.trim();
        const bVal = b.children[column].textContent.trim();
        
        if (direction === 'asc') {
            return aVal.localeCompare(bVal, 'ru', { numeric: true });
        } else {
            return bVal.localeCompare(aVal, 'ru', { numeric: true });
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

function filterTable(table, searchTerm) {
    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Функция для копирования текста в буфер обмена
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Скопировано в буфер обмена', 'success', 2000);
        });
    } else {
        // Fallback для старых браузеров
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        
        try {
            document.execCommand('copy');
            showNotification('Скопировано в буфер обмена', 'success', 2000);
        } catch (err) {
            showNotification('Ошибка копирования', 'error');
        }
        
        document.body.removeChild(textArea);
    }
}

// Функции для анимаций
function slideUp(element, duration = 300) {
    element.style.transition = `height ${duration}ms ease`;
    element.style.height = element.offsetHeight + 'px';
    element.style.overflow = 'hidden';
    
    requestAnimationFrame(() => {
        element.style.height = '0px';
        
        setTimeout(() => {
            element.style.display = 'none';
            element.style.height = '';
            element.style.overflow = '';
            element.style.transition = '';
        }, duration);
    });
}

function slideDown(element, duration = 300) {
    element.style.display = 'block';
    const height = element.offsetHeight;
    element.style.height = '0px';
    element.style.overflow = 'hidden';
    element.style.transition = `height ${duration}ms ease`;
    
    requestAnimationFrame(() => {
        element.style.height = height + 'px';
        
        setTimeout(() => {
            element.style.height = '';
            element.style.overflow = '';
            element.style.transition = '';
        }, duration);
    });
}

// Обработка ошибок загрузки изображений
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img');
    
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzZiNzI4MCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPtCY0LfQvtCx0YDQsNC20LXQvdC40LUg0L3QtSDQvdCw0LnQtNC10L3QvjwvdGV4dD48L3N2Zz4=';
        });
    });
});

// Глобальные переменные
window.AdminJS = {
    showNotification,
    hideNotification,
    apiRequest,
    updateContent,
    previewImage,
    copyToClipboard,
    formatFileSize,
    sortTable,
    filterTable,
    slideUp,
    slideDown
};

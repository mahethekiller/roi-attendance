import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import * as bootstrap from 'bootstrap';
import { createIcons, icons } from 'lucide';

window.bootstrap = bootstrap;

// Initialize Lucide Icons globally
export function renderLucideIcons() {
    createIcons({ icons });
}
window.renderLucideIcons = renderLucideIcons;

// Theme Controller
export function setupThemeController() {
    const htmlElement = document.documentElement;
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    
    function updateThemeUI(theme) {
        htmlElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('roi_theme', theme);
        
        if (themeToggleBtn) {
            const isDark = theme === 'dark';
            const iconName = isDark ? 'sun' : 'moon';
            themeToggleBtn.setAttribute('aria-label', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
            themeToggleBtn.setAttribute('title', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
            themeToggleBtn.innerHTML = `<i data-lucide="${iconName}" style="width: 18px; height: 18px;"></i>`;
            renderLucideIcons();
        }
    }

    const currentTheme = localStorage.getItem('roi_theme') || htmlElement.getAttribute('data-bs-theme') || 'dark';
    updateThemeUI(currentTheme);

    if (themeToggleBtn && !themeToggleBtn.dataset.themeBound) {
        themeToggleBtn.dataset.themeBound = 'true';
        themeToggleBtn.addEventListener('click', () => {
            const nextTheme = htmlElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            updateThemeUI(nextTheme);
        });
    }
}
window.setupThemeController = setupThemeController;

// Keyboard Shortcuts (Ctrl+K / / to search)
export function setupKeyboardShortcuts() {
    document.addEventListener('keydown', (event) => {
        const searchInput = document.getElementById('globalSearchInput');
        if (!searchInput) return;

        const isModifierKey = event.ctrlKey || event.metaKey;
        const isSearchShortcut = (isModifierKey && event.key.toLowerCase() === 'k');
        const isSlashKey = (event.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName));

        if (isSearchShortcut || isSlashKey) {
            event.preventDefault();
            searchInput.focus();
            searchInput.select();
        } else if (event.key === 'Escape' && document.activeElement === searchInput) {
            searchInput.blur();
        }
    });
}
window.setupKeyboardShortcuts = setupKeyboardShortcuts;

document.addEventListener('DOMContentLoaded', () => {
    setupThemeController();
    setupKeyboardShortcuts();
    renderLucideIcons();
});

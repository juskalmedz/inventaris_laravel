import './bootstrap';

// Inisialisasi ikon Lucide
export function refreshLucideIcons() {
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
}

// Dark Mode Controller
export function initTheme() {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

export function toggleTheme() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    refreshLucideIcons();
}

window.toggleTheme = toggleTheme;
window.toggleThemeMode = toggleTheme;
window.refreshLucideIcons = refreshLucideIcons;

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    refreshLucideIcons();
});

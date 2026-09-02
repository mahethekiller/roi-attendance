import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import * as bootstrap from 'bootstrap';
import { createIcons, icons } from 'lucide';

window.bootstrap = bootstrap;

// Initialize Lucide Icons globally
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

// Helper to re-initialize icons on dynamic updates
window.renderLucideIcons = () => {
    createIcons({ icons });
};

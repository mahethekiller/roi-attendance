# Workspace Agent Guidelines

## Theme & Dark/Light Mode Compliance
- Always ensure all Blade views, components, tables, forms, badges, and cards are 100% compatible with both Light Mode and Dark Mode (`data-bs-theme="dark"`).
- Never use hardcoded light backgrounds (e.g. `bg-white`, `bg-light`, `#F1F5F9`) or hardcoded dark text colors (`color: #0F172A`, `text-gray-900`) without corresponding `[data-bs-theme="dark"]` override rules or theme variables (`bg-body`, `bg-body-tertiary`, `text-body-emphasis`, `text-body-secondary`).
- Ensure all `.table`, `.table th`, `.table td`, and `.card` elements maintain high-contrast legibility in both Light and Dark modes.

---

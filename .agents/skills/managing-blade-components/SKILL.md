---
name: managing-blade-components
description: Guides the creation of reusable, dynamic, and dark/light mode compatible Laravel Blade components. Use when creating Blade layout views, UI components, form controls, badges, cards, or dynamic tables.
---

# Managing Blade Components

## When to use this skill
- Building reusable Blade UI components (`resources/views/components/*.php` or `.blade.php`).
- Creating form fields, modal dialogs, data tables, metrics cards, or badges.
- Ensuring dynamic themes (`data-bs-theme="dark"`) and high contrast in light/dark modes.

## Workflow Checklist
- [ ] Ensure full support for Bootstrap 5.3 theme variables (`bg-body`, `bg-body-tertiary`, `text-body-emphasis`).
- [ ] Use `$attributes->merge()` to allow flexible class & property passing.
- [ ] Handle props using `@props(['variant' => 'primary', 'title' => ''])`.
- [ ] Test component layout contrast in both light and dark mode contexts.

## Code Template

```blade
@props([
    'title' => '',
    'value' => '0',
    'icon' => 'bi-activity',
    'badge' => null,
    'badgeColor' => 'success'
])

<div {{ $attributes->merge(['class' => 'card border-0 shadow-sm bg-body-tertiary text-body']) }}>
    <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="text-body-secondary small fw-medium">{{ $title }}</span>
            <div class="rounded-circle p-2 bg-primary-subtle text-primary">
                <i class="bi {{ $icon }}"></i>
            </div>
        </div>
        <div class="d-flex align-items-baseline justify-content-between">
            <h4 class="mb-0 fw-bold text-body-emphasis">{{ $value }}</h4>
            @if($badge)
                <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} border border-{{ $badgeColor }}-subtle">
                    {{ $badge }}
                </span>
            @endif
        </div>
    </div>
</div>
```

## Guidelines
- Never use hardcoded `#FFF` or `#000` colors without dark mode theme rules.
- Prefer class composition with Bootstrap 5.3 CSS utilities.

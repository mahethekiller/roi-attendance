---
name: designing-dashboard-ui
description: Creates high-impact, modern, responsive UI/UX designs for analytical, management, employee, and executive dashboards. Ensures 100% Bootstrap 5.3 and Dark/Light Mode compliance, high contrast, rich visual aesthetics, KPI metrics cards, dynamic charts, data tables, and Blade components. Use when designing, building, or refactoring dashboard views, analytics pages, metrics overview grids, or reporting UIs.
---

# Designing Dashboard UI/UX

Expert guide for creating state-of-the-art, high-converting, and modern dashboards tailored for enterprise web applications, HR management portals, and analytical platforms.

---

## Key Principles & Architectural Blueprint

### 1. Visual Hierarchy & Grid Layout Structure
A world-class dashboard guides the user's attention from top-level summaries down to actionable details:
- **Header & Action Bar (Top)**: Page title, contextual subtitle, date range / period filter dropdowns, search bar, and primary call-to-action (CTA) buttons (e.g. Export, Add New).
- **KPI Metrics Row (Upper Grid)**: 3 to 4 metric tiles showcasing critical numbers, period-over-period trend percentages (`+12.4%`, `-3.1%`), and themed icon indicators.
- **Primary Analytics Grid (Main Body - 8:4 or 7:5 Ratio)**:
  - *Left / Wide Column*: Primary data visualization (time-series trend line chart, stacked bar breakdown, pipeline funnel).
  - *Right / Narrow Column*: Secondary overview widgets (quick activity feed, pending approvals, upcoming events, distribution pie chart).
- **Detailed Data View (Lower Grid)**: Filterable data table, pagination controls, status badges, and action dropdown menus.

---

## 2. Theme & Dark/Light Mode Compliance

To ensure compliance with the master workspace rules (`data-bs-theme="dark"`):

- **Bootstrap 5.3 Semantic Color System**:
  - Cards & Containers: Use `bg-body-tertiary` or `bg-body` instead of fixed `bg-white` or `bg-light`.
  - Text Elements: Use `text-body-emphasis` (headings), `text-body` (body text), and `text-body-secondary` or `text-muted` (labels/subtitles).
  - Borders: Use `border-subtle` or `border` for subtle, theme-adaptive divider lines.
- **Contrast & Legibility**:
  - Never use hardcoded inline styles like `color: #000` or `background: #fff`.
  - Use opacity-subtle badge backgrounds: `bg-primary-subtle text-primary`, `bg-success-subtle text-success`, `bg-danger-subtle text-danger`, `bg-warning-subtle text-warning`.
- **CSS Variable Enhancements**:
  ```css
  /* Dashboard Card Hover Animation & Elevation */
  .dashboard-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid var(--bs-border-color-translucent);
  }
  .dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--bs-box-shadow-sm);
  }
  ```

---

## 3. Executive KPI Metric Tile Blueprint

Use this reusable HTML/Blade blueprint for high-impact metric summary cards:

```html
<div class="col-xl-3 col-md-6 mb-4">
  <div class="card h-100 border-0 shadow-sm dashboard-card bg-body-tertiary">
    <div class="card-body p-3">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-body-secondary fs-7 fw-semibold text-uppercase tracking-wider">Total Active Employees</span>
        <div class="avatar-sm rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center p-2">
          <i class="fa-solid fa-users text-primary fs-5"></i>
        </div>
      </div>
      <div class="d-flex align-items-baseline justify-content-between">
        <h3 class="fw-bolder text-body-emphasis mb-0">1,248</h3>
        <span class="badge bg-success-subtle text-success fw-bold fs-9">
          <i class="fa-solid fa-arrow-up me-1"></i>+8.4%
        </span>
      </div>
      <div class="mt-2 text-muted fs-8">
        <span>vs. 1,151 last month</span>
      </div>
    </div>
  </div>
</div>
```

---

## 4. Theme-Aware Chart Styling (ApexCharts / Chart.js)

When integrating interactive charts in dark/light mode:
- **Color Palette Strategy**:
  - Primary Series: `#4F46E5` (Indigo / Soft Violet)
  - Success Series: `#10B981` (Emerald)
  - Warning/Pending Series: `#F59E0B` (Amber)
  - Secondary/Danger Series: `#EF4444` (Coral / Rose)
- **Theme Detection**:
  Dynamically read the document theme attribute to switch chart grid & font colors:
  ```javascript
  const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
  const textColor = isDark ? '#94A3B8' : '#64748B';
  const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
  ```

---

## 5. Table & List UX Best Practices

For data tables and list widgets inside dashboard views:
1. **Empty States**: Always include visually pleasing empty states when `$dataset->isEmpty()` with an icon, title, short description, and create button.
2. **Status Badges**: Use rounded pill badges with indicator dots:
   ```html
   <span class="badge rounded-pill bg-success-subtle text-success px-2 py-1 fs-9 fw-semibold">
     <span class="spinner-grow spinner-grow-sm text-success me-1 d-inline-block" style="width: 6px; height: 6px;"></span>
     Active
   </span>
   ```
3. **Permissions Gating**: Use `@can('permission.name')` to wrap sensitive actions (e.g. Edit, Delete, Export) and widgets.

---

## 6. Implementation Checklist

When creating or refactoring a Dashboard View:
- [ ] Header includes descriptive Title, Breadcrumbs, and Contextual Action Filters.
- [ ] Top Row features 3–4 high-impact KPI metric tiles with trend indicators.
- [ ] Theme uses Bootstrap 5.3 semantic classes (`bg-body-tertiary`, `text-body-emphasis`, `border-subtle`).
- [ ] Tested and verified in both Light Mode and Dark Mode (`data-bs-theme="dark"`).
- [ ] 0% hardcoded CDN links or forbidden utilities (Tailwind / Flowbite / Alpine.js).
- [ ] Charts dynamically adjust text/grid lines based on dark mode setting.
- [ ] Empty states and loading skeletons are provided for asynchronous/empty data.
- [ ] Authorization checks (`@can`) protect sensitive metric tiles and actions.

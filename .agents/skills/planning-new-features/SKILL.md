---
name: planning-new-features
description: Generates a comprehensive architectural and technical implementation plan for any new feature in the Laravel codebase. Formulates detailed user stories, database schema migrations, API endpoints, UI/UX Blade components, RBAC permissions, and testing strategies. Use when asked to plan, architect, or design a new feature.
---

# Planning New Features

## When to use this skill
- Planning, architecting, or designing a brand-new feature or module.
- Refactoring complex legacy modules requiring detailed impact analysis.
- Asking clarifying questions to align on feature requirements before writing code.

## Workflow Checklist
- [ ] **Step 1: Requirements Gathering & Clarification**
  - Identify missing details or ambiguities in user goals.
  - Ask targeted questions using `ask_question` tool if details are missing.
- [ ] **Step 2: Architecture & Component Blueprint**
  - Define user stories and acceptance criteria.
  - Map out database migrations & Eloquent relationship updates.
  - Outline API endpoints & request/response payloads.
  - Design UI/UX Blade components with Light/Dark mode compliance (`data-bs-theme="dark"`).
  - Specify Security & RBAC permission rules.
- [ ] **Step 3: Verification & Rollout Strategy**
  - Define unit/feature test requirements and manual testing steps.

---

## Plan Artifact Template

When executing this skill, output the feature plan using the following structure:

```markdown
# Feature Specification: [Feature Name]

## 1. Overview & Business Value
Brief summary of the feature, target users, and key objectives.

## 2. User Stories & Acceptance Criteria
- **User Story 1:** As a [Role], I want [Goal] so that [Benefit].
  - [ ] Criteria A
  - [ ] Criteria B

## 3. Database Schema & Models
### New/Modified Tables
- Migration file name: `YYYY_MM_DD_HHMMSS_create_feature_name_table.php`
- Schema columns & constraints:
  - `id` (bigIncrements)
  - `user_id` (foreignId -> users, cascade)
  - `status` (string, indexed)
  - `timestamps()`

### Model Updates
- New/Updated Eloquent model: [`app/Models/FeatureName.php`](file:///app/Models/FeatureName.php)
- Relationships, casts, and scopes.

## 4. API Endpoints & Data Transfer
| Method | Endpoint | Description | Middleware |
| :--- | :--- | :--- | :--- |
| GET | `/api/v1/feature` | List resources | `auth:sanctum` |
| POST | `/api/v1/feature` | Create resource | `auth:sanctum` |

### Form Request & Payload Validation
- `app/Http/Requests/StoreFeatureRequest.php`

## 5. UI/UX & Blade Components
- **Views:** `resources/views/features/index.blade.php`
- **Theme Compliance:**
  - Bootstrap 5.3 theme utilities (`bg-body-tertiary`, `text-body-emphasis`).
  - Dark mode (`data-bs-theme="dark"`) contrast verification.

## 6. Security, Authorization & RBAC
- **Roles & Permissions:** (e.g. `admin`, `manager`, `employee`).
- **Gates/Policies:** `app/Policies/FeaturePolicy.php`.

## 7. Verification & Testing Plan
- **Automated Tests:** `tests/Feature/FeatureTest.php`
- **Manual Checklist:**
  - [ ] Test form submission with valid & invalid data.
  - [ ] Verify dark/light mode rendering.
```

## Guidelines
- Never start coding a complex feature without generating and getting approval for a comprehensive plan.
- Ensure all file paths in generated plans are valid github-style links (`file:///...`).

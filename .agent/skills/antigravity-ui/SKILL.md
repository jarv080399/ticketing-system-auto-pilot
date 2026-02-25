---
name: antigravity-ui
description: >
    Design system and component guide for the Antigravity Admin Panel — a Vue.js + Tailwind CSS
    dark-mode-first admin interface. Use this skill whenever building, fixing, or reviewing any
    UI component, page, or layout for Antigravity. Triggers include: creating new admin pages,
    fixing inconsistencies in existing views, building Vue components, writing Tailwind class
    structures, implementing dark/light mode support, designing dashboards, forms, tables, cards,
    modals, badges, or navigation. Also use when the user says "make it match the design system",
    "add a new page", "fix the styling", "create a component", or references the admin panel UI.
    Do NOT skip this skill just because the task seems simple — even minor UI fixes should follow
    these conventions.
compatibility:
    - Vue 3 (Composition API preferred)
    - Tailwind CSS v3+
    - No external UI libraries unless noted
---

# Antigravity Admin Panel — UI Skill

## Overview

Antigravity is a **dark-mode-first** helpdesk/admin platform. The design language is:

- **Dark, dense, and technical** — not playful, not minimal-startup
- **Consistent structural hierarchy** — every page follows the same template
- **Semantic color tokens** — colors carry meaning, not just aesthetics
- **Dual-mode ready** — every component must work in both dark and light mode

Read `references/tokens.md` for the full color token system before writing any component.
Read `references/components.md` for all reusable component patterns.
Read `references/page-template.md` for the standard page layout structure.

---

## Critical Inconsistencies to Always Avoid

These are known issues in the current codebase. **Never introduce these patterns:**

### 1. Title Color Inconsistency

❌ **Wrong** — Only "Intelligence" page uses two-tone title

```html
<h1>
    <span class="text-white">System</span>
    <span class="text-teal-400">Intelligence</span>
</h1>
```

✅ **Correct** — All page titles use the same pattern

```html
<h1 class="text-white font-bold text-3xl">System Intelligence</h1>
```

> Exception: if a brand decision is made to use accent titles everywhere, apply it consistently to ALL pages.

### 2. Dark/Light Mode Token Usage

❌ **Wrong** — Hardcoded dark colors that break in light mode

```html
<div class="bg-[#1a2035] text-white"></div>
```

✅ **Correct** — Always use semantic Tailwind dark/light pairs

```html
<div
    class="bg-surface text-content-primary dark:bg-surface-dark dark:text-content-primary-dark"
></div>
```

### 3. Card Style Fragmentation

There are currently **two card styles** in use that should be unified. Always use the `<AdminCard>` component (see `references/components.md`).

### 4. Page Header CTA Placement

Primary actions (e.g. "Deploy New Rule", "Add Item") must **always** be top-right of the page header, never inline or at the bottom.

### 5. Spacing Scale

Use only these spacing values: `4, 6, 8, 12, 16, 24, 32px` (Tailwind: `p-1, p-1.5, p-2, p-3, p-4, p-6, p-8`). Do not use arbitrary values.

---

## Page Structure Template

Every admin page **must** follow this exact structure:

```vue
<template>
    <div class="flex-1 overflow-auto bg-bg dark:bg-bg-dark">
        <!-- Page Header -->
        <div class="px-8 pt-8 pb-6 flex items-start justify-between">
            <div>
                <h1
                    class="text-3xl font-bold text-content-primary dark:text-white"
                >
                    {{ pageTitle }}
                </h1>
                <p
                    class="mt-1 text-sm text-content-secondary dark:text-slate-400"
                >
                    {{ pageSubtitle }}
                </p>
            </div>
            <!-- Optional: Primary CTA always top-right -->
            <slot name="header-action" />
        </div>

        <!-- Page Content -->
        <div class="px-8 pb-8 space-y-6">
            <slot />
        </div>
    </div>
</template>
```

---

## Sidebar Navigation

The sidebar is fixed-width (`w-52`) and has two nav section groups:

```
PLATFORM
  - Dashboard
  - Intelligence

CONFIGURATION
  - General Settings
  - Automation Rules
  - Business Hours
  - Holidays
  - Custom Fields

SYSTEM
  - System Health
  - Activity Log

(divider)
  - Agent View
  - User Portal
```

Active nav item: `bg-slate-700/60 text-white rounded-md`
Inactive nav item: `text-slate-400 hover:text-white hover:bg-slate-700/30 rounded-md`
Section label: `text-[10px] font-semibold tracking-widest text-slate-500 uppercase px-3 mb-1`

---

## Status Badges

Badges must always use this system — never ad-hoc colored spans:

| Status    | Classes                                                           |
| --------- | ----------------------------------------------------------------- |
| `RUNNING` | `bg-emerald-500/20 text-emerald-400 border border-emerald-500/30` |
| `STOPPED` | `bg-red-500/20 text-red-400 border border-red-500/30`             |
| `PENDING` | `bg-amber-500/20 text-amber-400 border border-amber-500/30`       |
| `WARNING` | `bg-orange-500/20 text-orange-400 border border-orange-500/30`    |
| `UNKNOWN` | `bg-slate-500/20 text-slate-400 border border-slate-500/30`       |

```vue
<!-- StatusBadge.vue -->
<span
    :class="badgeClass"
    class="text-[10px] font-bold tracking-widest px-2 py-0.5 rounded-full uppercase"
>
  {{ status }}
</span>
```

---

## Metric Cards (Intelligence / Dashboard)

```vue
<div class="bg-surface dark:bg-slate-800/60 rounded-xl border border-slate-700/50 p-6">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-10 h-10 rounded-lg bg-slate-700/60 flex items-center justify-center">
      <component :is="icon" class="w-5 h-5 text-slate-300" />
    </div>
    <span class="text-[11px] font-semibold tracking-widest text-slate-400 uppercase">
      {{ label }}
    </span>
  </div>
  <div class="text-4xl font-bold text-white">
    {{ value }}<span class="text-xl text-slate-400 ml-1">{{ unit }}</span>
  </div>
  <div class="mt-2 text-xs" :class="trend > 0 ? 'text-emerald-400' : 'text-red-400'">
    {{ trend > 0 ? '↑' : '↓' }} {{ Math.abs(trend) }}% vs last period
  </div>
</div>
```

---

## Status Cards (System Health style)

```vue
<div class="bg-surface dark:bg-slate-800/60 rounded-xl border border-slate-700/50 p-6">
  <div class="flex items-center justify-between mb-1">
    <span class="text-[11px] font-semibold tracking-widest text-slate-400 uppercase">
      {{ title }}
    </span>
    <StatusBadge :status="status" />
  </div>
  <div class="text-2xl font-bold text-white mt-2">{{ value }}</div>
  <div class="mt-4 h-1 rounded-full bg-slate-700">
    <div class="h-1 rounded-full bg-emerald-400 transition-all" :style="{ width: progressPct + '%' }" />
  </div>
</div>
```

---

## Section Panels

Used to group related content (e.g., "Maintenance Actions", "Standard Weekly Schedule"):

```vue
<div
    class="bg-surface dark:bg-slate-800/40 rounded-xl border border-slate-700/40 p-6"
>
  <h2 class="text-[11px] font-semibold tracking-widest text-slate-400 uppercase mb-4">
    {{ sectionTitle }}
  </h2>
  <slot />
</div>
```

---

## Buttons

```html
<!-- Primary -->
<button
    class="bg-teal-500 hover:bg-teal-400 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2"
>
    + Deploy New Rule
</button>

<!-- Secondary -->
<button
    class="bg-slate-700 hover:bg-slate-600 text-slate-200 text-sm font-semibold px-4 py-2 rounded-lg transition-colors"
>
    Configure
</button>

<!-- Danger -->
<button
    class="bg-red-500/20 hover:bg-red-500/30 text-red-400 text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-red-500/30"
>
    Delete
</button>

<!-- Ghost/Action (Maintenance Actions style) -->
<button
    class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-medium px-5 py-3 rounded-lg transition-colors border border-slate-700/60 flex items-center gap-2 w-full justify-center"
>
    🔥 Clear Cache
</button>
```

---

## Form Fields

```html
<!-- Text Input -->
<div class="space-y-1">
    <label class="text-sm font-medium text-slate-300">{{ label }}</label>
    <p class="text-xs text-slate-500">{{ description }}</p>
    <input
        type="text"
        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/50 focus:border-teal-500"
    />
</div>

<!-- Number Input with unit label -->
<div class="relative">
    <input
        type="number"
        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-white pr-16 ..."
    />
    <span
        class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-500 tracking-widest"
        >NUM</span
    >
</div>

<!-- Toggle -->
<button
    :class="enabled ? 'bg-teal-500' : 'bg-slate-700'"
    class="relative w-11 h-6 rounded-full transition-colors focus:outline-none"
    @click="enabled = !enabled"
>
    <span
        :class="enabled ? 'translate-x-5' : 'translate-x-1'"
        class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"
    />
</button>
```

---

## Time Picker (Business Hours style)

```html
<div class="flex items-center gap-2">
    <div
        class="flex items-center gap-1 bg-slate-900 border border-slate-700 rounded-lg px-3 py-2"
    >
        <span class="text-sm text-white font-mono">09:00</span>
        <span class="text-xs text-slate-400 ml-1">AM</span>
        <button class="ml-2 text-slate-500 hover:text-slate-300">⊙</button>
    </div>
    <span class="text-slate-600">—</span>
    <div
        class="flex items-center gap-1 bg-slate-900 border border-slate-700 rounded-lg px-3 py-2"
    >
        <span class="text-sm text-white font-mono">05:00</span>
        <span class="text-xs text-slate-400 ml-1">PM</span>
        <button class="ml-2 text-slate-500 hover:text-slate-300">⊙</button>
    </div>
</div>
```

---

## Period Selector Tabs (Intelligence page)

```html
<div class="flex bg-slate-800 rounded-lg p-1 gap-1">
    <button
        v-for="period in ['7D', '30D', '90D']"
        :key="period"
        :class="activePeriod === period
      ? 'bg-teal-500 text-white'
      : 'text-slate-400 hover:text-white'"
        class="text-xs font-bold px-3 py-1.5 rounded-md transition-colors"
        @click="activePeriod = period"
    >
        {{ period }}
    </button>
</div>
```

---

## Automation Rule Card

```html
<div class="bg-slate-800/60 border border-slate-700/60 rounded-xl p-5">
    <div class="flex items-start justify-between mb-3">
        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-white font-semibold">{{ rule.name }}</h3>
                <StatusBadge :status="rule.status" />
            </div>
            <p class="text-sm text-slate-400 mt-0.5">{{ rule.description }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="...secondary button...">✏️ Configure</button>
            <button class="...danger icon button...">🗑</button>
        </div>
    </div>
    <!-- Pipeline visualization -->
    <div class="flex items-center gap-2 mt-4">
        <span
            class="bg-purple-500/20 text-purple-400 border border-purple-500/30 text-[10px] font-bold px-2 py-0.5 rounded uppercase"
            >TRIGGER</span
        >
        <span class="text-slate-500 text-xs font-mono">{{ rule.trigger }}</span>
        <span class="text-slate-600">▶▶</span>
        <span
            class="bg-teal-500/20 text-teal-400 border border-teal-500/30 text-[10px] font-bold px-2 py-0.5 rounded uppercase"
            >✔ Satisfied Conditions</span
        >
        <span class="text-slate-600">▶▶</span>
        <span
            class="bg-blue-500/20 text-blue-400 border border-blue-500/30 text-[10px] font-bold px-2 py-0.5 rounded uppercase"
            >THEN Exec Action Batch</span
        >
    </div>
</div>
```

---

## Tailwind Config Additions

Add to `tailwind.config.js` to support semantic tokens:

```js
theme: {
  extend: {
    colors: {
      'bg': {
        DEFAULT: '#f0f2f7',
        dark: '#0f1623',
      },
      'surface': {
        DEFAULT: '#ffffff',
        dark: '#1a2235',
      },
      'surface-raised': {
        DEFAULT: '#e8ebf2',
        dark: '#212d42',
      },
      'border': {
        DEFAULT: '#d4d8e8',
        dark: 'rgba(255,255,255,0.08)',
      },
      'content-primary': {
        DEFAULT: '#1a2035',
        dark: '#ffffff',
      },
      'content-secondary': {
        DEFAULT: '#5a6480',
        dark: '#94a3b8',
      },
    }
  }
}
```

---

## Vue Component Naming Conventions

| Type                 | Convention                     | Example                           |
| -------------------- | ------------------------------ | --------------------------------- |
| Page views           | `PascalCase` + `View` suffix   | `SystemHealthView.vue`            |
| Shared UI components | `PascalCase` + `Admin` prefix  | `AdminCard.vue`, `AdminBadge.vue` |
| Layout components    | `PascalCase` + `Layout` suffix | `AdminPanelLayout.vue`            |
| Composables          | `camelCase` + `use` prefix     | `useSystemHealth.ts`              |

---

## Further References

- `references/tokens.md` — Complete color token system with light/dark values
- `references/components.md` — Full component API reference for all shared components
- `references/page-template.md` — Boilerplate Vue SFC for new admin pages
- `references/icons.md` — Icon usage guide (which icons map to which nav items / actions)

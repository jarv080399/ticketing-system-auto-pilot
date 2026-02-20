---
name: "Vue.js + Tailwind Design System (PICKUP)"
description: "Guidelines and instructions for implementing the PICKUP Logistics & Ticketing Platform UI using Vue.js and Tailwind CSS."
---

# PICKUP — Vue.js + Tailwind Design System

> Logistics & Ticketing Platform UI Guide

---

## 1. Tailwind Config (`tailwind.config.js`)

```js
const { fontFamily } = require("tailwindcss/defaultTheme");

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: "class",
    content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"DM Sans"', ...fontFamily.sans],
                display: ['"Space Grotesk"', ...fontFamily.sans],
                mono: ['"JetBrains Mono"', ...fontFamily.mono],
            },
            colors: {
                // Brand Blues (primary from UI)
                brand: {
                    50: "#eff6ff",
                    100: "#dbeafe",
                    200: "#bfdbfe",
                    300: "#93c5fd",
                    400: "#60a5fa",
                    500: "#3b82f6",
                    600: "#2563eb", // primary action
                    700: "#1d4ed8", // hover
                    800: "#1e3a8a", // sidebar bg dark
                    900: "#1e2d6b", // hero dark bg
                    950: "#0f1b4d",
                },
                // Status colors
                status: {
                    pending: "#f59e0b",
                    active: "#10b981",
                    cancelled: "#ef4444",
                    transit: "#3b82f6",
                    delivered: "#059669",
                },
                // Neutral (light/dark aware)
                surface: {
                    light: "#ffffff",
                    muted: "#f8fafc",
                    border: "#e2e8f0",
                    dark: "#0f172a",
                    card: "#1e293b",
                },
            },
            borderRadius: {
                ui: "8px",
                card: "12px",
                modal: "16px",
            },
            spacing: {
                sidebar: "240px",
                topbar: "56px",
                panel: "320px",
            },
            boxShadow: {
                card: "0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.06)",
                "card-md":
                    "0 4px 6px -1px rgb(0 0 0 / 0.08), 0 2px 4px -2px rgb(0 0 0 / 0.08)",
                modal: "0 20px 60px -10px rgb(0 0 0 / 0.3)",
                sidebar: "2px 0 8px 0 rgb(0 0 0 / 0.08)",
            },
            fontSize: {
                "2xs": ["0.625rem", { lineHeight: "0.875rem" }],
            },
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
    ],
};
```

---

## 2. CSS Variables & Theming (`src/assets/main.css`)

```css
@import url("https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap");

@tailwind base;
@tailwind components;
@tailwind utilities;

/* ── Light Mode ─────────────────────────────────── */
:root {
    --color-bg: #f8fafc;
    --color-surface: #ffffff;
    --color-surface-2: #f1f5f9;
    --color-border: #e2e8f0;
    --color-border-focus: #3b82f6;
    --color-text: #0f172a;
    --color-text-muted: #64748b;
    --color-text-subtle: #94a3b8;
    --color-primary: #2563eb;
    --color-primary-hover: #1d4ed8;
    --color-primary-light: #eff6ff;
    --color-sidebar-bg: #ffffff;
    --color-sidebar-text: #334155;
    --color-sidebar-icon: #64748b;
    --color-sidebar-active-bg: #eff6ff;
    --color-sidebar-active-text: #2563eb;
    --color-topbar-bg: #ffffff;
    --shadow-card: 0 1px 3px rgb(0 0 0 / 0.06);
}

/* ── Dark Mode ──────────────────────────────────── */
.dark {
    --color-bg: #0f172a;
    --color-surface: #1e293b;
    --color-surface-2: #263349;
    --color-border: #334155;
    --color-border-focus: #3b82f6;
    --color-text: #f1f5f9;
    --color-text-muted: #94a3b8;
    --color-text-subtle: #64748b;
    --color-primary: #3b82f6;
    --color-primary-hover: #60a5fa;
    --color-primary-light: #1e3a5f;
    --color-sidebar-bg: #1e293b;
    --color-sidebar-text: #cbd5e1;
    --color-sidebar-icon: #64748b;
    --color-sidebar-active-bg: #1e3a5f;
    --color-sidebar-active-text: #60a5fa;
    --color-topbar-bg: #1e293b;
    --shadow-card: 0 1px 3px rgb(0 0 0 / 0.3);
}

/* ── Base ───────────────────────────────────────── */
body {
    background-color: var(--color-bg);
    color: var(--color-text);
    font-family: "DM Sans", sans-serif;
    font-size: 14px;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
}

/* ── Utility Classes ────────────────────────────── */
@layer components {
    /* Cards */
    .card {
        @apply rounded-card bg-[var(--color-surface)] border border-[var(--color-border)] shadow-card p-4;
    }

    /* Sidebar */
    .sidebar {
        @apply fixed left-0 top-0 h-screen w-[240px] bg-[var(--color-sidebar-bg)]
           border-r border-[var(--color-border)] flex flex-col z-30 shadow-sidebar;
    }

    /* Topbar */
    .topbar {
        @apply fixed top-0 left-[240px] right-0 h-14 bg-[var(--color-topbar-bg)]
           border-b border-[var(--color-border)] flex items-center px-6 z-20 gap-4;
    }

    /* Main content area */
    .main-content {
        @apply ml-[240px] mt-14 p-6 min-h-screen bg-[var(--color-bg)];
    }

    /* Page header */
    .page-header {
        @apply flex items-center justify-between mb-6 pb-4 border-b border-[var(--color-border)];
    }

    .page-title {
        @apply font-display text-lg font-semibold text-[var(--color-text)];
    }

    .page-subtitle {
        @apply text-xs text-[var(--color-text-muted)] mt-0.5;
    }
}
```

---

## 3. Typography Scale

| Use             | Class                                    | Size | Weight |
| --------------- | ---------------------------------------- | ---- | ------ |
| Page title      | `font-display text-lg font-semibold`     | 18px | 600    |
| Section heading | `font-display text-base font-medium`     | 16px | 500    |
| Card title      | `text-sm font-semibold`                  | 14px | 600    |
| Body / row text | `text-sm font-normal`                    | 14px | 400    |
| Label / caption | `text-xs text-[var(--color-text-muted)]` | 12px | 400    |
| Micro / badge   | `text-2xs font-medium`                   | 10px | 500    |
| ID / code       | `font-mono text-xs`                      | 12px | 400    |

---

## 4. Buttons (`components/ui/AppButton.vue`)

```vue
<template>
    <button :class="classes" :disabled="disabled || loading" v-bind="$attrs">
        <span
            v-if="loading"
            class="mr-1.5 h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-t-transparent"
        />
        <slot />
    </button>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    variant: { type: String, default: "primary" },
    size: { type: String, default: "md" },
    disabled: Boolean,
    loading: Boolean,
});

const base =
    "inline-flex items-center justify-center font-medium rounded-ui transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed select-none";

const variants = {
    primary:
        "bg-[var(--color-primary)] text-white hover:bg-[var(--color-primary-hover)] focus:ring-blue-500",
    secondary:
        "bg-[var(--color-surface-2)] text-[var(--color-text)] border border-[var(--color-border)] hover:bg-[var(--color-border)] focus:ring-slate-400",
    ghost: "text-[var(--color-text-muted)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-text)] focus:ring-slate-400",
    danger: "bg-red-500 text-white hover:bg-red-600 focus:ring-red-500",
    success:
        "bg-emerald-500 text-white hover:bg-emerald-600 focus:ring-emerald-500",
    outline:
        "border border-[var(--color-primary)] text-[var(--color-primary)] hover:bg-[var(--color-primary-light)] focus:ring-blue-500",
};

const sizes = {
    xs: "h-6 px-2.5 text-2xs gap-1",
    sm: "h-7 px-3 text-xs gap-1.5",
    md: "h-8 px-4 text-xs gap-2",
    lg: "h-10 px-5 text-sm gap-2",
};

const classes = computed(() => [
    base,
    variants[props.variant],
    sizes[props.size],
]);
</script>
```

### Button usage examples

```vue
<AppButton>Book Load</AppButton>
<AppButton variant="secondary">Cancel</AppButton>
<AppButton variant="ghost" size="sm">View Details</AppButton>
<AppButton variant="danger" size="sm">Delete</AppButton>
<AppButton variant="outline">Export</AppButton>
<AppButton :loading="true">Saving...</AppButton>
```

---

## 5. Status Badges (`components/ui/StatusBadge.vue`)

```vue
<template>
    <span
        :class="[
            'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-2xs font-medium',
            colorClass,
        ]"
    >
        <span class="h-1.5 w-1.5 rounded-full bg-current" />
        {{ label }}
    </span>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    status: { type: String, required: true },
    // 'pending' | 'active' | 'in-transit' | 'delivered' | 'cancelled' | 'completed'
});

const map = {
    pending: {
        label: "Pending",
        color: "bg-amber-50  text-amber-600  dark:bg-amber-900/30  dark:text-amber-400",
    },
    active: {
        label: "Active",
        color: "bg-blue-50   text-blue-600   dark:bg-blue-900/30   dark:text-blue-400",
    },
    "in-transit": {
        label: "In Transit",
        color: "bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400",
    },
    delivered: {
        label: "Delivered",
        color: "bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400",
    },
    cancelled: {
        label: "Cancelled",
        color: "bg-red-50    text-red-600    dark:bg-red-900/30    dark:text-red-400",
    },
    completed: {
        label: "Completed",
        color: "bg-slate-100 text-slate-600  dark:bg-slate-700     dark:text-slate-300",
    },
};

const { label, color } = computed(() => map[props.status] ?? map.pending).value;
const colorClass = color;
</script>
```

---

## 6. Form Inputs (`components/ui/AppInput.vue`)

```vue
<template>
    <div class="flex flex-col gap-1">
        <label
            v-if="label"
            class="text-xs font-medium text-[var(--color-text-muted)]"
        >
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>
        <div class="relative">
            <span
                v-if="$slots.prefix"
                class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[var(--color-text-subtle)]"
            >
                <slot name="prefix" />
            </span>
            <input
                v-bind="$attrs"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :class="[
                    'w-full rounded-ui border bg-[var(--color-surface)] text-sm text-[var(--color-text)]',
                    'placeholder:text-[var(--color-text-subtle)]',
                    'transition-colors duration-150',
                    'focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                    'disabled:opacity-50 disabled:cursor-not-allowed',
                    error
                        ? 'border-red-400'
                        : 'border-[var(--color-border)] hover:border-slate-300 dark:hover:border-slate-500',
                    $slots.prefix ? 'pl-8' : 'pl-3',
                    $slots.suffix ? 'pr-8' : 'pr-3',
                    sizes[size],
                ]"
            />
            <span
                v-if="$slots.suffix"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[var(--color-text-subtle)]"
            >
                <slot name="suffix" />
            </span>
        </div>
        <p v-if="error" class="text-2xs text-red-500">{{ error }}</p>
        <p v-else-if="hint" class="text-2xs text-[var(--color-text-subtle)]">
            {{ hint }}
        </p>
    </div>
</template>

<script setup>
defineProps({
    modelValue: [String, Number],
    label: String,
    hint: String,
    error: String,
    required: Boolean,
    size: { type: String, default: "md" },
});
defineEmits(["update:modelValue"]);

const sizes = {
    sm: "h-7 text-xs",
    md: "h-8",
    lg: "h-10",
};
</script>
```

---

## 7. Data Table (`components/ui/DataTable.vue`)

```vue
<template>
    <div class="card overflow-hidden p-0">
        <!-- Table header toolbar -->
        <div
            class="flex items-center justify-between px-4 py-3 border-b border-[var(--color-border)]"
        >
            <h3
                class="font-display text-sm font-semibold text-[var(--color-text)]"
            >
                <slot name="title" />
            </h3>
            <div class="flex items-center gap-2">
                <slot name="toolbar" />
            </div>
        </div>

        <!-- Scrollable table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr
                        class="border-b border-[var(--color-border)] bg-[var(--color-surface-2)]"
                    >
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            class="px-4 py-2.5 text-left text-2xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)]"
                            :style="col.width ? `width: ${col.width}` : ''"
                        >
                            {{ col.label }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)]">
                    <tr
                        v-for="(row, i) in rows"
                        :key="i"
                        class="hover:bg-[var(--color-surface-2)] transition-colors cursor-pointer group"
                        @click="$emit('row-click', row)"
                    >
                        <td
                            v-for="col in columns"
                            :key="col.key"
                            class="px-4 py-2.5 text-[var(--color-text)]"
                        >
                            <slot
                                :name="`cell-${col.key}`"
                                :row="row"
                                :value="row[col.key]"
                            >
                                {{ row[col.key] }}
                            </slot>
                        </td>
                    </tr>
                    <tr v-if="!rows.length">
                        <td
                            :colspan="columns.length"
                            class="px-4 py-10 text-center text-sm text-[var(--color-text-muted)]"
                        >
                            No records found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="totalPages > 1"
            class="flex items-center justify-between px-4 py-3 border-t border-[var(--color-border)]"
        >
            <p class="text-xs text-[var(--color-text-muted)]">
                Showing {{ (page - 1) * perPage + 1 }}–{{
                    Math.min(page * perPage, total)
                }}
                of {{ total }}
            </p>
            <div class="flex gap-1">
                <AppButton
                    variant="ghost"
                    size="sm"
                    :disabled="page === 1"
                    @click="$emit('update:page', page - 1)"
                    >←</AppButton
                >
                <AppButton
                    variant="ghost"
                    size="sm"
                    :disabled="page === totalPages"
                    @click="$emit('update:page', page + 1)"
                    >→</AppButton
                >
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import AppButton from "./AppButton.vue";

const props = defineProps({
    columns: { type: Array, default: () => [] },
    rows: { type: Array, default: () => [] },
    total: { type: Number, default: 0 },
    page: { type: Number, default: 1 },
    perPage: { type: Number, default: 20 },
});
defineEmits(["row-click", "update:page"]);

const totalPages = computed(() => Math.ceil(props.total / props.perPage));
</script>
```

---

## 8. Sidebar (`components/layout/AppSidebar.vue`)

```vue
<template>
    <aside class="sidebar">
        <!-- Logo -->
        <div
            class="flex h-14 shrink-0 items-center px-5 border-b border-[var(--color-border)]"
        >
            <span
                class="font-display text-base font-bold text-[var(--color-primary)]"
                >PICKUP</span
            >
        </div>

        <!-- Nav -->
        <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-0.5">
            <RouterLink
                v-for="item in navItems"
                :key="item.to"
                :to="item.to"
                custom
                v-slot="{ isActive, navigate }"
            >
                <button
                    @click="navigate"
                    :class="[
                        'w-full flex items-center gap-3 px-3 py-2 rounded-ui text-sm transition-colors duration-100',
                        isActive
                            ? 'bg-[var(--color-sidebar-active-bg)] text-[var(--color-sidebar-active-text)] font-medium'
                            : 'text-[var(--color-sidebar-text)] hover:bg-[var(--color-surface-2)]',
                    ]"
                >
                    <component :is="item.icon" class="h-4 w-4 shrink-0" />
                    {{ item.label }}
                    <span
                        v-if="item.badge"
                        class="ml-auto rounded-full bg-[var(--color-primary)] px-1.5 py-0.5 text-2xs font-medium text-white"
                    >
                        {{ item.badge }}
                    </span>
                </button>
            </RouterLink>
        </nav>

        <!-- User -->
        <div class="shrink-0 border-t border-[var(--color-border)] p-3">
            <div
                class="flex items-center gap-3 rounded-ui px-2 py-2 hover:bg-[var(--color-surface-2)] cursor-pointer"
            >
                <img
                    :src="user.avatar"
                    class="h-7 w-7 rounded-full object-cover"
                />
                <div class="min-w-0 flex-1">
                    <p
                        class="truncate text-xs font-medium text-[var(--color-text)]"
                    >
                        {{ user.name }}
                    </p>
                    <p class="truncate text-2xs text-[var(--color-text-muted)]">
                        {{ user.role }}
                    </p>
                </div>
            </div>
        </div>
    </aside>
</template>

<script setup>
defineProps({
    navItems: Array,
    user: Object,
});
</script>
```

---

## 9. Topbar with Dark Mode Toggle (`components/layout/AppTopbar.vue`)

```vue
<template>
    <header class="topbar">
        <!-- Breadcrumb -->
        <div
            class="flex items-center gap-2 text-xs text-[var(--color-text-muted)]"
        >
            <span
                v-for="(crumb, i) in breadcrumbs"
                :key="i"
                class="flex items-center gap-2"
            >
                <span
                    :class="
                        i === breadcrumbs.length - 1
                            ? 'text-[var(--color-text)] font-medium'
                            : ''
                    "
                >
                    {{ crumb }}
                </span>
                <span
                    v-if="i < breadcrumbs.length - 1"
                    class="text-[var(--color-text-subtle)]"
                    >/</span
                >
            </span>
        </div>

        <!-- Spacer -->
        <div class="flex-1" />

        <!-- Search -->
        <AppInput placeholder="Search loads..." size="sm" class="w-48">
            <template #prefix>🔍</template>
        </AppInput>

        <!-- Actions -->
        <div class="flex items-center gap-2">
            <!-- Dark mode toggle -->
            <button
                @click="toggleDark"
                class="h-8 w-8 flex items-center justify-center rounded-ui text-[var(--color-text-muted)] hover:bg-[var(--color-surface-2)] transition-colors"
            >
                <span v-if="isDark">☀️</span>
                <span v-else>🌙</span>
            </button>

            <!-- Notifications -->
            <button
                class="relative h-8 w-8 flex items-center justify-center rounded-ui text-[var(--color-text-muted)] hover:bg-[var(--color-surface-2)]"
            >
                🔔
                <span
                    class="absolute top-1 right-1 h-2 w-2 rounded-full bg-red-500"
                />
            </button>

            <!-- Avatar -->
            <img
                :src="user.avatar"
                class="h-7 w-7 rounded-full object-cover cursor-pointer"
            />
        </div>
    </header>
</template>

<script setup>
import { ref } from "vue";
import AppInput from "../ui/AppInput.vue";

const props = defineProps({ breadcrumbs: Array, user: Object });

const isDark = ref(document.documentElement.classList.contains("dark"));

function toggleDark() {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle("dark", isDark.value);
    localStorage.setItem("theme", isDark.value ? "dark" : "light");
}
</script>
```

---

## 10. Detail Panel / Drawer (`components/ui/SlidePanel.vue`)

```vue
<template>
    <Transition name="panel">
        <div v-if="open" class="fixed inset-0 z-50 flex justify-end">
            <!-- Backdrop -->
            <div
                class="absolute inset-0 bg-black/30 backdrop-blur-sm"
                @click="$emit('close')"
            />

            <!-- Panel -->
            <div
                class="relative w-full max-w-[480px] bg-[var(--color-surface)] shadow-modal flex flex-col h-full overflow-hidden"
            >
                <!-- Header -->
                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-[var(--color-border)] shrink-0"
                >
                    <div>
                        <h2
                            class="font-display text-base font-semibold text-[var(--color-text)]"
                        >
                            {{ title }}
                        </h2>
                        <p
                            v-if="subtitle"
                            class="text-xs text-[var(--color-text-muted)] mt-0.5"
                        >
                            {{ subtitle }}
                        </p>
                    </div>
                    <button
                        @click="$emit('close')"
                        class="h-7 w-7 flex items-center justify-center rounded-ui hover:bg-[var(--color-surface-2)]"
                    >
                        ✕
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto px-6 py-4">
                    <slot />
                </div>

                <!-- Footer -->
                <div
                    v-if="$slots.footer"
                    class="shrink-0 border-t border-[var(--color-border)] px-6 py-4 flex gap-2 justify-end"
                >
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
defineProps({ open: Boolean, title: String, subtitle: String });
defineEmits(["close"]);
</script>

<style scoped>
.panel-enter-active,
.panel-leave-active {
    transition: opacity 0.2s ease;
}
.panel-enter-active .relative,
.panel-leave-active .relative {
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.panel-enter-from {
    opacity: 0;
}
.panel-enter-from .relative {
    transform: translateX(100%);
}
.panel-leave-to {
    opacity: 0;
}
.panel-leave-to .relative {
    transform: translateX(100%);
}
</style>
```

---

## 11. Spacing & Layout System

```
Layout
├── Sidebar        240px fixed left
├── Topbar          56px fixed top
└── Main content   padding: 24px (p-6)

Spacing scale (use these only)
  4px   → gap-1, p-1   (tight: icon padding)
  8px   → gap-2, p-2   (compact: badge, chip)
  12px  → gap-3, p-3   (default: button, input)
  16px  → gap-4, p-4   (card internal)
  24px  → gap-6, p-6   (section, page padding)
  32px  → gap-8, p-8   (modal padding)

Card structure
  .card               → border + shadow + rounded-card
  .card > header      → pb-3 mb-3 border-b
  .card > section     → py-3 border-b last:border-0

Grid columns
  Dashboard stats     → grid-cols-4 gap-4
  Load list + detail  → grid-cols-[1fr_320px] gap-0
  Form fields         → grid-cols-2 gap-4
```

---

## 12. Dark Mode Setup (`main.js`)

```js
// Apply saved theme before Vue mounts (prevents flash)
const saved = localStorage.getItem("theme");
const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
if (saved === "dark" || (!saved && prefersDark)) {
    document.documentElement.classList.add("dark");
}

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import "./assets/main.css";

createApp(App).use(router).mount("#app");
```

---

## 13. Complete Example: Load Board Page

```vue
<template>
    <AppLayout>
        <!-- Page header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Load Board</h1>
                <p class="page-subtitle">
                    {{ loads.length }} open loads available
                </p>
            </div>
            <div class="flex gap-2">
                <AppButton variant="secondary" size="sm">Filter</AppButton>
                <AppButton size="sm">+ New Load</AppButton>
            </div>
        </div>

        <!-- Stats row -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div v-for="stat in stats" :key="stat.label" class="card">
                <p class="text-xs text-[var(--color-text-muted)]">
                    {{ stat.label }}
                </p>
                <p
                    class="font-display text-xl font-semibold text-[var(--color-text)] mt-1"
                >
                    {{ stat.value }}
                </p>
                <p
                    :class="[
                        'text-2xs mt-1',
                        stat.up ? 'text-emerald-500' : 'text-red-400',
                    ]"
                >
                    {{ stat.change }}
                </p>
            </div>
        </div>

        <!-- Table -->
        <DataTable
            :columns="columns"
            :rows="loads"
            :total="loads.length"
            @row-click="openLoad"
        >
            <template #title>Active Loads</template>
            <template #toolbar>
                <AppInput
                    v-model="search"
                    placeholder="Search..."
                    size="sm"
                    class="w-44"
                />
            </template>
            <template #cell-status="{ value }">
                <StatusBadge :status="value" />
            </template>
            <template #cell-id="{ value }">
                <span class="font-mono text-xs text-[var(--color-primary)]">{{
                    value
                }}</span>
            </template>
            <template #cell-actions>
                <AppButton variant="ghost" size="xs">View</AppButton>
            </template>
        </DataTable>

        <!-- Slide panel -->
        <SlidePanel
            :open="!!selectedLoad"
            title="Load Details"
            :subtitle="selectedLoad?.id"
            @close="selectedLoad = null"
        >
            <!-- load detail content -->
            <template #footer>
                <AppButton variant="secondary" @click="selectedLoad = null"
                    >Close</AppButton
                >
                <AppButton>Book Load</AppButton>
            </template>
        </SlidePanel>
    </AppLayout>
</template>
```

---

## 14. Font Import (index.html)

```html
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
    rel="stylesheet"
/>
```

---

## 15. Summary Cheat Sheet

| Token               | Value                      |
| ------------------- | -------------------------- |
| Primary font        | DM Sans                    |
| Display/headings    | Space Grotesk              |
| Monospace (IDs)     | JetBrains Mono             |
| Primary blue        | `#2563eb` / `brand-600`    |
| Sidebar width       | 240px                      |
| Topbar height       | 56px                       |
| Card radius         | 12px (`rounded-card`)      |
| Input/button radius | 8px (`rounded-ui`)         |
| Default padding     | 24px (`p-6`)               |
| Card padding        | 16px (`p-4`)               |
| Dark mode           | `.dark` class on `<html>`  |
| Theme persistence   | `localStorage` key `theme` |

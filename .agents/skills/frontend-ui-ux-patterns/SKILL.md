---
name: frontend-ui-ux-patterns
description: Curated collection of top GitHub UI/UX design patterns.
version: 1.0.0
author: Koperasi Bermadani Team
license: MIT
platforms: [linux, macos, windows]
metadata:
  tags: [frontend, ui-patterns, tailwind-v4, alpinejs, bento-grid]
---

# Frontend UI/UX Design Patterns Skill

This skill captures reusable UI/UX patterns derived from top GitHub frontend repositories (Shadcn UI, Tremor, Cruip) tailored for Tailwind CSS v4 and Alpine.js.

---

## 🛠️ Pattern 1: Interactive Split Tab Showcase

Use this pattern when presenting multiple core products in a single section without cluttering the screen.

```html
<div class="bg-zinc-100/70 rounded-3xl p-6 sm:p-10 shadow-sm" x-data="{ activeTab: 'tab1' }">
    <div class="flex flex-col lg:flex-row gap-8 items-stretch w-full">
        <!-- Left Column: Row Selectors (5/12 width) -->
        <div class="w-full lg:w-5/12 flex-shrink-0 flex flex-col justify-center space-y-4">
            <button @click="activeTab = 'tab1'"
                :class="activeTab === 'tab1' ? 'bg-[#155A6B] text-white shadow-md scale-[1.02]' : 'bg-white text-zinc-800 shadow-sm hover:shadow-md'"
                class="w-full text-left p-5 rounded-2xl transition-all duration-300 flex items-center justify-between group">
                <!-- Content -->
            </button>
        </div>

        <!-- Right Column: Dynamic Spotlight (7/12 width) -->
        <div class="w-full lg:w-7/12 flex-grow min-w-0 bg-white rounded-2xl p-6 sm:p-8 flex flex-col justify-between shadow-md">
            <div x-show="activeTab === 'tab1'" x-transition:enter="transition ease-out duration-300">
                <!-- Active Content -->
            </div>
            <div x-show="activeTab === 'tab2'" x-cloak style="display: none;">
                <!-- Hidden Content with x-cloak -->
            </div>
        </div>
    </div>
</div>
```

---

## 🛠️ Pattern 2: 3-Column Step Workflow Cards

Use this pattern for onboarding, supplier submission, or multi-step processes.

```html
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="rounded-3xl bg-white p-6 sm:p-7 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-6 group">
        <div class="space-y-4">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-50 px-3 py-1 rounded-full">
                LANGKAH 01
            </span>
            <h3 class="text-xl font-bold text-zinc-900 tracking-tight">Step Title</h3>
            <p class="text-xs text-zinc-600 font-medium leading-relaxed">Step description text goes here.</p>
        </div>
        <!-- Clean Visual Frame Placeholder -->
        <div class="w-full aspect-[4/3] rounded-2xl bg-gradient-to-br from-amber-50/50 via-zinc-50 to-orange-50/30 border border-amber-100/60 flex flex-col items-center justify-center text-center p-6 relative overflow-hidden group-hover:scale-[1.02] transition-transform duration-500">
            <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-amber-600 text-xl mb-2">
                <i class='bx bx-id-card'></i>
            </div>
            <span class="text-xs font-bold text-zinc-700">Frame Label</span>
        </div>
    </div>
</div>
```

---

## 🚨 Tailwind CSS v4 & Alpine.js Critical Guidelines

1. **Prevent Column Squeezing**: Always specify explicit flexbox/grid widths (`w-full lg:w-5/12` and `w-full lg:w-7/12 min-w-0`) when nesting flex layouts inside Tailwind v4.
2. **Prevent Layout Flickering**: Always add `x-cloak` and `style="display: none;"` to hidden Alpine.js tab elements.

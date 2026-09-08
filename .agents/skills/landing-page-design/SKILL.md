---
name: landing-page-design
description: Master skill for modern, anti-slop landing page UI/UX design.
version: 1.0.0
author: Koperasi Bermadani Team
license: MIT
platforms: [linux, macos, windows]
metadata:
  tags: [landing-page, ui-ux, design-system, tailwind, alpinejs]
---

# Master Landing Page Design & UI/UX Skill

This skill provides comprehensive instructions for designing world-class, modern landing pages based on top GitHub repositories (Cruip, Shadcn UI, Tremor, Linear).

---

## Core UI/UX Design Principles

### 1. Borderless Depth & Soft Shadows
* **NO Harsh 1px Black Outlines**: Never use `border-zinc-200` or `border-black` outline borders around feature cards, badges, or image containers.
* **Soft Depth Layers**: Use subtle background contrasts (`bg-zinc-100/70` on parent container, `bg-white` on card tiles) combined with soft shadow transitions (`shadow-sm hover:shadow-xl duration-300`).

### 2. High-Impact Typography & Spacing
* **Font Stack**: Clean sans-serif hierarchy (System Apple, Inter, Outfit, Geist).
* **Headlines**: Extra bold tracking-tight titles (`font-extrabold tracking-tight text-zinc-900`).
* **Section Padding**: Generous vertical spacing (`py-16 sm:py-24`) to maintain high-end whitespace.

### 3. Handling "Heavy" Multi-Product Features
When an application has multiple complex products (e.g. Retail POS, Sharia Savings, Dividends, Supplier Consignment):
* **Do NOT Cram All Features into 1 Flat Grid**: Avoid packing 5+ heavy products into a single static 3-column card grid with dense paragraphs.
* **Use Interactive Split Toggles**: Implement horizontal or vertical row selectors using Alpine.js (`x-data="{ activeTab: '...' }"`) so users focus on 1 spotlight product at a time.
* **Use Clean Visual Frame Shells**: When high-quality screenshots aren't available, use styled visual frame placeholders with soft icon gradients and subtle labels rather than low-quality AI images.

---

## Standard Landing Page Section Architecture

1. **Header / Navbar**: Sticky top bar, logo, high-contrast nav items, login CTA button.
2. **Hero Showcase**: Main headline, subheadline, double CTA buttons, background texture or app frame shell.
3. **Core Features / Products**: Interactive split showcase (left row selectors, right spotlight card with metric pills and visual display).
4. **Step-by-Step Workflow (e.g. Supplier / Partner)**: 3-column vertical step cards with numbered badges and aspect ratio frames.
5. **Strategic Advantages / Metrics**: Metric grid cards highlighting key data points (members, volume, security).
6. **Accordion FAQ**: Clean expand/collapse FAQ using Alpine.js (`x-show="open"`).
7. **Footer**: Quick links, legal notices, copyright, brand logo.

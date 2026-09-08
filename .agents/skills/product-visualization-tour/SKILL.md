---
name: product-visualization-tour
description: Skill for effectively visualizing multi-pillar products on landing pages.
version: 1.0.0
author: Koperasi Bermadani Team
license: MIT
platforms: [linux, macos, windows]
metadata:
  tags: [product-visualization, ui-ux, interactive-tour, bento-grid, multi-product]
---

# Product Visualization & Ecosystem Tour Skill

This skill defines the strategic methodologies for visualizing complex multi-pillar products on a single landing page without overwhelming the user or creating layout clutter.

---

## 🎯 The Core Problem & Solution

### Problem: Heavy Feature Overhead
When an ecosystem has multiple heavy pillars (e.g. Retail POS, Sharia Savings Ledger, Annual SHU Dividends, UMKM Supplier Consignment, Member Digital QR Card), putting them all in static text cards creates visual fatigue and low engagement.

### Solution: The 3-Tier Visual Strategy

1. **Audience-Based Filter Toggles (Segmenting Intent)**:
   * Provide a top filter switcher at the section header: `[Semua Fitur]` | `[Anggota Kampus]` | `[Supplier & UMKM]`.
   * This immediately hides irrelevant information and reduces cognitive load by 50%.

2. **Interactive Micro-UI Product Spotlights (Pure CSS/Blade Mockups)**:
   * Instead of raster JPEG/PNG images, build **Pure CSS Micro-UI Mockup Cards** (e.g. an interactive mini POS receipt, a live balance ledger card, or a digital QR ID card).
   * Pure CSS mockups load instantly, scale crisp on Retina screens, and allow live interactions.

3. **Problem vs. Solution Comparative Cards**:
   * Highlight the core differential advantage of each product against conventional alternatives (e.g. "Bank Biasa: Biaya Admin Rp 15rb/bln" vs "Bermadani: Rp 0 Admin Siluman").

---

## 🏗️ Visualization Layout Models

### Model A: Interactive Segmented Product Matrix (Recommended)
* **Header**: Section title + Segment filter buttons (`All`, `Member`, `Supplier`).
* **Main Area**: 2-Column Split View:
  * **Left (5/12)**: Vertical feature selection list with micro-badges, icons, and short descriptions.
  * **Right (7/12)**: Full-featured spotlight container containing live interactive micro-UI widget + key metrics pills.

### Model B: Interactive Bento Grid with Micro-Widgets
* **Grid Layout**: Asymmetric 5-tile bento layout (1 Hero 7-col tile, 1 Medium 5-col tile, 3 Equal 4-col tiles).
* **Tile Composition**:
  * **Top 60%**: Pure CSS UI Mockup Widget (e.g. mini chart, mini QR card, mini POS list).
  * **Bottom 40%**: High-contrast title, concise caption, and action link.

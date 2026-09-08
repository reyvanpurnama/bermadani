---
name: micro-ui-mockups
description: Pure CSS & Tailwind micro-UI mockup component templates.
version: 1.0.0
author: Koperasi Bermadani Team
license: MIT
platforms: [linux, macos, windows]
metadata:
  tags: [micro-ui, css-mockups, tailwind-v4, blade, component-templates]
---

# Pure CSS Micro-UI Mockup Templates Skill

This skill provides ready-to-use pure CSS/Tailwind component mockups for visualizing complex products (POS, Simpanan, SHU, QR Cards, Supplier Dashboard) directly in HTML/Blade code without relying on external image files.

---

## 🎨 Micro-UI Mockup Components

### 1. Digital Member QR Card Mockup (Kartu Anggota Digital)
```html
<div class="w-full p-5 rounded-2xl bg-gradient-to-br from-[#155A6B] to-teal-900 text-white shadow-lg relative overflow-hidden">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-xs font-black">B</div>
            <span class="text-xs font-bold tracking-wider uppercase opacity-90">Koperasi Bermadani</span>
        </div>
        <span class="text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded-full border border-emerald-400/30">ANGGOTA AKTIF</span>
    </div>
    <div class="flex items-end justify-between">
        <div>
            <p class="text-[10px] uppercase text-teal-200 font-medium">Nama Anggota</p>
            <p class="text-sm font-bold tracking-tight text-white">Ahmad Fauzi</p>
            <p class="text-[10px] text-teal-200 mt-0.5">NIM: 220104089</p>
        </div>
        <div class="w-12 h-12 bg-white rounded-xl p-1.5 flex items-center justify-center shadow-inner">
            <i class='bx bx-qr-scan text-3xl text-zinc-900'></i>
        </div>
    </div>
</div>
```

### 2. POS Digital Slip & Point Receipt (Bermadani Mart POS)
```html
<div class="w-full p-4 rounded-2xl bg-white border border-zinc-100 shadow-md space-y-3">
    <div class="flex items-center justify-between text-xs pb-2 border-b border-zinc-100">
        <span class="font-bold text-zinc-800">Nota Belanja #POS-8842</span>
        <span class="text-[10px] text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full font-bold">+45 Poin SHU</span>
    </div>
    <div class="space-y-1.5 text-xs text-zinc-600">
        <div class="flex justify-between">
            <span>Air Mineral 600ml (x2)</span>
            <span class="font-semibold text-zinc-800">Rp 6.000</span>
        </div>
        <div class="flex justify-between">
            <span>Roti Bakery Kampus</span>
            <span class="font-semibold text-zinc-800">Rp 8.500</span>
        </div>
    </div>
    <div class="pt-2 border-t border-dashed border-zinc-200 flex justify-between items-center text-xs">
        <span class="font-bold text-zinc-900">Total Transaksi</span>
        <span class="font-extrabold text-[#155A6B] text-sm">Rp 14.500</span>
    </div>
</div>
```

### 3. Sharia Ledger Balance & 0% Admin Badge (Simpanan Syariah)
```html
<div class="w-full p-4 rounded-2xl bg-gradient-to-r from-teal-50 to-emerald-50/50 border border-teal-100/80 shadow-sm space-y-3">
    <div class="flex items-center justify-between">
        <span class="text-[10px] font-extrabold uppercase text-[#155A6B] bg-white px-2.5 py-0.5 rounded-full shadow-2xs">Saldo Simpanan</span>
        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/60 px-2 py-0.5 rounded-full">Akad Wadi'ah</span>
    </div>
    <div>
        <p class="text-xl font-extrabold text-zinc-900 tracking-tight">Rp 1.450.000</p>
        <p class="text-[10px] text-zinc-500 mt-0.5">Simpanan Wajib + Sukarela</p>
    </div>
    <div class="flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 pt-1 border-t border-teal-100">
        <i class='bx bx-check-shield text-base'></i>
        <span>Rp 0 Biaya Administrasi Bulanan</span>
    </div>
</div>
```

### 4. Annual SHU Dividends Return Widget (Dividen SHU)
```html
<div class="w-full p-4 rounded-2xl bg-white border border-purple-100 shadow-md space-y-3">
    <div class="flex items-center justify-between">
        <span class="text-[10px] font-extrabold uppercase text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-full">Proyeksi SHU 2026</span>
        <span class="text-xs font-bold text-purple-700">Tahun Buku 2025</span>
    </div>
    <div class="space-y-2">
        <div class="flex justify-between text-xs">
            <span class="text-zinc-600">Dividen Belanja Toko</span>
            <span class="font-bold text-zinc-800">Rp 320.000</span>
        </div>
        <div class="w-full bg-zinc-100 h-2 rounded-full overflow-hidden">
            <div class="bg-purple-600 h-full rounded-full" style="width: 75%;"></div>
        </div>
    </div>
</div>
```

# UI Branding — Always On (glob: resources/**, *.blade.php)

Visual identity is inspired by **Npontu Technologies** (npontu.com) — a Ghanaian enterprise tech firm. The design language is confident, corporate-tech, and professional: deep navy as the dominant brand color, warm gold/amber as the action accent, and clean white/light-gray surfaces.

---

## Design Palette

Define in `tailwind.config.js` under `theme.extend.colors` and as CSS custom properties in `resources/css/app.css`. **Never hardcode hex values in Blade files.**

### Color Tokens

| Token | Value | Usage |
|-------|-------|-------|
| `--color-primary` | `#0A2647` | Deep navy — headers, sidebar, primary buttons |
| `--color-primary-dark` | `#071A33` | Hover/active states on primary elements |
| `--color-accent` | `#F2A93B` | Gold/amber — CTAs, active nav item, key metric highlights |
| `--color-accent-dark` | `#D98F1F` | Hover state on accent elements |
| `--color-surface` | `#FFFFFF` | Main content area background |
| `--color-surface-muted` | `#F4F6F8` | Subtle backgrounds, card alternates |
| `--color-text` | `#1E293B` | Primary body text (slate-800 equivalent) |
| `--color-success` | `#1B8A5A` | Available / Paid status |
| `--color-warning` | `#E8871E` | Reserved / Partial payment status |
| `--color-danger` | `#C0392B` | Occupied conflict / Overdue / Error |
| `--color-muted-border` | `#E2E8F0` | Card borders, dividers |

### `tailwind.config.js` Theme Extension

```js
theme: {
  extend: {
    colors: {
      primary: {
        DEFAULT: '#0A2647',
        dark:    '#071A33',
      },
      accent: {
        DEFAULT: '#F2A93B',
        dark:    '#D98F1F',
      },
      surface: {
        DEFAULT: '#FFFFFF',
        muted:   '#F4F6F8',
      },
      hms: {
        text:    '#1E293B',
        success: '#1B8A5A',
        warning: '#E8871E',
        danger:  '#C0392B',
        border:  '#E2E8F0',
      },
    },
    fontFamily: {
      sans: ['Inter', 'system-ui', 'sans-serif'],
    },
  },
},
```

---

## CSS Custom Properties

Add to `resources/css/app.css` (inside a `:root` block):

```css
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

:root {
  --color-primary:      #0A2647;
  --color-primary-dark: #071A33;
  --color-accent:       #F2A93B;
  --color-accent-dark:  #D98F1F;
  --color-surface:      #FFFFFF;
  --color-surface-muted:#F4F6F8;
  --color-text:         #1E293B;
  --color-success:      #1B8A5A;
  --color-warning:      #E8871E;
  --color-danger:       #C0392B;
  --color-muted-border: #E2E8F0;
}
```

---

## Layout Structure — Staff Dashboard

```
┌──────────────────────────────────────────────────┐
│  [#0A2647 SIDEBAR - 256px]   │  [#FFFFFF CONTENT] │
│  Logo                        │                    │
│  Nav item (hover: #F2A93B)   │  Page header       │
│  ● Active (bg: #F2A93B text) │  Cards / Table     │
│  ...                         │  Pagination        │
└──────────────────────────────────────────────────┘
```

- Sidebar: `bg-primary text-white w-64 min-h-screen` (fixed on desktop, drawer on mobile)
- Sidebar logo: HMS logo or hotel name in `text-accent font-bold text-xl`
- Nav items: `text-gray-300 hover:text-white hover:bg-primary-dark px-4 py-3`
- Active nav item: `bg-accent text-primary font-semibold`
- Content area: `flex-1 bg-surface-muted min-h-screen`
- Page header: `bg-surface border-b border-hms-border px-6 py-4`
- Card: `bg-surface rounded-xl shadow-sm border border-hms-border p-6`

---

## Blade Component Standards

### Status Badge Component (`resources/views/components/status-badge.blade.php`)

```blade
@props(['status'])

@php
$classes = match($status) {
    'available', 'paid'      => 'bg-green-100 text-green-800 border-green-200',
    'reserved', 'partial'    => 'bg-amber-100 text-amber-800 border-amber-200',
    'occupied', 'unpaid'     => 'bg-red-100 text-red-800 border-red-200',
    'dirty', 'maintenance'   => 'bg-gray-100 text-gray-600 border-gray-200',
    'cancelled'              => 'bg-red-50 text-red-500 border-red-100',
    'confirmed', 'checked_in'=> 'bg-blue-100 text-blue-800 border-blue-200',
    default                  => 'bg-gray-100 text-gray-600 border-gray-200',
};
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $classes }}">
    {{ ucfirst(str_replace('_', ' ', $status)) }}
</span>
```

### Dashboard Metric Card Component (`resources/views/components/metric-card.blade.php`)

```blade
@props(['label', 'value', 'icon' => null, 'trend' => null, 'color' => 'primary'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
    @if($icon)
    <div class="w-12 h-12 rounded-lg bg-accent/10 flex items-center justify-center text-accent text-xl">
        {!! $icon !!}
    </div>
    @endif
    <div>
        <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
        <p class="text-2xl font-bold text-primary mt-1">{{ $value }}</p>
        @if($trend)
        <p class="text-xs text-gray-400 mt-0.5">{{ $trend }}</p>
        @endif
    </div>
</div>
```

---

## Typography

- Font: **Inter** (via Google Fonts CDN in the main layout `<head>`)
- Base size: `text-sm` (14px) for table data, `text-base` (16px) for body, `text-lg`+ for headings
- Heading hierarchy: `h1` = page title (24px bold), `h2` = section heading (20px semibold), `h3` = card header (16px semibold)

---

## Buttons

| Variant | Tailwind Classes |
|---------|-----------------|
| Primary | `bg-primary hover:bg-primary-dark text-white font-medium px-4 py-2 rounded-lg transition` |
| Accent / CTA | `bg-accent hover:bg-accent-dark text-primary font-semibold px-4 py-2 rounded-lg transition` |
| Outline | `border border-primary text-primary hover:bg-primary hover:text-white px-4 py-2 rounded-lg transition` |
| Danger | `bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition` |
| Ghost | `text-gray-600 hover:text-primary hover:bg-gray-100 px-4 py-2 rounded-lg transition` |

---

## Tables

```blade
<div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-primary">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Column</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-50">
            <tr class="hover:bg-surface-muted transition">
                <td class="px-6 py-4 text-sm text-hms-text">Data</td>
            </tr>
        </tbody>
    </table>
</div>
```

---

## Form Inputs

```blade
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Field Label</label>
    <input type="text"
           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition"
           name="field">
    @error('field')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>
```

---

## Mobile Responsiveness

- Sidebar: hidden on `< md`, shown as slide-over drawer triggered by hamburger button (Alpine.js `x-show`)
- Tables: horizontal scroll on mobile (`overflow-x-auto`)
- Dashboard cards: grid `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4`

---

## Branding Note

> The color values above were derived from Npontu Technologies' public website design language (npontu.com). If the site's branding changes, update `tailwind.config.js` and `app.css` only — never update individual Blade files.

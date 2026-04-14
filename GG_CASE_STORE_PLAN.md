# GG Case Store — Rebuild Plan & Requirements

> A full rebuild and rebrand of the original GGStoreCalatogs project.
> Stack: Laravel 12 + Inertia.js v2 + Vue 3 + Tailwind CSS v3 + shadcn-vue

---

## 1. Project Overview

GG Case Store is a **product catalog web application** for a physical tech accessories and devices shop based in Samarinda, Indonesia. It is not an e-commerce checkout system — it is a **browsable catalog** that directs customers to Tokopedia/Shopee to purchase.

The rebuild goals are:
- Modern, responsive UI that works on mobile, tablet, and desktop
- Clean admin panel for managing products, categories, brands, and banners
- Retain the brand identity (black + gold `#e6b120` color scheme)
- Replace the old vanilla JS + Node.js stack with Laravel + Inertia + Vue 3

---

## 2. Brand Identity

| Token | Value | Usage |
|---|---|---|
| Primary / Gold | `#e6b120` | Buttons, links, accents, badges |
| Primary Hover | `#ffcd29` | Hover states |
| Background | `#ffffff` | Page background |
| Foreground | `#000000` / `#1b1b18` | Body text |
| Muted text | `#66686a` | Secondary text, descriptions |
| Danger | `#ef4444` | Delete actions, errors |

Font: **Inter** (already used in old project), fallback to system-ui.

The current `app.css` uses a neutral shadcn-vue theme. The CSS variables will need to be updated to reflect the gold/black brand palette.

---

## 3. Data Models

Derived from `gg_catalog_db.sql` and old API CRUD files.

### 3.1 Core Tables

```
products
  id, name, description, brand_id, category_id,
  price, promo_price, promo_price_start_date, promo_price_end_date,
  total_sold, avg_rating, total_raters,
  created_at, updated_at

product_photos
  id, product_id, photo_url

product_variants
  id, product_id, variant_name

categories
  id, name, category_photo, created_at

brands
  id, name, brand_photo, created_at

banners
  id, title, banner_image_url, redirect_url, is_active,
  created_at, updated_at

ratings
  id, product_id, ip_address, rating (1–5), created_at

admins  (uses Laravel's built-in users table with role)
  id, username, email, password, role (admin|user)
```

### 3.2 Relationships
- Product `belongsTo` Brand, Category
- Product `hasMany` ProductPhoto, ProductVariant, Rating
- Category `hasMany` Product
- Brand `hasMany` Product

---

## 4. Pages & Routes

### Public (Guest) Pages

| Route | Vue Page | Description |
|---|---|---|
| `/` | `pages/Home.vue` | Hero banner, category grid, promo products, newest products |
| `/catalog` | `pages/Catalog.vue` | Full product listing with filter + infinite scroll |
| `/catalog?category=X` | `pages/Catalog.vue` | Pre-filtered by category |
| `/catalog?brand=X` | `pages/Catalog.vue` | Pre-filtered by brand |
| `/catalog?search=X` | `pages/Catalog.vue` | Search results |
| `/product/{id}` | `pages/ProductDetail.vue` | Product detail, photos, variants, rating |
| `/brands` | `pages/Brands.vue` | Brand listing with logo grid |
| `/brands/{id}` | `pages/Catalog.vue` | Products filtered by brand |
| `/wishlist` | `pages/Wishlist.vue` | Client-side wishlist (localStorage) |

### Admin Pages (auth-protected)

| Route | Vue Page | Description |
|---|---|---|
| `/admin` | `pages/admin/Dashboard.vue` | Stats overview |
| `/admin/products` | `pages/admin/Products/Index.vue` | Product list + CRUD |
| `/admin/products/create` | `pages/admin/Products/Form.vue` | Create product |
| `/admin/products/{id}/edit` | `pages/admin/Products/Form.vue` | Edit product |
| `/admin/categories` | `pages/admin/Categories/Index.vue` | Category CRUD |
| `/admin/brands` | `pages/admin/Brands/Index.vue` | Brand CRUD |
| `/admin/banners` | `pages/admin/Banners/Index.vue` | Banner CRUD |
| `/login` | `pages/auth/Login.vue` | Admin login (already scaffolded) |

---

## 5. Feature Requirements

### 5.1 Public — Home Page
- [ ] Hero banner carousel (Swiper.js, autoplay, pagination dots)
- [ ] Category grid (swiper with 2 rows, responsive columns: 3 mobile → 6 desktop)
- [ ] "Promo Products" section — products with active promo price
- [ ] "Newest Products" section — sorted by `created_at` desc
- [ ] Responsive product card grid (2 cols mobile → 5 cols desktop-lg)
- [ ] Product card shows: image, name (3-line clamp), price (or promo price + original strikethrough + % off badge)
- [ ] Skeleton loaders while data loads

### 5.2 Public — Catalog Page
- [ ] Infinite scroll (Inertia v2 deferred props + `merge` or cursor pagination)
- [ ] Filter sidebar/drawer: by category, by brand, by price range
- [ ] Sort options: newest, price asc/desc, most sold, top rated
- [ ] Search bar (debounced, updates URL query param)
- [ ] Empty state illustration when no results
- [ ] Skeleton loaders

### 5.3 Public — Product Detail Page
- [ ] Image gallery (main image + thumbnail strip, Swiper)
- [ ] Product name, price, promo price if active
- [ ] Variant selector (chips/buttons)
- [ ] Description (expandable if long)
- [ ] Rating display (avg + total raters)
- [ ] Star rating submission (IP-based, one per product per IP)
- [ ] "Buy on Tokopedia / Shopee" CTA buttons (external links)
- [ ] Related products section (same category)

### 5.4 Public — Brands Page
- [ ] Brand logo grid
- [ ] Click → filtered catalog

### 5.5 Public — Wishlist Page
- [ ] Stored in `localStorage`
- [ ] Add/remove from product cards and detail page
- [ ] Heart icon toggle on product cards

### 5.6 Admin — Products CRUD
- [ ] Paginated product table with search
- [ ] Create/edit form: name, description, brand, category, price, promo price + date range, variants (dynamic add/remove), image upload (multiple, reorder)
- [ ] Delete with confirmation dialog
- [ ] Manually set `total_sold` and `avg_rating`

### 5.7 Admin — Categories & Brands CRUD
- [ ] Name + photo upload
- [ ] Inline edit or modal form

### 5.8 Admin — Banners CRUD
- [ ] Image upload, title, redirect URL, active toggle
- [ ] Drag-to-reorder (optional v2)

### 5.9 Admin — Dashboard
- [ ] Total products, categories, brands count
- [ ] Top 5 rated products
- [ ] Top 5 most sold products

---

## 6. Technical Architecture

### 6.1 Backend (Laravel 12)

```
app/
  Models/
    Product.php, Category.php, Brand.php,
    Banner.php, ProductPhoto.php, ProductVariant.php, Rating.php
  Http/
    Controllers/
      ProductController.php       (public)
      CatalogController.php       (public, handles filter/search/pagination)
      BrandController.php         (public)
      RatingController.php        (public, IP-based POST)
      Admin/
        ProductController.php
        CategoryController.php
        BrandController.php
        BannerController.php
        DashboardController.php
    Requests/
      Admin/StoreProductRequest.php
      Admin/UpdateProductRequest.php
      StoreRatingRequest.php
  Policies/
    AdminPolicy.php               (gate: role === 'admin')
database/
  migrations/
    create_categories_table.php
    create_brands_table.php
    create_products_table.php
    create_product_photos_table.php
    create_product_variants_table.php
    create_banners_table.php
    create_ratings_table.php
    add_role_to_users_table.php
  seeders/
    CategorySeeder.php
    BrandSeeder.php
    ProductSeeder.php
    BannerSeeder.php
    AdminUserSeeder.php
```

### 6.2 Frontend (Vue 3 + Inertia v2)

```
resources/js/
  pages/
    Home.vue
    Catalog.vue
    ProductDetail.vue
    Brands.vue
    Wishlist.vue
    admin/
      Dashboard.vue
      Products/Index.vue
      Products/Form.vue
      Categories/Index.vue
      Brands/Index.vue
      Banners/Index.vue
  layouts/
    GuestLayout.vue       (top nav + bottom mobile nav + footer)
    AdminLayout.vue       (sidebar nav)
  components/
    catalog/
      ProductCard.vue
      ProductGrid.vue
      FilterDrawer.vue
      SortSelect.vue
    home/
      HeroBanner.vue
      CategorySwiper.vue
      ProductSection.vue
    product/
      ImageGallery.vue
      VariantSelector.vue
      StarRating.vue
    shared/
      AppLogo.vue
      TopNav.vue
      BottomMobileNav.vue
      Footer.vue
      SkeletonCard.vue
      EmptyState.vue
      PriceDisplay.vue    (handles promo vs regular price)
  composables/
    useWishlist.ts        (localStorage wishlist)
    useFormatRupiah.ts    (IDR currency formatter)
    usePromoActive.ts     (date range check)
```

### 6.3 File Storage
- Product images, category photos, brand logos, banners → `storage/app/public/uploads/`
- Served via `php artisan storage:link`
- Use Laravel's `store()` with `webp` conversion via `intervention/image` (optional) or accept webp/jpg/png as-is

---

## 7. Libraries & Tools

### Already Installed (use these)
| Library | Purpose |
|---|---|
| `@inertiajs/vue3` v2 | SPA routing, form handling, deferred props |
| `tailwindcss` v3 | Utility-first styling |
| `radix-vue` | Headless UI primitives (dialogs, dropdowns, etc.) |
| `shadcn-vue` (via `components.json`) | Pre-built accessible components |
| `lucide-vue-next` | Icons |
| `@vueuse/core` | Composables (useIntersectionObserver for infinite scroll, useLocalStorage for wishlist) |
| `class-variance-authority` + `clsx` + `tailwind-merge` | Conditional class utilities |
| `@headlessui/vue` | Additional headless components |

### To Install (Frontend)
| Library | Purpose | Install |
|---|---|---|
| `swiper` | Banner + category carousel | `npm install swiper` |

### To Install (Backend / Composer)
| Package | Purpose | Install |
|---|---|---|
| `intervention/image` v3 | Image resizing/optimization on upload | `composer require intervention/image` |
| `spatie/laravel-query-builder` | Clean filter/sort/search on catalog endpoint | `composer require spatie/laravel-query-builder` |

### Already Available (no install needed)
| Tool | Purpose |
|---|---|
| `pestphp/pest` | Testing |
| `laravel/pint` | PHP code formatting |
| `prettier` + `eslint` | JS/Vue formatting and linting |
| `vite` + `laravel-vite-plugin` | Asset bundling |

---

## 8. Responsive Breakpoints

Following Tailwind defaults, matching old project behavior:

| Breakpoint | Min Width | Product Grid Cols | Category Swiper Cols |
|---|---|---|---|
| mobile | < 640px | 2 | 3 |
| sm | 640px | 3 | 4 |
| md | 768px | 3 | 5 |
| lg | 1024px | 4 | 6 |
| xl | 1280px | 5 | 6 |

Bottom mobile navigation bar visible on `< lg` screens.
Top navigation bar always visible.

---

## 9. Implementation Tasks (Ordered)

### Phase 1 — Foundation
1. Update CSS variables in `app.css` to gold/black brand palette
2. Update `tailwind.config.js` to add brand color tokens
3. Create all database migrations
4. Create Eloquent models with relationships and factories
5. Run seeders with sample data from old SQL dump

### Phase 2 — Public Layouts & Shared Components
6. Build `GuestLayout.vue` (TopNav, BottomMobileNav, Footer)
7. Build `AdminLayout.vue` (sidebar, breadcrumb)
8. Build `ProductCard.vue`, `SkeletonCard.vue`, `PriceDisplay.vue`
9. Build `EmptyState.vue`

### Phase 3 — Public Pages
10. Home page (banner, categories, promo section, newest section)
11. Catalog page (grid + filter + sort + infinite scroll + search)
12. Product detail page (gallery, variants, rating)
13. Brands page
14. Wishlist page (localStorage)

### Phase 4 — Admin Panel
15. Admin dashboard (stats)
16. Products CRUD (list, create, edit, delete, image upload)
17. Categories CRUD
18. Brands CRUD
19. Banners CRUD

### Phase 5 — Polish
20. Skeleton loaders on all data-fetching sections
21. Page transition animations (Inertia progress bar already included)
22. SEO meta tags via Inertia `<Head>`
23. Mobile UX pass (touch targets, bottom nav active states)
24. Error pages (404, 500)

---

## 10. Notes & Decisions

- **No cart / checkout** — this is a catalog only. All purchase CTAs link to Tokopedia/Shopee.
- **Rating system** — IP-based (stored in `ratings` table), one rating per product per IP. No user account required.
- **Wishlist** — client-side only via `localStorage`. No server persistence needed for v1.
- **Admin auth** — uses the existing Laravel auth scaffolding. A `role` column on `users` table gates admin routes via middleware.
- **Image uploads** — stored locally in `storage/app/public`. No cloud storage for v1.
- **Promo price** — a product has an optional `promo_price` with `promo_price_start_date` / `promo_price_end_date`. The frontend checks if today falls within the range.
- **Language** — UI text will be in **Indonesian** (Bahasa Indonesia) to match the original and the target market.
- **Social links** (from old footer): YouTube, Instagram, TikTok, Tokopedia, Shopee, Google Maps — all preserved in the new footer.

# GG Case Store — SEO Plan & Requirements

> Companion document to `GG_CASE_STORE_PLAN.md`.
> Covers everything needed for the site to be discoverable on Google (and google.co.id),
> with a focus on the Indonesian local market and mobile-first search behavior.

---

## 1. Context & Goals

GG Case Store is a **local physical shop in Samarinda, Indonesia** with a catalog website.
The primary search audience is Indonesian users searching on mobile via Google Indonesia (`google.co.id`).

Key SEO goals:
1. Rank for product-specific searches — e.g. *"micro SD 128GB Samarinda"*, *"airbuds murah Samarinda"*
2. Appear in **Google Maps / Local Pack** for *"toko aksesoris HP Samarinda"*
3. Show **rich results** (star ratings, price, product name) in Google SERPs
4. Be crawlable despite being an Inertia.js SPA (requires SSR or careful meta injection)
5. Outrank Tokopedia/Shopee listings for branded searches like *"GG Case Store"*

---

## 2. The SPA Crawlability Problem & Solution

Inertia.js renders pages client-side. Googlebot can execute JavaScript, but it is slower and less reliable than crawling static HTML. For a catalog site where product pages need to be indexed, this matters.

### Recommended Approach: Inertia SSR

Enable Inertia's built-in SSR mode (`npm run build:ssr`). This pre-renders each page to HTML on the server before sending it to the browser, giving crawlers fully-formed HTML on first load.

```bash
# Already in package.json
npm run build:ssr
```

The `vite.config.ts` already has an SSR entry point scaffold from the Laravel Vue starter kit. SSR requires Node.js running alongside PHP (via `php artisan inertia:start-ssr`).

**If SSR is not feasible in v1**, the fallback is to ensure all critical meta tags are injected server-side via the Blade layout (`resources/views/app.blade.php`) using data passed from the controller, so at minimum the `<title>`, `<meta name="description">`, and canonical URL are present in the raw HTML response.

---

## 3. Meta Tags — Per Page Requirements

All meta tags are set via Inertia's `<Head>` component in each Vue page.
The controller passes the necessary data as Inertia props.

### 3.1 Global (all pages)

```html
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="robots" content="index, follow" />
<link rel="canonical" href="https://ggcasestore.com/[current-path]" />

<!-- Open Graph (for WhatsApp/social sharing — huge in Indonesia) -->
<meta property="og:site_name" content="GG Case Store" />
<meta property="og:locale" content="id_ID" />
<meta property="og:type" content="website" />

<!-- Twitter Card (fallback) -->
<meta name="twitter:card" content="summary_large_image" />
```

### 3.2 Home Page

```html
<title>GG Case Store — Aksesoris HP & Gadget Terlengkap di Samarinda</title>
<meta name="description"
  content="Toko aksesoris HP, earphone, charger, casing, dan kartu memori terpercaya di Samarinda. Cek katalog produk GG Case Store dan beli di Tokopedia atau Shopee." />
<meta property="og:title" content="GG Case Store — Aksesoris HP & Gadget Samarinda" />
<meta property="og:description" content="Katalog lengkap aksesoris HP dan gadget. Temukan earphone, charger, casing, kartu SD, dan lebih banyak lagi." />
<meta property="og:image" content="https://ggcasestore.com/og-home.jpg" />
<meta property="og:url" content="https://ggcasestore.com/" />
```

### 3.3 Catalog Page (with filters)

```html
<!-- Dynamic based on active filter -->
<title>Katalog {Category} — GG Case Store Samarinda</title>
<!-- e.g. "Katalog Earphone — GG Case Store Samarinda" -->
<meta name="description"
  content="Temukan {Category} terbaik di GG Case Store. Harga terjangkau, kualitas terjamin. Tersedia di Tokopedia dan Shopee." />
<link rel="canonical" href="https://ggcasestore.com/catalog?category={id}" />

<!-- Paginated catalog: tell Google which is the canonical set -->
<!-- Do NOT use rel=prev/next (deprecated). Use canonical pointing to page 1 for paginated views. -->
```

### 3.4 Product Detail Page

```html
<title>{Product Name} — GG Case Store</title>
<!-- e.g. "SanDisk microSDXC Ultra 128GB — GG Case Store" -->
<meta name="description"
  content="{First 150 chars of product description stripped of HTML}. Beli di Tokopedia atau Shopee." />
<meta property="og:type" content="product" />
<meta property="og:title" content="{Product Name}" />
<meta property="og:description" content="{Short description}" />
<meta property="og:image" content="{First product photo URL}" />
<meta property="og:url" content="https://ggcasestore.com/product/{id}" />
<meta property="product:price:amount" content="{price}" />
<meta property="product:price:currency" content="IDR" />
```

### 3.5 Brand Page

```html
<title>Produk {Brand Name} — GG Case Store Samarinda</title>
<meta name="description"
  content="Lihat semua produk {Brand Name} yang tersedia di GG Case Store Samarinda. Harga terbaik, stok terjamin." />
```

### 3.6 Admin Pages

```html
<meta name="robots" content="noindex, nofollow" />
```

All admin routes must be excluded from indexing.

---

## 4. Structured Data (JSON-LD)

Inject as `<script type="application/ld+json">` via Inertia `<Head>` or a dedicated Vue composable `useStructuredData()`.

### 4.1 Organization + LocalBusiness (site-wide, in `GuestLayout.vue`)

This is the most important schema for local search and Google Maps visibility.

```json
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "@id": "https://ggcasestore.com/#business",
  "name": "GG Case Store",
  "alternateName": "GG Case Group",
  "description": "Toko aksesoris HP dan gadget di Samarinda — earphone, charger, casing, kartu memori, dan lebih banyak lagi.",
  "url": "https://ggcasestore.com",
  "logo": "https://ggcasestore.com/assets/logoCatalog.png",
  "image": "https://ggcasestore.com/assets/logoCatalog.png",
  "telephone": "+62-XXX-XXXX-XXXX",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "[Alamat Toko]",
    "addressLocality": "Samarinda",
    "addressRegion": "Kalimantan Timur",
    "postalCode": "75XXX",
    "addressCountry": "ID"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": -0.5022,
    "longitude": 117.1536
  },
  "sameAs": [
    "https://www.tokopedia.com/ggcasegroups",
    "https://shopee.co.id/ggcasegroup",
    "https://www.instagram.com/ggcasegroup.id",
    "https://www.tiktok.com/@ggaccindonesia",
    "https://www.youtube.com/@ggcasegroupentertainment"
  ],
  "priceRange": "Rp 1.200 – Rp 1.349.000"
}
```

> Fill in the real address, phone, and coordinates before going live.

### 4.2 WebSite + SearchAction (enables Google Sitelinks Search Box)

```json
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "@id": "https://ggcasestore.com/#website",
  "url": "https://ggcasestore.com",
  "name": "GG Case Store",
  "inLanguage": "id",
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "https://ggcasestore.com/catalog?search={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  }
}
```

### 4.3 Product Schema (Product Detail Page only)

```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "{product.name}",
  "description": "{product.description}",
  "image": ["{product.photos[0]}", "{product.photos[1]}"],
  "brand": {
    "@type": "Brand",
    "name": "{product.brand.name}"
  },
  "offers": {
    "@type": "Offer",
    "url": "https://ggcasestore.com/product/{product.id}",
    "priceCurrency": "IDR",
    "price": "{product.promo_price_active ? product.promo_price : product.price}",
    "availability": "https://schema.org/InStock",
    "seller": {
      "@type": "Organization",
      "name": "GG Case Store"
    }
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "{product.avg_rating}",
    "reviewCount": "{product.total_raters}",
    "bestRating": "5",
    "worstRating": "1"
  }
}
```

> Only include `aggregateRating` when `total_raters > 0`. Google will reject it otherwise.

### 4.4 BreadcrumbList (Product Detail + Catalog + Brand pages)

```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "https://ggcasestore.com/"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Katalog",
      "item": "https://ggcasestore.com/catalog"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "{product.name}",
      "item": "https://ggcasestore.com/product/{product.id}"
    }
  ]
}
```

### 4.5 ItemList (Catalog & Home product sections)

Helps Google understand the page lists products, enabling richer SERP display.

```json
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Katalog Produk GG Case Store",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "url": "https://ggcasestore.com/product/4"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "url": "https://ggcasestore.com/product/5"
    }
  ]
}
```

---

## 5. URL Structure

Clean, readable URLs are important for both users and crawlers.

| Page | URL Pattern | Notes |
|---|---|---|
| Home | `/` | |
| Catalog (all) | `/catalog` | |
| Catalog filtered | `/catalog?category=kartu-sd` | Use slug, not ID |
| Catalog search | `/catalog?search=earphone` | |
| Product detail | `/product/{id}-{slug}` | e.g. `/product/5-sandisk-microsdxc-ultra-128gb` |
| Brand page | `/brands/{id}-{slug}` | e.g. `/brands/7-samsung` |
| Category page | `/catalog?category={slug}` | Redirect `/category/{slug}` → catalog filter |

**Slug generation:** Add a `slug` column to `products`, `categories`, and `brands` tables. Auto-generate from `name` using Laravel's `Str::slug()` on save.

---

## 6. Sitemap

Generate a dynamic XML sitemap via a Laravel route. Register it in Google Search Console.

### Routes to include:
- `/` (home)
- `/catalog`
- `/brands`
- `/product/{id}-{slug}` — one entry per product
- `/brands/{id}-{slug}` — one entry per brand

### Routes to exclude:
- All `/admin/*` routes
- `/login`, `/register`, `/dashboard`
- `/wishlist` (client-side only, no indexable content)

### Implementation:

```php
// routes/web.php
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
```

```php
// app/Http/Controllers/SitemapController.php
public function index(): Response
{
    $products = Product::select('id', 'slug', 'updated_at')->get();
    $brands   = Brand::select('id', 'slug', 'updated_at')->get();

    return response()->view('sitemap', compact('products', 'brands'))
        ->header('Content-Type', 'application/xml');
}
```

```xml
<!-- resources/views/sitemap.blade.php -->
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/catalog') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    @foreach ($products as $product)
    <url>
        <loc>{{ url("/product/{$product->id}-{$product->slug}") }}</loc>
        <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
    @foreach ($brands as $brand)
    <url>
        <loc>{{ url("/brands/{$brand->id}-{$brand->slug}") }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
</urlset>
```

---

## 7. robots.txt

```
User-agent: *
Allow: /
Disallow: /admin
Disallow: /admin/*
Disallow: /login
Disallow: /register
Disallow: /dashboard
Disallow: /settings
Disallow: /wishlist

Sitemap: https://ggcasestore.com/sitemap.xml
```

Serve via a static file at `public/robots.txt` or a Laravel route.

---

## 8. Performance (Core Web Vitals)

Google uses Core Web Vitals as a ranking signal. These are the targets:

| Metric | Target | How to achieve |
|---|---|---|
| LCP (Largest Contentful Paint) | < 2.5s | Lazy-load below-fold images, preload hero banner image |
| CLS (Cumulative Layout Shift) | < 0.1 | Always set `width` + `height` on `<img>` tags, use skeleton loaders |
| INP (Interaction to Next Paint) | < 200ms | Avoid heavy JS on main thread, use Inertia deferred props |
| TTFB (Time to First Byte) | < 800ms | Enable Laravel route caching, use SQLite or indexed MySQL |

### Image Optimization Checklist
- [ ] Serve images as `.webp` (already used in old project)
- [ ] Add `width` and `height` attributes to all `<img>` tags
- [ ] Use `loading="lazy"` on all below-fold product images
- [ ] Use `loading="eager"` + `fetchpriority="high"` on the hero banner image
- [ ] Set explicit aspect ratios on image wrappers to prevent CLS

### Caching
```php
// In AppServiceProvider or a middleware
Route::get('/', ...)->middleware('cache.headers:public;max_age=3600;etag');
```

---

## 9. Indonesian Local SEO

### 9.1 Google Business Profile (GBP)
This is the single highest-impact action for local visibility.

- [ ] Create/claim the GBP listing for GG Case Store at the Samarinda address
- [ ] Category: "Mobile Phone Accessories Store" / "Electronics Store"
- [ ] Add all product photos to GBP
- [ ] Link GBP website to `https://ggcasestore.com`
- [ ] Add Tokopedia and Shopee store links in GBP
- [ ] Enable messaging and Q&A

### 9.2 NAP Consistency
Name, Address, Phone must be **identical** across:
- The website footer
- Google Business Profile
- Tokopedia store info
- Shopee store info
- Instagram bio

### 9.3 Keyword Strategy (Bahasa Indonesia)

Target a mix of product-specific and local intent keywords:

| Keyword Type | Examples |
|---|---|
| Product + location | "micro SD 128GB Samarinda", "earphone bluetooth Samarinda" |
| Category + location | "toko aksesoris HP Samarinda", "jual casing HP Samarinda" |
| Brand + product | "Samsung Galaxy Buds Samarinda", "Xiaomi charger 33W" |
| Generic catalog | "katalog aksesoris HP", "harga earphone TWS murah" |
| Informal/slang | "airbuds murah", "cas HP type C", "mmc hp" |

Use these keywords naturally in:
- Page titles and meta descriptions
- Product names and descriptions (already in Indonesian in the old data)
- Category and brand names
- Alt text on images

### 9.4 Language & hreflang
The site is Indonesian-only. Add to `<head>`:

```html
<html lang="id">
<link rel="alternate" hreflang="id" href="https://ggcasestore.com/" />
<link rel="alternate" hreflang="x-default" href="https://ggcasestore.com/" />
```

---

## 10. Image Alt Text Requirements

Every `<img>` must have a descriptive `alt` attribute in Indonesian.

| Image type | Alt text pattern | Example |
|---|---|---|
| Product photo | `{product name} - GG Case Store` | `SanDisk microSDXC Ultra 128GB - GG Case Store` |
| Category photo | `Kategori {category name}` | `Kategori Earphone` |
| Brand logo | `Logo {brand name}` | `Logo Samsung` |
| Banner | `{banner title}` | `Promo Kartu SD Agustus 2025` |
| Logo | `GG Case Store Logo` | |

---

## 11. Canonical URLs

Prevent duplicate content issues from filter/sort query parameters.

```php
// In HandleInertiaRequests.php — share canonical with every page
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'canonical' => url($request->path()),  // strips query params
    ];
}
```

In Vue pages, use the shared canonical:
```vue
<Head>
  <link rel="canonical" :href="$page.props.canonical" />
</Head>
```

For catalog pages with filters, the canonical should always point to the base `/catalog` URL to avoid Google indexing hundreds of filter combinations.

---

## 12. Open Graph for WhatsApp Sharing

WhatsApp is the dominant sharing platform in Indonesia. When a user shares a product link on WhatsApp, it should unfurl with a proper preview.

Requirements per product page:
- `og:title` — product name
- `og:description` — first 100 chars of description
- `og:image` — first product photo, minimum 600×315px
- `og:url` — canonical product URL

The `og:image` must be an **absolute URL** (not a relative path).

---

## 13. Implementation Checklist

### Phase 1 — Technical Foundation
- [ ] Add `slug` column to `products`, `categories`, `brands` migrations
- [ ] Auto-generate slugs on model save via `Str::slug()`
- [ ] Update all routes to use `{id}-{slug}` pattern
- [ ] Create `SitemapController` and `sitemap.blade.php`
- [ ] Create `public/robots.txt`
- [ ] Set `<html lang="id">` in `app.blade.php`
- [ ] Share `canonical` URL via `HandleInertiaRequests`

### Phase 2 — Meta Tags
- [ ] Build `usePageMeta` composable (sets title, description, og tags via `<Head>`)
- [ ] Implement per-page meta in: Home, Catalog, ProductDetail, Brands
- [ ] Add `noindex` to all admin and auth pages
- [ ] Add `og:image` fallback (site logo) for pages without a specific image

### Phase 3 — Structured Data
- [ ] Build `useStructuredData` composable that injects JSON-LD via `<Head>`
- [ ] Implement `LocalBusiness` + `WebSite` schema in `GuestLayout.vue`
- [ ] Implement `Product` + `BreadcrumbList` schema in `ProductDetail.vue`
- [ ] Implement `ItemList` schema in `Catalog.vue` and home product sections
- [ ] Implement `BreadcrumbList` in `Catalog.vue` and `Brands.vue`

### Phase 4 — Performance
- [ ] Add `width`, `height`, and `loading="lazy"` to all product `<img>` tags
- [ ] Add `fetchpriority="high"` to hero banner first image
- [ ] Set explicit aspect ratio on all image wrapper divs
- [ ] Enable Laravel route and config caching in production

### Phase 5 — Local SEO (off-site, manual)
- [ ] Set up / claim Google Business Profile
- [ ] Ensure NAP consistency across all platforms
- [ ] Submit sitemap to Google Search Console
- [ ] Submit sitemap to Bing Webmaster Tools

### Phase 6 — Validation
- [ ] Test all structured data with [Google Rich Results Test](https://search.google.com/test/rich-results)
- [ ] Test meta tags with [OpenGraph.xyz](https://www.opengraph.xyz) (WhatsApp preview)
- [ ] Run Lighthouse audit targeting 90+ Performance, 100 SEO scores
- [ ] Verify `robots.txt` is correct at `https://ggcasestore.com/robots.txt`
- [ ] Verify sitemap is accessible at `https://ggcasestore.com/sitemap.xml`

---

## 14. Tools & Resources

| Tool | Purpose | URL |
|---|---|---|
| Google Search Console | Submit sitemap, monitor indexing | search.google.com/search-console |
| Google Rich Results Test | Validate structured data | search.google.com/test/rich-results |
| Google Business Profile | Local search visibility | business.google.com |
| PageSpeed Insights | Core Web Vitals audit | pagespeed.web.dev |
| OpenGraph.xyz | Preview WhatsApp/social sharing | opengraph.xyz |
| Schema.org | Structured data reference | schema.org |
| Ahrefs / Ubersuggest | Keyword research (Indonesian) | ahrefs.com / neilpatel.com/ubersuggest |

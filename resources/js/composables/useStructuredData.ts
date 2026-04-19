import { useHead } from '@vueuse/head';

const APP_URL = window.location.origin;

/** Injects a JSON-LD <script> tag into <head>. */
export function useStructuredData(schema: Record<string, unknown> | Record<string, unknown>[]) {
    useHead({
        script: [
            {
                type: 'application/ld+json',
                innerHTML: JSON.stringify(Array.isArray(schema) ? schema : [schema]),
            },
        ],
    });
}

/** LocalBusiness + WebSite schema — used site-wide in GuestLayout */
export function localBusinessSchema() {
    return [
        {
            '@context': 'https://schema.org',
            '@type': 'LocalBusiness',
            '@id': `${APP_URL}/#business`,
            name: 'GG Case Store',
            alternateName: 'GG Case Group',
            description:
                'Toko aksesoris HP dan gadget di Samarinda — earphone, charger, casing, kartu memori, dan lebih banyak lagi.',
            url: APP_URL,
            logo: `${APP_URL}/images/logoCatalog.png`,
            image: `${APP_URL}/images/logoCatalog.png`,
            address: {
                '@type': 'PostalAddress',
                addressLocality: 'Samarinda',
                addressRegion: 'Kalimantan Timur',
                addressCountry: 'ID',
            },
            sameAs: [
                'https://www.tokopedia.com/ggcasegroups',
                'https://shopee.co.id/ggcasegroup',
                'https://www.instagram.com/ggcasegroup.id',
                'https://www.tiktok.com/@ggaccindonesia',
                'https://www.youtube.com/@ggcasegroupentertainment',
            ],
        },
        {
            '@context': 'https://schema.org',
            '@type': 'WebSite',
            '@id': `${APP_URL}/#website`,
            url: APP_URL,
            name: 'GG Case Store',
            inLanguage: 'id',
            potentialAction: {
                '@type': 'SearchAction',
                target: {
                    '@type': 'EntryPoint',
                    urlTemplate: `${APP_URL}/catalog?search={search_term_string}`,
                },
                'query-input': 'required name=search_term_string',
            },
        },
    ];
}

/** Product schema for detail page */
export function productSchema(product: {
    id: number;
    hash: string;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    promo_price: number | null;
    is_promo_active: boolean;
    brand_name: string | null;
    photos: { photo_url: string }[];
}) {
    const effectivePrice = product.is_promo_active && product.promo_price
        ? product.promo_price
        : product.price;

    return {
        '@context': 'https://schema.org',
        '@type': 'Product',
        name: product.name,
        description: product.description ?? product.name,
        image: product.photos.map((p) => p.photo_url),
        brand: product.brand_name
            ? { '@type': 'Brand', name: product.brand_name }
            : undefined,
        offers: {
            '@type': 'Offer',
            url: `${APP_URL}/product/${product.hash}`,
            priceCurrency: 'IDR',
            price: effectivePrice,
            availability: 'https://schema.org/InStock',
            seller: { '@type': 'Organization', name: 'GG Case Store' },
        },
    };
}

/** BreadcrumbList schema */
export function breadcrumbSchema(items: { name: string; url: string }[]) {
    return {
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: items.map((item, i) => ({
            '@type': 'ListItem',
            position: i + 1,
            name: item.name,
            item: item.url,
        })),
    };
}

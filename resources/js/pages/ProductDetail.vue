<script setup lang="ts">
import ProductCard from '@/components/catalog/ProductCard.vue';
import PriceDisplay from '@/components/shared/PriceDisplay.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { useWishlist } from '@/composables/useWishlist';
import { breadcrumbSchema, productSchema, useStructuredData } from '@/composables/useStructuredData';
import { Head, Link } from '@inertiajs/vue3';
import { ChevronRight, Heart } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { FreeMode, Navigation, Thumbs } from 'swiper/modules';
import type { Swiper as SwiperType } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/free-mode';
import 'swiper/css/thumbs';

interface Photo { id: number; photo_url: string }

interface Product {
    id: number;
    hash: string;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    promo_price: number | null;
    is_promo_active: boolean;
    promo_price_start_date: string | null;
    promo_price_end_date: string | null;
    produk_dilihat: number;
    brand_name: string | null;
    brand_slug: string | null;
    category_name: string | null;
    category_slug: string | null;
    photos: Photo[];
    variants: string[];
}

interface RelatedProduct {
    hash: string;
    name: string;
    slug: string;
    price: number;
    promo_price: number | null;
    is_promo_active: boolean;
    first_photo: string | null;
    brand_name: string | null;
}

const props = defineProps<{ product: Product; related: RelatedProduct[] }>();

const { isWishlisted, toggle } = useWishlist();
const wishlisted = isWishlisted(props.product.hash);

// Structured data
useStructuredData([
    productSchema(props.product),
    breadcrumbSchema([
        { name: 'Home', url: `${window.location.origin}/` },
        { name: 'Katalog', url: `${window.location.origin}/catalog` },
        ...(props.product.category_name
            ? [{ name: props.product.category_name, url: `${window.location.origin}/catalog?category=${props.product.category_slug}` }]
            : []),
        { name: props.product.name, url: `${window.location.origin}/product/${props.product.hash}` },
    ]),
]);

// Gallery
const thumbsSwiper = ref<SwiperType | null>(null);
const setThumbsSwiper = (swiper: SwiperType) => { thumbsSwiper.value = swiper; };
const modules = [FreeMode, Navigation, Thumbs];

// Variant selection
const selectedVariant = ref<string | null>(props.product.variants[0] ?? null);

// Description expand
const descExpanded = ref(false);
const descLong = computed(() => (props.product.description?.length ?? 0) > 300);
const descText = computed(() =>
    descLong.value && !descExpanded.value
        ? props.product.description!.slice(0, 300) + '…'
        : props.product.description
);
</script>

<template>
    <Head>
        <title>{{ product.name }} — GG Case Store</title>
        <meta name="description" :content="`${product.description?.slice(0, 150) ?? product.name}. Beli di Tokopedia atau Shopee.`" />
        <link rel="canonical" :href="`/product/${product.hash}`" />
        <meta property="og:type" content="product" />
        <meta property="og:title" :content="`${product.name} — GG Case Store`" />
        <meta property="og:description" :content="product.description?.slice(0, 150) ?? product.name" />
        <meta v-if="product.photos[0]" property="og:image" :content="product.photos[0].photo_url" />
        <meta property="og:url" :content="`/product/${product.hash}`" />
        <meta property="product:price:amount" :content="String(product.is_promo_active && product.promo_price ? product.promo_price : product.price)" />
        <meta property="product:price:currency" content="IDR" />
    </Head>

    <GuestLayout>
        <div class="mx-auto max-w-6xl px-4 py-6">

            <!-- ── Breadcrumb ── -->
            <nav class="mb-5 flex items-center gap-1.5 text-xs text-muted-foreground">
                <Link href="/" class="hover:text-primary">Home</Link>
                <ChevronRight :size="12" />
                <Link href="/catalog" class="hover:text-primary">Katalog</Link>
                <template v-if="product.category_name">
                    <ChevronRight :size="12" />
                    <Link :href="`/catalog?category=${product.category_slug}`" class="hover:text-primary">
                        {{ product.category_name }}
                    </Link>
                </template>
                <ChevronRight :size="12" />
                <span class="line-clamp-1 text-foreground">{{ product.name }}</span>
            </nav>

            <!-- ── Main layout ── -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">

                <!-- Left — Gallery -->
                <div class="space-y-3">
                    <!-- Main image -->
                    <Swiper
                        :modules="modules"
                        :thumbs="{ swiper: thumbsSwiper }"
                        :navigation="product.photos.length > 1"
                        class="gallery-main overflow-hidden rounded-2xl border border-border bg-muted"
                    >
                        <SwiperSlide v-for="photo in product.photos" :key="photo.id">
                            <img
                                :src="photo.photo_url"
                                :alt="product.name"
                                width="600"
                                height="600"
                                class="aspect-square w-full object-contain"
                            />
                        </SwiperSlide>
                        <!-- Fallback when no photos -->
                        <SwiperSlide v-if="product.photos.length === 0">
                            <div class="flex aspect-square w-full items-center justify-center text-muted-foreground">
                                <span class="text-sm">Tidak ada foto</span>
                            </div>
                        </SwiperSlide>
                    </Swiper>

                    <!-- Thumbnails -->
                    <Swiper
                        v-if="product.photos.length > 1"
                        :modules="[FreeMode, Thumbs]"
                        :slides-per-view="5"
                        :space-between="8"
                        :free-mode="true"
                        :watch-slides-progress="true"
                        class="gallery-thumbs"
                        @swiper="setThumbsSwiper"
                    >
                        <SwiperSlide
                            v-for="photo in product.photos"
                            :key="photo.id"
                            class="cursor-pointer overflow-hidden rounded-lg border border-border bg-muted"
                        >
                            <img
                                :src="photo.photo_url"
                                :alt="product.name"
                                width="100"
                                height="100"
                                class="aspect-square w-full object-contain p-1"
                            />
                        </SwiperSlide>
                    </Swiper>
                </div>

                <!-- Right — Info -->
                <div class="flex flex-col gap-5">

                    <!-- Brand + name -->
                    <div>
                        <Link
                            v-if="product.brand_name"
                            :href="`/catalog?brand=${product.brand_slug}`"
                            class="mb-1 inline-block text-xs font-semibold uppercase tracking-widest text-primary hover:underline"
                        >
                            {{ product.brand_name }}
                        </Link>
                        <h1 class="text-xl font-bold leading-snug text-foreground sm:text-2xl">
                            {{ product.name }}
                        </h1>
                        <p class="mt-1.5 flex items-center gap-1 text-xs text-muted-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            Dilihat {{ product.produk_dilihat.toLocaleString('id-ID') }} kali
                        </p>
                    </div>

                    <!-- Price -->
                    <div class="rounded-xl bg-muted/50 px-4 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <PriceDisplay
                                :price="product.price"
                                :promo-price="product.promo_price"
                                :is-promo-active="product.is_promo_active"
                            />
                            <!-- Wishlist — mobile only, shown beside price -->
                            <button
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-border transition-colors hover:border-red-400 sm:hidden"
                                :aria-label="wishlisted ? 'Hapus dari wishlist' : 'Tambah ke wishlist'"
                                @click="toggle(product.hash)"
                            >
                                <Heart
                                    :size="18"
                                    :class="wishlisted ? 'fill-red-500 text-red-500' : 'text-muted-foreground'"
                                />
                            </button>
                        </div>
                        <p v-if="product.is_promo_active && product.promo_price_end_date" class="mt-1.5 text-xs text-muted-foreground">
                            Promo berlaku hingga {{ product.promo_price_end_date }}
                        </p>
                    </div>

                    <!-- Variants -->
                    <div v-if="product.variants.length > 0">
                        <p class="mb-2 text-sm font-medium text-foreground">
                            Pilih Varian:
                            <span class="font-semibold text-primary">{{ selectedVariant }}</span>
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="variant in product.variants"
                                :key="variant"
                                :class="[
                                    'rounded-lg border px-4 py-2 text-sm font-medium transition-colors',
                                    selectedVariant === variant
                                        ? 'border-primary bg-primary text-white'
                                        : 'border-border text-foreground hover:border-primary',
                                ]"
                                @click="selectedVariant = variant"
                            >
                                {{ variant }}
                            </button>
                        </div>
                    </div>

                    <!-- CTA buttons -->
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <a
                            href="https://www.tokopedia.com/ggcasegroups"
                            target="_blank"
                            rel="noopener"
                            class="flex flex-1 items-center justify-center gap-2 rounded-full border border-primary py-3 text-sm font-semibold text-primary transition-colors hover:bg-primary hover:text-white"
                        >
                            <img src="/images/social/tokopedia.png" alt="" width="18" height="18" class="h-[18px] w-[18px] object-contain" />
                            Beli di Tokopedia
                        </a>
                        <a
                            href="https://shopee.co.id/ggcasegroup"
                            target="_blank"
                            rel="noopener"
                            class="flex flex-1 items-center justify-center gap-2 rounded-full border border-primary py-3 text-sm font-semibold text-primary transition-colors hover:bg-primary hover:text-white"
                        >
                            <img src="/images/social/shopee.png" alt="" width="18" height="18" class="h-[18px] w-[18px] object-contain" />
                            Beli di Shopee
                        </a>
                        <button
                            class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-full border border-border transition-colors hover:border-red-400 sm:flex"
                            :aria-label="wishlisted ? 'Hapus dari wishlist' : 'Tambah ke wishlist'"
                            @click="toggle(product.hash)"
                        >
                            <Heart
                                :size="20"
                                :class="wishlisted ? 'fill-red-500 text-red-500' : 'text-muted-foreground'"
                            />
                        </button>
                    </div>
                </div>
            </div>

            <!-- ── Description ── -->
            <div v-if="product.description" class="mt-10">
                <h2 class="mb-3 text-base font-bold text-foreground">Deskripsi Produk</h2>
                <div class="rounded-xl border border-border p-5">
                    <p class="whitespace-pre-line text-sm leading-relaxed text-muted-foreground">{{ descText }}</p>
                    <button
                        v-if="descLong"
                        class="mt-3 text-sm font-medium text-primary hover:underline"
                        @click="descExpanded = !descExpanded"
                    >
                        {{ descExpanded ? 'Tampilkan lebih sedikit' : 'Tampilkan selengkapnya' }}
                    </button>
                </div>
            </div>

            <!-- ── Related Products ── -->
            <div v-if="related.length > 0" class="mt-10">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-bold text-foreground">Produk Serupa</h2>
                    <Link
                        v-if="product.category_slug"
                        :href="`/catalog?category=${product.category_slug}`"
                        class="flex items-center gap-1 text-sm font-medium text-primary hover:underline"
                    >
                        Lihat Semua <ChevronRight :size="16" />
                    </Link>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                    <ProductCard
                        v-for="p in related"
                        :key="p.hash"
                        :hash="p.hash"
                        :name="p.name"
                        :slug="p.slug"
                        :price="p.price"
                        :promo-price="p.promo_price"
                        :is-promo-active="p.is_promo_active"
                        :first-photo="p.first_photo"
                        :brand-name="p.brand_name"
                    />
                </div>
            </div>

        </div>
    </GuestLayout>
</template>

<style scoped>
.gallery-main :deep(.swiper-button-next),
.gallery-main :deep(.swiper-button-prev) {
    color: rgba(255, 255, 255, 0.92);
    background: rgba(0, 0, 0, 0.22);
    backdrop-filter: blur(8px) saturate(1.4);
    -webkit-backdrop-filter: blur(8px) saturate(1.4);
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 0.5px solid rgba(255, 255, 255, 0.18);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.18);
    transition: background 0.15s ease, transform 0.1s ease;
    top: 50%;
    margin-top: -11px;
}
.gallery-main :deep(.swiper-button-next):active,
.gallery-main :deep(.swiper-button-prev):active {
    background: rgba(0, 0, 0, 0.38);
    transform: scale(0.92);
}
.gallery-main :deep(.swiper-button-next::after),
.gallery-main :deep(.swiper-button-prev::after) {
    font-size: 8px;
    font-weight: 700;
    letter-spacing: -0.5px;
}
.gallery-thumbs :deep(.swiper-slide-thumb-active) {
    border-color: hsl(var(--primary));
}
</style>

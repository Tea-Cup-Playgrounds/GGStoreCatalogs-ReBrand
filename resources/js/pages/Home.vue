<script setup lang="ts">
import ProductCard from '@/components/catalog/ProductCard.vue';
import SkeletonCard from '@/components/shared/SkeletonCard.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { Autoplay, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/pagination';

interface Banner {
    id: number;
    title: string;
    banner_image_url: string;
    redirect_url: string | null;
}

interface Category {
    id: number;
    name: string;
    slug: string;
    category_photo: string | null;
    products_count: number;
}

interface Product {
    id: number;
    name: string;
    slug: string;
    price: number;
    promo_price: number | null;
    is_promo_active: boolean;
    first_photo: string | null;
    brand_name: string | null;
}

const props = defineProps<{
    banners: Banner[];
    categories: Category[];
    promoProducts: Product[];
    newestProducts: Product[];
}>();

const modules = [Autoplay, Pagination];
const loading = ref(true);

onMounted(() => {
    loading.value = false;
});
</script>

<template>
    <Head>
        <title>GG Case Store — Aksesoris HP & Gadget Terlengkap di Samarinda</title>
        <meta
            name="description"
            content="Toko aksesoris HP, earphone, charger, casing, dan kartu memori terpercaya di Samarinda. Cek katalog produk GG Case Store."
        />
    </Head>

    <GuestLayout>
        <div class="mx-auto max-w-6xl px-4 py-6 space-y-10">

            <!-- ── Hero Banner ── -->
            <section aria-label="Banner promosi">
                <Swiper
                    :modules="modules"
                    :loop="banners.length > 1"
                    :autoplay="{ delay: 4000, disableOnInteraction: false }"
                    :pagination="{ clickable: true }"
                    :grab-cursor="true"
                    class="banner-swiper overflow-hidden rounded-2xl"
                >
                    <SwiperSlide v-for="banner in banners" :key="banner.id">
                        <component
                            :is="banner.redirect_url ? 'a' : 'div'"
                            :href="banner.redirect_url ?? undefined"
                            class="block"
                        >
                            <img
                                :src="banner.banner_image_url"
                                :alt="banner.title"
                                width="1200"
                                height="400"
                                class="h-40 w-full object-cover sm:h-56 md:h-72 lg:h-80"
                                :fetchpriority="banners.indexOf(banner) === 0 ? 'high' : 'auto'"
                                :loading="banners.indexOf(banner) === 0 ? 'eager' : 'lazy'"
                            />
                        </component>
                    </SwiperSlide>
                </Swiper>
            </section>

            <!-- ── Categories ── -->
            <section aria-label="Kategori produk">
                <h2 class="mb-4 text-lg font-bold uppercase tracking-wide text-foreground">Kategori</h2>
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6">
                    <Link
                        v-for="cat in categories"
                        :key="cat.id"
                        :href="`/catalog?category=${cat.slug}`"
                        class="group flex flex-col overflow-hidden rounded-xl border border-border bg-card transition-all hover:border-primary hover:shadow-md"
                    >
                        <!-- Square image area -->
                        <div class="aspect-square w-full overflow-hidden bg-muted">
                            <img
                                v-if="cat.category_photo"
                                :src="cat.category_photo"
                                :alt="`Kategori ${cat.name}`"
                                width="200"
                                height="200"
                                loading="lazy"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center text-3xl">
                                📦
                            </div>
                        </div>
                        <!-- Name label -->
                        <div class="px-2 py-2 text-center">
                            <span class="line-clamp-2 text-xs font-medium leading-tight text-foreground group-hover:text-primary">
                                {{ cat.name }}
                            </span>
                        </div>
                    </Link>
                </div>
            </section>

            <!-- ── Promo Products ── -->
            <section v-if="promoProducts.length > 0" aria-label="Produk yang sedang promo">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold uppercase tracking-wide text-foreground">
                        🔥 Produk yang Sedang Promo
                    </h2>
                    <Link
                        href="/catalog?promo=1"
                        class="flex items-center gap-1 text-sm font-medium text-primary hover:underline"
                    >
                        Lihat Semua <ChevronRight :size="16" />
                    </Link>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                    <template v-if="loading">
                        <SkeletonCard v-for="n in 5" :key="n" />
                    </template>
                    <template v-else>
                        <ProductCard
                            v-for="product in promoProducts"
                            :key="product.id"
                            :id="product.id"
                            :name="product.name"
                            :slug="product.slug"
                            :price="product.price"
                            :promo-price="product.promo_price"
                            :is-promo-active="product.is_promo_active"
                            :first-photo="product.first_photo"
                            :brand-name="product.brand_name"
                        />
                    </template>
                </div>
            </section>

            <!-- ── Newest Products ── -->
            <section aria-label="Produk terbaru">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold uppercase tracking-wide text-foreground">
                        Produk Terbaru
                    </h2>
                    <Link
                        href="/catalog"
                        class="flex items-center gap-1 text-sm font-medium text-primary hover:underline"
                    >
                        Lihat Semua <ChevronRight :size="16" />
                    </Link>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                    <template v-if="loading">
                        <SkeletonCard v-for="n in 10" :key="n" />
                    </template>
                    <template v-else>
                        <ProductCard
                            v-for="product in newestProducts"
                            :key="product.id"
                            :id="product.id"
                            :name="product.name"
                            :slug="product.slug"
                            :price="product.price"
                            :promo-price="product.promo_price"
                            :is-promo-active="product.is_promo_active"
                            :first-photo="product.first_photo"
                            :brand-name="product.brand_name"
                        />
                    </template>
                </div>
            </section>

        </div>
    </GuestLayout>
</template>

<style scoped>
.banner-swiper :deep(.swiper-pagination-bullet-active) {
    background: hsl(var(--primary));
}
</style>

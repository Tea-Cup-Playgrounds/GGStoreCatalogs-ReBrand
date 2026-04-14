<script setup lang="ts">
import ProductCard from '@/components/catalog/ProductCard.vue';
import SkeletonCard from '@/components/shared/SkeletonCard.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Filter, SlidersHorizontal, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Product {
    id: number;
    name: string;
    slug: string;
    price: number;
    promo_price: number | null;
    is_promo_active: boolean;
    first_photo: string | null;
    brand_name: string | null;
    category_name: string | null;
}

interface PaginatedProducts {
    data: Product[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface FilterOption {
    id: number;
    name: string;
    slug: string;
}

interface Filters {
    search?: string;
    category?: string;
    brand?: string;
    sort?: string;
    promo?: string;
}

const props = defineProps<{
    products: PaginatedProducts;
    categories: FilterOption[];
    brands: FilterOption[];
    filters: Filters;
}>();

const drawerOpen = ref(false);
const loading = ref(false);

const sortOptions = [
    { value: 'newest',     label: 'Terbaru' },
    { value: 'price_asc',  label: 'Harga Terendah' },
    { value: 'price_desc', label: 'Harga Tertinggi' },
];

const activeSort   = ref(props.filters.sort ?? 'newest');
const activeSearch = ref(props.filters.search ?? '');
let searchTimer: ReturnType<typeof setTimeout>;

const activeFiltersCount = computed(() => {
    let count = 0;
    if (props.filters.category) count++;
    if (props.filters.brand) count++;
    if (props.filters.promo) count++;
    return count;
});

const pageTitle = computed(() => {
    if (props.filters.search) return `Hasil pencarian: "${props.filters.search}"`;
    if (props.filters.category) {
        const cat = props.categories.find((c) => c.slug === props.filters.category);
        return cat ? `Katalog ${cat.name}` : 'Katalog';
    }
    if (props.filters.brand) {
        const brand = props.brands.find((b) => b.slug === props.filters.brand);
        return brand ? `Produk ${brand.name}` : 'Katalog';
    }
    return 'Katalog Produk';
});

function applyFilter(params: Record<string, string | undefined>) {
    loading.value = true;
    router.get('/catalog', { ...props.filters, ...params, page: undefined }, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => { loading.value = false; drawerOpen.value = false; },
    });
}

function clearFilter(key: string) {
    applyFilter({ [key]: undefined });
}

function clearAll() {
    loading.value = true;
    router.get('/catalog', {}, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => { loading.value = false; drawerOpen.value = false; },
    });
}

watch(activeSort, (val) => applyFilter({ sort: val }));

function onSearchInput() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => applyFilter({ search: activeSearch.value || undefined }), 400);
}
</script>

<template>
    <Head>
        <title>{{ pageTitle }} — GG Case Store</title>
        <meta name="description" content="Temukan aksesoris HP, earphone, charger, casing, dan kartu memori terbaik di GG Case Store Samarinda." />
    </Head>

    <GuestLayout>
        <div class="mx-auto max-w-6xl px-4 py-6">

            <!-- ── Header ── -->
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-xl font-bold text-foreground">{{ pageTitle }}</h1>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        {{ products.total }} produk ditemukan
                    </p>
                </div>

                <!-- Search + Sort + Filter toggle -->
                <div class="flex items-center gap-2">
                    <!-- Search -->
                    <div class="relative flex-1 sm:w-52 sm:flex-none">
                        <input
                            v-model="activeSearch"
                            type="search"
                            placeholder="Cari produk..."
                            class="h-9 w-full rounded-full border border-border bg-muted pl-4 pr-4 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                            @input="onSearchInput"
                        />
                    </div>

                    <!-- Sort -->
                    <select
                        v-model="activeSort"
                        class="h-9 rounded-full border border-border bg-muted px-3 text-sm outline-none focus:border-primary"
                    >
                        <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">
                            {{ opt.label }}
                        </option>
                    </select>

                    <!-- Filter toggle -->
                    <button
                        class="relative flex h-9 items-center gap-1.5 rounded-full border border-border bg-muted px-3 text-sm transition-colors hover:border-primary"
                        @click="drawerOpen = true"
                    >
                        <SlidersHorizontal :size="15" />
                        Filter
                        <span
                            v-if="activeFiltersCount > 0"
                            class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-white"
                        >
                            {{ activeFiltersCount }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- ── Active filter chips ── -->
            <div v-if="activeFiltersCount > 0" class="mb-4 flex flex-wrap items-center gap-2">
                <span class="text-xs text-muted-foreground">Filter aktif:</span>
                <button
                    v-if="filters.category"
                    class="flex items-center gap-1 rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary"
                    @click="clearFilter('category')"
                >
                    {{ categories.find(c => c.slug === filters.category)?.name }}
                    <X :size="12" />
                </button>
                <button
                    v-if="filters.brand"
                    class="flex items-center gap-1 rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary"
                    @click="clearFilter('brand')"
                >
                    {{ brands.find(b => b.slug === filters.brand)?.name }}
                    <X :size="12" />
                </button>
                <button
                    v-if="filters.promo"
                    class="flex items-center gap-1 rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary"
                    @click="clearFilter('promo')"
                >
                    Promo <X :size="12" />
                </button>
                <button class="text-xs text-muted-foreground underline" @click="clearAll">
                    Hapus semua
                </button>
            </div>

            <!-- ── Product grid ── -->
            <div v-if="loading" class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                <SkeletonCard v-for="n in 10" :key="n" />
            </div>

            <template v-else>
                <!-- Empty state -->
                <div v-if="products.data.length === 0" class="flex flex-col items-center justify-center py-24 text-center">
                    <svg class="mb-5 h-28 w-28 text-muted-foreground/30" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <!-- Box body -->
                        <path d="M40 80 L100 50 L160 80 L160 150 L100 180 L40 150 Z" stroke="currentColor" stroke-width="5" stroke-linejoin="round" fill="none"/>
                        <!-- Box lid fold -->
                        <path d="M40 80 L100 110 L160 80" stroke="currentColor" stroke-width="5" stroke-linejoin="round" fill="none"/>
                        <!-- Center spine -->
                        <path d="M100 110 L100 180" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                        <!-- Lid flaps -->
                        <path d="M100 50 L100 20" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                        <path d="M70 65 L70 35" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                        <path d="M130 65 L130 35" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                        <!-- Magnifier -->
                        <circle cx="148" cy="148" r="22" stroke="currentColor" stroke-width="5" fill="none"/>
                        <path d="M164 164 L178 178" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                        <!-- X inside magnifier -->
                        <path d="M140 140 L156 156 M156 140 L140 156" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                    <p class="text-lg font-semibold text-foreground">Produk tidak ditemukan</p>
                    <p class="mt-1 text-sm text-muted-foreground">Coba ubah filter atau kata kunci pencarian.</p>
                    <button class="mt-4 rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white" @click="clearAll">
                        Reset Filter
                    </button>
                </div>

                <div v-else class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                    <ProductCard
                        v-for="product in products.data"
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
                </div>

                <!-- ── Pagination ── -->
                <div v-if="products.last_page > 1" class="mt-8 flex items-center justify-center gap-1">
                    <Link
                        v-for="link in products.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        :class="[
                            'flex h-9 min-w-9 items-center justify-center rounded-lg border px-3 text-sm transition-colors',
                            link.active
                                ? 'border-primary bg-primary font-semibold text-white'
                                : 'border-border bg-card text-foreground hover:border-primary hover:text-primary',
                            !link.url ? 'pointer-events-none opacity-40' : '',
                        ]"
                        preserve-scroll
                        v-html="link.label"
                    />
                </div>
            </template>
        </div>

        <!-- ── Filter Drawer ── -->
        <Teleport to="body">
            <Transition name="drawer">
                <div v-if="drawerOpen" class="fixed inset-0 z-50 flex justify-end">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/50" @click="drawerOpen = false" />

                    <!-- Panel -->
                    <div class="relative flex h-full w-72 flex-col overflow-y-auto bg-background shadow-xl">
                        <div class="flex items-center justify-between border-b border-border px-4 py-4">
                            <span class="font-semibold text-foreground">Filter Produk</span>
                            <button class="rounded-full p-1 hover:bg-muted" @click="drawerOpen = false">
                                <X :size="18" />
                            </button>
                        </div>

                        <div class="flex-1 space-y-6 px-4 py-5">
                            <!-- Promo toggle -->
                            <div>
                                <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Penawaran</h3>
                                <button
                                    :class="[
                                        'w-full rounded-lg border px-4 py-2.5 text-left text-sm font-medium transition-colors',
                                        filters.promo
                                            ? 'border-primary bg-primary/10 text-primary'
                                            : 'border-border text-foreground hover:border-primary',
                                    ]"
                                    @click="applyFilter({ promo: filters.promo ? undefined : '1' })"
                                >
                                    🔥 Produk Promo
                                </button>
                            </div>

                            <!-- Categories -->
                            <div>
                                <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Kategori</h3>
                                <div class="space-y-1">
                                    <button
                                        v-for="cat in categories"
                                        :key="cat.id"
                                        :class="[
                                            'w-full rounded-lg border px-4 py-2.5 text-left text-sm transition-colors',
                                            filters.category === cat.slug
                                                ? 'border-primary bg-primary/10 font-medium text-primary'
                                                : 'border-transparent text-foreground hover:bg-muted',
                                        ]"
                                        @click="applyFilter({ category: filters.category === cat.slug ? undefined : cat.slug })"
                                    >
                                        {{ cat.name }}
                                    </button>
                                </div>
                            </div>

                            <!-- Brands -->
                            <div>
                                <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Brand</h3>
                                <div class="space-y-1">
                                    <button
                                        v-for="brand in brands"
                                        :key="brand.id"
                                        :class="[
                                            'w-full rounded-lg border px-4 py-2.5 text-left text-sm transition-colors',
                                            filters.brand === brand.slug
                                                ? 'border-primary bg-primary/10 font-medium text-primary'
                                                : 'border-transparent text-foreground hover:bg-muted',
                                        ]"
                                        @click="applyFilter({ brand: filters.brand === brand.slug ? undefined : brand.slug })"
                                    >
                                        {{ brand.name }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Clear all -->
                        <div class="border-t border-border px-4 py-4">
                            <button
                                class="w-full rounded-full border border-border py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:border-destructive hover:text-destructive"
                                @click="clearAll"
                            >
                                Hapus Semua Filter
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </GuestLayout>
</template>

<style scoped>
.drawer-enter-active,
.drawer-leave-active {
    transition: opacity 0.2s ease;
}
.drawer-enter-active .relative,
.drawer-leave-active .relative {
    transition: transform 0.25s ease;
}
.drawer-enter-from,
.drawer-leave-to {
    opacity: 0;
}
</style>

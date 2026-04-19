<script setup lang="ts">
import ProductCard from '@/components/catalog/ProductCard.vue';
import SkeletonCard from '@/components/shared/SkeletonCard.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Check, ChevronDown, SlidersHorizontal, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Product {
    hash: string;
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
    category?: string; // comma-separated slugs
    brand?: string;    // comma-separated slugs
    sort?: string;
    promo?: string;
}

const props = defineProps<{
    products: PaginatedProducts;
    categories: FilterOption[];
    brands: FilterOption[];
    filters: Filters;
}>();

// ── Helpers ──────────────────────────────────────────────────────────────────
const parseSlugs = (val?: string) =>
    val ? val.split(',').filter(Boolean) : [];

const joinSlugs = (arr: string[]) => arr.length ? arr.join(',') : undefined;

// ── State ─────────────────────────────────────────────────────────────────────
const drawerOpen   = ref(false);
const loading      = ref(false);
const sortOpen     = ref(false);

const activeSort   = ref(props.filters.sort ?? 'newest');
const activeSearch = ref(props.filters.search ?? '');

// Draft selections inside the drawer (applied only on "Terapkan")
const draftCategories = ref<string[]>(parseSlugs(props.filters.category));
const draftBrands     = ref<string[]>(parseSlugs(props.filters.brand));
const draftPromo      = ref(!!props.filters.promo);

// Search within drawer lists
const catSearch   = ref('');
const brandSearch = ref('');

let searchTimer: ReturnType<typeof setTimeout>;

// ── Computed ──────────────────────────────────────────────────────────────────
const sortOptions = [
    { value: 'newest',     label: 'Terbaru' },
    { value: 'price_asc',  label: 'Harga Terendah' },
    { value: 'price_desc', label: 'Harga Tertinggi' },
];

const activeSortLabel = computed(
    () => sortOptions.find((o) => o.value === activeSort.value)?.label ?? 'Pilih Sortir',
);

// Active (committed) slugs from URL
const activeCategories = computed(() => parseSlugs(props.filters.category));
const activeBrands     = computed(() => parseSlugs(props.filters.brand));

const activeFiltersCount = computed(() =>
    activeCategories.value.length + activeBrands.value.length + (props.filters.promo ? 1 : 0),
);

// Pending count in drawer (not yet applied)
const pendingCount = computed(() =>
    draftCategories.value.length + draftBrands.value.length + (draftPromo.value ? 1 : 0),
);

const filteredCategories = computed(() =>
    props.categories.filter(c => c.name.toLowerCase().includes(catSearch.value.toLowerCase())),
);
const filteredBrands = computed(() =>
    props.brands.filter(b => b.name.toLowerCase().includes(brandSearch.value.toLowerCase())),
);

const pageTitle = computed(() => {
    if (props.filters.search) return `Hasil pencarian: "${props.filters.search}"`;
    if (activeCategories.value.length === 1) {
        const cat = props.categories.find(c => c.slug === activeCategories.value[0]);
        return cat ? `Katalog ${cat.name}` : 'Katalog';
    }
    if (activeCategories.value.length > 1) return 'Katalog Produk';
    if (activeBrands.value.length === 1) {
        const brand = props.brands.find(b => b.slug === activeBrands.value[0]);
        return brand ? `Produk ${brand.name}` : 'Katalog';
    }
    if (activeBrands.value.length > 1) return 'Katalog Produk';
    return 'Katalog Produk';
});

// ── Actions ───────────────────────────────────────────────────────────────────
function navigate(params: Record<string, string | undefined>) {
    loading.value = true;
    router.get('/catalog', { ...props.filters, ...params, page: undefined }, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => { loading.value = false; },
    });
}

function applyDrawer() {
    drawerOpen.value = false;
    navigate({
        category: joinSlugs(draftCategories.value),
        brand:    joinSlugs(draftBrands.value),
        promo:    draftPromo.value ? '1' : undefined,
    });
}

function openDrawer() {
    // Sync draft from current URL state
    draftCategories.value = parseSlugs(props.filters.category);
    draftBrands.value     = parseSlugs(props.filters.brand);
    draftPromo.value      = !!props.filters.promo;
    catSearch.value       = '';
    brandSearch.value     = '';
    drawerOpen.value      = true;
}

function toggleDraftCategory(slug: string) {
    const idx = draftCategories.value.indexOf(slug);
    draftCategories.value = idx === -1
        ? [...draftCategories.value, slug]
        : draftCategories.value.filter(s => s !== slug);
}

function toggleDraftBrand(slug: string) {
    const idx = draftBrands.value.indexOf(slug);
    draftBrands.value = idx === -1
        ? [...draftBrands.value, slug]
        : draftBrands.value.filter(s => s !== slug);
}

function removeCategoryChip(slug: string) {
    navigate({ category: joinSlugs(activeCategories.value.filter(s => s !== slug)) });
}

function removeBrandChip(slug: string) {
    navigate({ brand: joinSlugs(activeBrands.value.filter(s => s !== slug)) });
}

function clearAll() {
    loading.value = true;
    router.get('/catalog', {}, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => { loading.value = false; drawerOpen.value = false; },
    });
}

function selectSort(value: string) {
    activeSort.value = value;
    sortOpen.value = false;
}

watch(activeSort, (val) => navigate({ sort: val }));

function onSearchInput() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => navigate({ search: activeSearch.value || undefined }), 400);
}
</script>

<template>
    <Head>
        <title>{{ pageTitle }} — GG Case Store</title>
        <meta name="description" :content="`Temukan ${pageTitle.toLowerCase()} di GG Case Store Samarinda. Aksesoris HP, earphone, charger, casing, kartu memori terbaik.`" />
        <link rel="canonical" href="/catalog" />
        <meta property="og:type" content="website" />
        <meta property="og:title" :content="`${pageTitle} — GG Case Store`" />
        <meta property="og:description" :content="`Temukan ${pageTitle.toLowerCase()} di GG Case Store Samarinda.`" />
        <meta property="og:url" content="/catalog" />
        <meta v-if="filters.search || filters.category || filters.brand || filters.promo" name="robots" content="noindex, follow" />
    </Head>

    <GuestLayout>
        <div class="mx-auto max-w-6xl px-4 py-6">

            <!-- ── Header ── -->
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-xl font-bold text-foreground">{{ pageTitle }}</h1>
                    <p class="mt-0.5 text-sm text-muted-foreground">{{ products.total }} produk ditemukan</p>
                </div>

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
                    <div class="relative">
                        <button
                            class="flex h-9 items-center gap-1.5 rounded-full border border-border bg-muted px-3 text-sm transition-colors hover:border-primary"
                            @click="sortOpen = !sortOpen"
                        >
                            <span>{{ activeSortLabel }}</span>
                            <ChevronDown :size="14" :class="sortOpen ? 'rotate-180' : ''" class="transition-transform" />
                        </button>
                        <div v-if="sortOpen" class="absolute right-0 top-10 z-30 min-w-40 overflow-hidden rounded-xl border border-border bg-background shadow-lg">
                            <button
                                v-for="opt in sortOptions"
                                :key="opt.value"
                                class="w-full px-4 py-2.5 text-left text-sm transition-colors hover:bg-muted"
                                :class="activeSort === opt.value ? 'font-semibold text-primary' : 'text-foreground'"
                                @click="selectSort(opt.value)"
                            >
                                {{ opt.label }}
                            </button>
                        </div>
                        <div v-if="sortOpen" class="fixed inset-0 z-20" @click="sortOpen = false" />
                    </div>

                    <!-- Filter toggle -->
                    <button
                        class="relative flex h-9 items-center gap-1.5 rounded-full border border-border bg-muted px-3 text-sm transition-colors hover:border-primary"
                        @click="openDrawer"
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
                    v-for="slug in activeCategories"
                    :key="`cat-${slug}`"
                    class="flex items-center gap-1 rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary"
                    @click="removeCategoryChip(slug)"
                >
                    {{ categories.find(c => c.slug === slug)?.name ?? slug }}
                    <X :size="12" />
                </button>

                <button
                    v-for="slug in activeBrands"
                    :key="`brand-${slug}`"
                    class="flex items-center gap-1 rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary"
                    @click="removeBrandChip(slug)"
                >
                    {{ brands.find(b => b.slug === slug)?.name ?? slug }}
                    <X :size="12" />
                </button>

                <button
                    v-if="filters.promo"
                    class="flex items-center gap-1 rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary"
                    @click="navigate({ promo: undefined })"
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
                <div v-if="products.data.length === 0" class="flex flex-col items-center justify-center py-24 text-center">
                    <svg class="mb-5 h-28 w-28 text-muted-foreground/30" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M40 80 L100 50 L160 80 L160 150 L100 180 L40 150 Z" stroke="currentColor" stroke-width="5" stroke-linejoin="round" fill="none"/>
                        <path d="M40 80 L100 110 L160 80" stroke="currentColor" stroke-width="5" stroke-linejoin="round" fill="none"/>
                        <path d="M100 110 L100 180" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                        <path d="M100 50 L100 20" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                        <path d="M70 65 L70 35" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                        <path d="M130 65 L130 35" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                        <circle cx="148" cy="148" r="22" stroke="currentColor" stroke-width="5" fill="none"/>
                        <path d="M164 164 L178 178" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
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
                        :key="product.hash"
                        :hash="product.hash"
                        :name="product.name"
                        :slug="product.slug"
                        :price="product.price"
                        :promo-price="product.promo_price"
                        :is-promo-active="product.is_promo_active"
                        :first-photo="product.first_photo"
                        :brand-name="product.brand_name"
                    />
                </div>

                <!-- Pagination -->
                <div v-if="products.last_page > 1" class="mt-8 flex items-center justify-center gap-1">
                    <Link
                        v-for="link in products.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        :class="[
                            'flex h-9 min-w-9 items-center justify-center rounded-lg border px-3 text-sm transition-colors',
                            link.active ? 'border-primary bg-primary font-semibold text-white' : 'border-border bg-card text-foreground hover:border-primary hover:text-primary',
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
                    <div class="absolute inset-0 bg-black/50" @click="drawerOpen = false" />

                    <div class="relative flex h-full w-72 flex-col bg-background shadow-xl">
                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-border px-4 py-4">
                            <span class="font-semibold text-foreground">Filter Produk</span>
                            <button class="rounded-full p-1 hover:bg-muted" @click="drawerOpen = false">
                                <X :size="18" />
                            </button>
                        </div>

                        <!-- Scrollable body -->
                        <div class="flex-1 space-y-6 overflow-y-auto px-4 py-5">

                            <!-- Promo -->
                            <div>
                                <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Penawaran</h3>
                                <button
                                    :class="[
                                        'flex w-full items-center justify-between rounded-lg border px-4 py-2.5 text-left text-sm font-medium transition-colors',
                                        draftPromo ? 'border-primary bg-primary/10 text-primary' : 'border-border text-foreground hover:border-primary',
                                    ]"
                                    @click="draftPromo = !draftPromo"
                                >
                                    <span>Produk Promo</span>
                                    <Check v-if="draftPromo" :size="14" class="text-primary" />
                                </button>
                            </div>

                            <!-- Categories -->
                            <div>
                                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                    Kategori
                                    <span v-if="draftCategories.length" class="ml-1 rounded-full bg-primary px-1.5 py-0.5 text-[10px] font-bold text-white">
                                        {{ draftCategories.length }}
                                    </span>
                                </h3>
                                <!-- Search within categories -->
                                <input
                                    v-model="catSearch"
                                    type="search"
                                    placeholder="Cari kategori..."
                                    class="mb-2 h-8 w-full rounded-lg border border-border bg-muted px-3 text-xs outline-none focus:border-primary"
                                />
                                <div class="space-y-1">
                                    <button
                                        v-for="cat in filteredCategories"
                                        :key="cat.id"
                                        :class="[
                                            'flex w-full items-center justify-between rounded-lg border px-3 py-2 text-left text-sm transition-colors',
                                            draftCategories.includes(cat.slug)
                                                ? 'border-primary bg-primary/10 font-medium text-primary'
                                                : 'border-transparent text-foreground hover:bg-muted',
                                        ]"
                                        @click="toggleDraftCategory(cat.slug)"
                                    >
                                        <span>{{ cat.name }}</span>
                                        <span
                                            :class="[
                                                'flex h-4 w-4 shrink-0 items-center justify-center rounded border transition-colors',
                                                draftCategories.includes(cat.slug)
                                                    ? 'border-primary bg-primary text-white'
                                                    : 'border-border',
                                            ]"
                                        >
                                            <Check v-if="draftCategories.includes(cat.slug)" :size="10" />
                                        </span>
                                    </button>
                                    <p v-if="filteredCategories.length === 0" class="py-2 text-center text-xs text-muted-foreground">Tidak ditemukan.</p>
                                </div>
                            </div>

                            <!-- Brands -->
                            <div>
                                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                    Brand
                                    <span v-if="draftBrands.length" class="ml-1 rounded-full bg-primary px-1.5 py-0.5 text-[10px] font-bold text-white">
                                        {{ draftBrands.length }}
                                    </span>
                                </h3>
                                <!-- Search within brands -->
                                <input
                                    v-model="brandSearch"
                                    type="search"
                                    placeholder="Cari brand..."
                                    class="mb-2 h-8 w-full rounded-lg border border-border bg-muted px-3 text-xs outline-none focus:border-primary"
                                />
                                <div class="space-y-1">
                                    <button
                                        v-for="brand in filteredBrands"
                                        :key="brand.id"
                                        :class="[
                                            'flex w-full items-center justify-between rounded-lg border px-3 py-2 text-left text-sm transition-colors',
                                            draftBrands.includes(brand.slug)
                                                ? 'border-primary bg-primary/10 font-medium text-primary'
                                                : 'border-transparent text-foreground hover:bg-muted',
                                        ]"
                                        @click="toggleDraftBrand(brand.slug)"
                                    >
                                        <span>{{ brand.name }}</span>
                                        <span
                                            :class="[
                                                'flex h-4 w-4 shrink-0 items-center justify-center rounded border transition-colors',
                                                draftBrands.includes(brand.slug)
                                                    ? 'border-primary bg-primary text-white'
                                                    : 'border-border',
                                            ]"
                                        >
                                            <Check v-if="draftBrands.includes(brand.slug)" :size="10" />
                                        </span>
                                    </button>
                                    <p v-if="filteredBrands.length === 0" class="py-2 text-center text-xs text-muted-foreground">Tidak ditemukan.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer actions -->
                        <div class="space-y-2 border-t border-border px-4 py-4">
                            <button
                                class="w-full rounded-full bg-primary py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                                @click="applyDrawer"
                            >
                                Terapkan
                                <span v-if="pendingCount > 0" class="ml-1 opacity-80">({{ pendingCount }})</span>
                            </button>
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
.drawer-enter-from,
.drawer-leave-to {
    opacity: 0;
}
</style>

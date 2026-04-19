<script setup lang="ts">
/**
 * BannerRedirectPicker
 *
 * Lets the admin choose where a banner links to:
 *   - None (no redirect)
 *   - Product  → /product/{hash}
 *   - Category → /catalog?category={slug}
 *   - Brand    → /catalog?brand={slug}
 *
 * Emits a single redirect_url string (or empty string for none).
 */
import { Check, ChevronDown, Search, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface ProductOption  { id: number; hash: string; name: string }
interface CategoryOption { id: number; name: string; slug: string }
interface BrandOption    { id: number; name: string; slug: string }

const props = defineProps<{
    modelValue: string;                 // the redirect_url
    products:   ProductOption[];
    categories: CategoryOption[];
    brands:     BrandOption[];
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', url: string): void;
}>();

type RedirectType = 'none' | 'product' | 'category' | 'brand';

// ── Parse incoming URL back to type + selection ──────────────────────────────
function parseUrl(url: string): { type: RedirectType; slugs: string[] } {
    if (!url) return { type: 'none', slugs: [] };
    if (url.includes('/product/')) {
        const hash = url.split('/product/')[1];
        return { type: 'product', slugs: hash ? [hash] : [] };
    }
    if (url.includes('category=')) {
        // Format: /catalog?category=slug1,slug2
        const raw = new URLSearchParams(url.split('?')[1]).get('category') ?? '';
        return { type: 'category', slugs: raw.split(',').filter(Boolean) };
    }
    if (url.includes('brand=')) {
        // Format: /catalog?brand=slug1,slug2
        const raw = new URLSearchParams(url.split('?')[1]).get('brand') ?? '';
        return { type: 'brand', slugs: raw.split(',').filter(Boolean) };
    }
    return { type: 'none', slugs: [] };
}

const parsed = parseUrl(props.modelValue);
const redirectType  = ref<RedirectType>(parsed.type);
const selectedSlugs = ref<string[]>(parsed.slugs);
const search        = ref('');

// ── Filtered lists ────────────────────────────────────────────────────────────
const filteredProducts = computed(() =>
    props.products.filter(p => p.name.toLowerCase().includes(search.value.toLowerCase()))
);
const filteredCategories = computed(() =>
    props.categories.filter(c => c.name.toLowerCase().includes(search.value.toLowerCase()))
);
const filteredBrands = computed(() =>
    props.brands.filter(b => b.name.toLowerCase().includes(search.value.toLowerCase()))
);

// ── Build URL from selection ──────────────────────────────────────────────────
function buildUrl(): string {
    if (redirectType.value === 'none' || selectedSlugs.value.length === 0) return '';

    if (redirectType.value === 'product') {
        // Products don't support multi-filter in catalog; link directly to the product page
        return `/product/${selectedSlugs.value[0]}`;
    }
    if (redirectType.value === 'category') {
        // Matches catalog URL format: /catalog?category=slug1,slug2
        return `/catalog?category=${selectedSlugs.value.join(',')}`;
    }
    if (redirectType.value === 'brand') {
        // Matches catalog URL format: /catalog?brand=slug1,slug2
        return `/catalog?brand=${selectedSlugs.value.join(',')}`;
    }
    return '';
}

// ── Sync outward ─────────────────────────────────────────────────────────────
watch([redirectType, selectedSlugs], () => {
    emit('update:modelValue', buildUrl());
}, { deep: true });

// ── Toggle selection ─────────────────────────────────────────────────────────
function toggle(slug: string) {
    const idx = selectedSlugs.value.indexOf(slug);
    if (idx === -1) {
        // For category/brand allow multi; for product allow multi too (we'll use first)
        selectedSlugs.value = [...selectedSlugs.value, slug];
    } else {
        selectedSlugs.value = selectedSlugs.value.filter(s => s !== slug);
    }
}

function isSelected(slug: string) {
    return selectedSlugs.value.includes(slug);
}

function onTypeChange(type: RedirectType) {
    redirectType.value = type;
    selectedSlugs.value = [];
    search.value = '';
}

// ── Labels for selected chips ─────────────────────────────────────────────────
const selectedLabels = computed(() => {
    if (redirectType.value === 'product') {
        return selectedSlugs.value.map(hash => props.products.find(p => p.hash === hash)?.name ?? hash);
    }
    if (redirectType.value === 'category') {
        return selectedSlugs.value.map(slug => props.categories.find(c => c.slug === slug)?.name ?? slug);
    }
    if (redirectType.value === 'brand') {
        return selectedSlugs.value.map(slug => props.brands.find(b => b.slug === slug)?.name ?? slug);
    }
    return [];
});

const typeLabels: Record<RedirectType, string> = {
    none: 'Tidak ada redirect',
    product: 'Produk',
    category: 'Kategori',
    brand: 'Brand',
};
</script>

<template>
    <div class="space-y-3">
        <label class="text-sm font-medium text-foreground">Redirect Banner</label>

        <!-- Type selector tabs -->
        <div class="flex flex-wrap gap-2">
            <button
                v-for="t in (['none', 'product', 'category', 'brand'] as const)"
                :key="t"
                type="button"
                :class="[
                    'rounded-full border px-3 py-1.5 text-xs font-medium transition-colors',
                    redirectType === t
                        ? 'border-primary bg-primary text-white'
                        : 'border-border text-muted-foreground hover:border-primary hover:text-foreground',
                ]"
                @click="onTypeChange(t)"
            >
                {{ typeLabels[t] }}
            </button>
        </div>

        <!-- Selection panel -->
        <div v-if="redirectType !== 'none'" class="rounded-xl border border-border bg-background">
            <!-- Search -->
            <div class="relative border-b border-border">
                <Search :size="14" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" />
                <input
                    v-model="search"
                    type="search"
                    :placeholder="`Cari ${typeLabels[redirectType].toLowerCase()}…`"
                    class="h-9 w-full bg-transparent pl-8 pr-3 text-sm outline-none"
                />
            </div>

            <!-- List -->
            <div class="max-h-48 overflow-y-auto">
                <!-- Products -->
                <template v-if="redirectType === 'product'">
                    <button
                        v-for="p in filteredProducts"
                        :key="p.hash"
                        type="button"
                        class="flex w-full items-center justify-between px-3 py-2 text-left text-sm transition-colors hover:bg-muted"
                        @click="toggle(p.hash)"
                    >
                        <span :class="isSelected(p.hash) ? 'font-medium text-primary' : 'text-foreground'">{{ p.name }}</span>
                        <Check v-if="isSelected(p.hash)" :size="14" class="shrink-0 text-primary" />
                    </button>
                    <p v-if="filteredProducts.length === 0" class="px-3 py-4 text-center text-xs text-muted-foreground">Tidak ada hasil.</p>
                </template>

                <!-- Categories -->
                <template v-if="redirectType === 'category'">
                    <button
                        v-for="c in filteredCategories"
                        :key="c.slug"
                        type="button"
                        class="flex w-full items-center justify-between px-3 py-2 text-left text-sm transition-colors hover:bg-muted"
                        @click="toggle(c.slug)"
                    >
                        <span :class="isSelected(c.slug) ? 'font-medium text-primary' : 'text-foreground'">{{ c.name }}</span>
                        <Check v-if="isSelected(c.slug)" :size="14" class="shrink-0 text-primary" />
                    </button>
                    <p v-if="filteredCategories.length === 0" class="px-3 py-4 text-center text-xs text-muted-foreground">Tidak ada hasil.</p>
                </template>

                <!-- Brands -->
                <template v-if="redirectType === 'brand'">
                    <button
                        v-for="b in filteredBrands"
                        :key="b.slug"
                        type="button"
                        class="flex w-full items-center justify-between px-3 py-2 text-left text-sm transition-colors hover:bg-muted"
                        @click="toggle(b.slug)"
                    >
                        <span :class="isSelected(b.slug) ? 'font-medium text-primary' : 'text-foreground'">{{ b.name }}</span>
                        <Check v-if="isSelected(b.slug)" :size="14" class="shrink-0 text-primary" />
                    </button>
                    <p v-if="filteredBrands.length === 0" class="px-3 py-4 text-center text-xs text-muted-foreground">Tidak ada hasil.</p>
                </template>
            </div>
        </div>

        <!-- Selected chips -->
        <div v-if="selectedLabels.length > 0" class="flex flex-wrap gap-1.5">
            <span
                v-for="(label, i) in selectedLabels"
                :key="i"
                class="flex items-center gap-1 rounded-full bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary"
            >
                {{ label }}
                <button type="button" @click="toggle(selectedSlugs[i])">
                    <X :size="11" />
                </button>
            </span>
        </div>

        <!-- Resolved URL preview -->
        <p v-if="modelValue" class="truncate font-mono text-[11px] text-muted-foreground">
            → {{ modelValue }}
        </p>
    </div>
</template>

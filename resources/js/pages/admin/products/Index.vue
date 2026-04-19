<script setup lang="ts">
import FlashMessage from '@/components/admin/FlashMessage.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Eye, Package, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Produk', href: '/admin/products' },
];

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
    produk_dilihat: number;
}

interface PaginatedProducts {
    data: Product[];
    current_page: number;
    last_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface FilterOption { id: number; name: string }

const props = defineProps<{
    products: PaginatedProducts;
    categories: FilterOption[];
    brands: FilterOption[];
    filters: { search?: string; category?: string; brand?: string };
}>();

const formatRupiah = (v: number) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v);

const search = ref(props.filters.search ?? '');
const category = ref(props.filters.category ?? '');
const brand = ref(props.filters.brand ?? '');
let timer: ReturnType<typeof setTimeout>;

function applyFilter() {
    router.get(route('admin.products.index'), {
        search: search.value || undefined,
        category: category.value || undefined,
        brand: brand.value || undefined,
    }, { preserveScroll: true, preserveState: true });
}

function onSearchInput() {
    clearTimeout(timer);
    timer = setTimeout(applyFilter, 400);
}

const deleteForm = useForm({});
function confirmDelete(p: Product) {
    if (confirm(`Hapus produk "${p.name}"?`)) {
        deleteForm.delete(route('admin.products.destroy', p.id));
    }
}
</script>

<template>
    <Head title="Produk — Admin" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <FlashMessage />

            <!-- Header -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2">
                    <Package :size="20" class="text-primary" />
                    <h1 class="text-lg font-bold text-foreground">Produk</h1>
                    <span class="rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground">{{ products.total }}</span>
                </div>
                <Link
                    :href="route('admin.products.create')"
                    class="flex items-center gap-1.5 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                >
                    <Plus :size="15" /> Tambah Produk
                </Link>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-2">
                <input
                    v-model="search"
                    type="search"
                    placeholder="Cari produk..."
                    class="h-9 rounded-full border border-border bg-muted px-4 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                    @input="onSearchInput"
                />
                <select
                    v-model="category"
                    class="h-9 rounded-full border border-border bg-muted px-3 text-sm outline-none focus:border-primary"
                    @change="applyFilter"
                >
                    <option value="">Semua Kategori</option>
                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
                <select
                    v-model="brand"
                    class="h-9 rounded-full border border-border bg-muted px-3 text-sm outline-none focus:border-primary"
                    @change="applyFilter"
                >
                    <option value="">Semua Brand</option>
                    <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                </select>
            </div>

            <!-- Table -->
            <div class="rounded-xl border border-border bg-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-left text-xs font-medium uppercase tracking-wide text-muted-foreground">
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3">Brand / Kategori</th>
                                <th class="px-4 py-3 text-right">Harga</th>
                                <th class="px-4 py-3 text-center">Views</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr v-for="p in products.data" :key="p.id" class="transition-colors hover:bg-muted/40">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-muted">
                                            <img v-if="p.first_photo" :src="p.first_photo" :alt="p.name" class="h-full w-full object-cover" />
                                            <div v-else class="flex h-full w-full items-center justify-center"><Package :size="14" class="text-muted-foreground" /></div>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-foreground">{{ p.name }}</p>
                                            <p class="truncate font-mono text-xs text-muted-foreground">{{ p.slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs font-medium text-primary">{{ p.brand_name ?? '—' }}</p>
                                    <p class="text-xs text-muted-foreground">{{ p.category_name ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <p class="font-medium text-foreground">{{ formatRupiah(p.price) }}</p>
                                    <p v-if="p.is_promo_active && p.promo_price" class="text-xs text-green-600">
                                        Promo: {{ formatRupiah(p.promo_price) }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="flex items-center justify-center gap-1 text-xs text-muted-foreground">
                                        <Eye :size="12" /> {{ p.produk_dilihat.toLocaleString('id-ID') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link
                                            :href="route('admin.products.edit', p.id)"
                                            class="rounded-lg p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground"
                                        >
                                            <Pencil :size="15" />
                                        </Link>
                                        <button
                                            class="rounded-lg p-1.5 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                            @click="confirmDelete(p)"
                                        >
                                            <Trash2 :size="15" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="products.data.length === 0">
                                <td colspan="5" class="px-4 py-10 text-center text-muted-foreground">Tidak ada produk ditemukan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="products.last_page > 1" class="flex items-center justify-center gap-1 border-t border-border px-4 py-3">
                    <Link
                        v-for="link in products.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        :class="[
                            'flex h-8 min-w-8 items-center justify-center rounded-lg border px-2.5 text-xs transition-colors',
                            link.active ? 'border-primary bg-primary font-semibold text-white' : 'border-border bg-card text-foreground hover:border-primary hover:text-primary',
                            !link.url ? 'pointer-events-none opacity-40' : '',
                        ]"
                        preserve-scroll
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

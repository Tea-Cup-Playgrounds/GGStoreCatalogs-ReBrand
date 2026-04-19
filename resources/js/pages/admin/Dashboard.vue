<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Package, Layers, Tag, Users, TrendingUp, Eye } from 'lucide-vue-next';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin/dashboard' },
];

interface Stats {
    total_products: number;
    total_categories: number;
    total_brands: number;
    total_users: number;
    promo_products: number;
}

interface TopViewedProduct {
    id: number;
    name: string;
    produk_dilihat: number;
    price: number;
    first_photo: string | null;
    brand_name: string | null;
    category_name: string | null;
}

interface CategoryStat {
    name: string;
    count: number;
}

interface RecentProduct {
    id: number;
    name: string;
    price: number;
    brand_name: string | null;
    category_name: string | null;
    created_at: string;
}

const props = defineProps<{
    stats: Stats;
    topViewed: TopViewedProduct[];
    categoryStats: CategoryStat[];
    recentProducts: RecentProduct[];
}>();

const formatRupiah = (value: number) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);

const maxCategoryCount = computed(() => Math.max(...props.categoryStats.map((c) => c.count), 1));

const statCards = computed(() => [
    {
        label: 'Total Produk',
        value: props.stats.total_products,
        icon: Package,
        color: 'text-primary',
        bg: 'bg-primary/10',
    },
    {
        label: 'Kategori',
        value: props.stats.total_categories,
        icon: Layers,
        color: 'text-blue-500',
        bg: 'bg-blue-500/10',
    },
    {
        label: 'Brand',
        value: props.stats.total_brands,
        icon: Tag,
        color: 'text-purple-500',
        bg: 'bg-purple-500/10',
    },
    {
        label: 'Pengguna',
        value: props.stats.total_users,
        icon: Users,
        color: 'text-green-500',
        bg: 'bg-green-500/10',
    },
    {
        label: 'Produk Promo',
        value: props.stats.promo_products,
        icon: TrendingUp,
        color: 'text-orange-500',
        bg: 'bg-orange-500/10',
    },
]);
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">

            <!-- Stat Cards -->
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                <div
                    v-for="card in statCards"
                    :key="card.label"
                    class="flex flex-col gap-3 rounded-xl border border-border bg-card p-4"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-muted-foreground">{{ card.label }}</span>
                        <div :class="['rounded-lg p-2', card.bg]">
                            <component :is="card.icon" :size="16" :class="card.color" />
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-foreground">{{ card.value.toLocaleString('id-ID') }}</span>
                </div>
            </div>

            <!-- Main content grid -->
            <div class="grid gap-6 lg:grid-cols-3">

                <!-- Top Viewed Products -->
                <div class="lg:col-span-2 rounded-xl border border-border bg-card">
                    <div class="flex items-center justify-between border-b border-border px-5 py-4">
                        <h2 class="font-semibold text-foreground">Produk Paling Banyak Dilihat</h2>
                        <Eye :size="16" class="text-muted-foreground" />
                    </div>
                    <div class="divide-y divide-border">
                        <div
                            v-for="(product, index) in topViewed"
                            :key="product.id"
                            class="flex items-center gap-3 px-5 py-3"
                        >
                            <!-- Rank -->
                            <span
                                :class="[
                                    'flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                                    index === 0 ? 'bg-primary text-white' : 'bg-muted text-muted-foreground',
                                ]"
                            >
                                {{ index + 1 }}
                            </span>

                            <!-- Thumbnail -->
                            <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-muted">
                                <img
                                    v-if="product.first_photo"
                                    :src="product.first_photo"
                                    :alt="product.name"
                                    class="h-full w-full object-cover"
                                />
                                <div v-else class="flex h-full w-full items-center justify-center text-xs text-muted-foreground">
                                    <Package :size="14" />
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-foreground">{{ product.name }}</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ product.brand_name ?? '—' }} · {{ product.category_name ?? '—' }}
                                </p>
                            </div>

                            <!-- Views + Price -->
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-semibold text-foreground">{{ product.produk_dilihat.toLocaleString('id-ID') }} views</p>
                                <p class="text-xs text-muted-foreground">{{ formatRupiah(product.price) }}</p>
                            </div>
                        </div>

                        <div v-if="topViewed.length === 0" class="px-5 py-8 text-center text-sm text-muted-foreground">
                            Belum ada data produk.
                        </div>
                    </div>
                </div>

                <!-- Category Distribution -->
                <div class="rounded-xl border border-border bg-card">
                    <div class="border-b border-border px-5 py-4">
                        <h2 class="font-semibold text-foreground">Produk per Kategori</h2>
                    </div>
                    <div class="space-y-3 px-5 py-4">
                        <div v-for="cat in categoryStats" :key="cat.name" class="space-y-1">
                            <div class="flex items-center justify-between text-sm">
                                <span class="truncate text-foreground">{{ cat.name }}</span>
                                <span class="ml-2 shrink-0 font-semibold text-foreground">{{ cat.count }}</span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-muted">
                                <div
                                    class="h-full rounded-full bg-primary transition-all duration-500"
                                    :style="{ width: `${(cat.count / maxCategoryCount) * 100}%` }"
                                />
                            </div>
                        </div>

                        <div v-if="categoryStats.length === 0" class="py-4 text-center text-sm text-muted-foreground">
                            Belum ada kategori.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Products -->
            <div class="rounded-xl border border-border bg-card">
                <div class="flex items-center justify-between border-b border-border px-5 py-4">
                    <h2 class="font-semibold text-foreground">Produk Terbaru</h2>
                    <Link href="/admin/products" class="text-xs font-medium text-primary hover:underline">
                        Lihat Semua
                    </Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-left text-xs font-medium uppercase tracking-wide text-muted-foreground">
                                <th class="px-5 py-3">Produk</th>
                                <th class="px-5 py-3">Brand</th>
                                <th class="px-5 py-3">Kategori</th>
                                <th class="px-5 py-3 text-right">Harga</th>
                                <th class="px-5 py-3 text-right">Ditambahkan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="product in recentProducts"
                                :key="product.id"
                                class="transition-colors hover:bg-muted/50"
                            >
                                <td class="px-5 py-3 font-medium text-foreground">{{ product.name }}</td>
                                <td class="px-5 py-3 text-muted-foreground">{{ product.brand_name ?? '—' }}</td>
                                <td class="px-5 py-3 text-muted-foreground">{{ product.category_name ?? '—' }}</td>
                                <td class="px-5 py-3 text-right text-foreground">{{ formatRupiah(product.price) }}</td>
                                <td class="px-5 py-3 text-right text-muted-foreground">{{ product.created_at }}</td>
                            </tr>
                            <tr v-if="recentProducts.length === 0">
                                <td colspan="5" class="px-5 py-8 text-center text-muted-foreground">
                                    Belum ada produk.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Brand {
    id: number;
    name: string;
    slug: string;
    brand_photo: string | null;
    products_count: number;
}

defineProps<{ brands: Brand[] }>();
</script>

<template>
    <Head>
        <title>Brands — GG Case Store</title>
        <meta name="description" content="Temukan semua brand aksesoris HP dan gadget yang tersedia di GG Case Store Samarinda." />
    </Head>

    <GuestLayout>
        <div class="mx-auto max-w-6xl px-4 py-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-foreground">Brands</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ brands.length }} brand tersedia di GG Case Store
                </p>
            </div>

            <!-- Brand grid -->
            <div
                v-if="brands.length > 0"
                class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
            >
                <Link
                    v-for="brand in brands"
                    :key="brand.id"
                    :href="`/catalog?brand=${brand.slug}`"
                    class="group flex flex-col overflow-hidden rounded-2xl border border-border bg-card transition-all hover:border-primary hover:shadow-lg"
                >
                    <!-- Logo area -->
                    <div class="flex aspect-square w-full items-center justify-center overflow-hidden bg-white p-6">
                        <img
                            v-if="brand.brand_photo"
                            :src="brand.brand_photo"
                            :alt="`Logo ${brand.name}`"
                            width="200"
                            height="200"
                            loading="lazy"
                            class="h-full w-full object-contain transition-transform duration-300 group-hover:scale-110"
                        />
                        <!-- Fallback: brand initial -->
                        <span
                            v-else
                            class="text-4xl font-black text-muted-foreground/30 transition-colors group-hover:text-primary/40"
                        >
                            {{ brand.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>

                    <!-- Name + count -->
                    <div class="border-t border-border px-3 py-3 text-center">
                        <p class="truncate text-sm font-semibold text-foreground group-hover:text-primary">
                            {{ brand.name }}
                        </p>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            {{ brand.products_count }} produk
                        </p>
                    </div>
                </Link>
            </div>

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center justify-center py-32 text-center">
                <svg class="mb-4 h-20 w-20 text-muted-foreground/25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                <p class="text-base font-semibold text-foreground">Belum ada brand</p>
                <p class="mt-1 text-sm text-muted-foreground">Brand akan muncul di sini setelah ditambahkan.</p>
            </div>

        </div>
    </GuestLayout>
</template>

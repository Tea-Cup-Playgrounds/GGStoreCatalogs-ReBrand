<script setup lang="ts">
import ProductCard from '@/components/catalog/ProductCard.vue';
import SkeletonCard from '@/components/shared/SkeletonCard.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { useWishlist } from '@/composables/useWishlist';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ShoppingBag, Trash2 } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

interface WishlistProduct {
    hash: string;
    name: string;
    slug: string;
    price: number;
    promo_price: number | null;
    is_promo_active: boolean;
    first_photo: string | null;
    brand_name: string | null;
}

const { wishlist, toggle } = useWishlist();
const products = ref<WishlistProduct[]>([]);
const loading = ref(false);

async function fetchProducts() {
    if (wishlist.value.length === 0) {
        products.value = [];
        return;
    }

    loading.value = true;
    try {
        const { data } = await axios.post('/api/wishlist/products', {
            hashes: wishlist.value,
        });
        products.value = data;
    } catch {
        products.value = [];
    } finally {
        loading.value = false;
    }
}

// Fetch on mount and whenever the wishlist changes
onMounted(fetchProducts);
watch(wishlist, fetchProducts, { deep: true });

function clearAll() {
    wishlist.value = [];
}
</script>

<template>
    <Head>
        <title>Wishlist — GG Case Store</title>
        <meta name="robots" content="noindex, nofollow" />
    </Head>

    <GuestLayout>
        <div class="mx-auto max-w-6xl px-4 py-8">

            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Wishlist</h1>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        {{ wishlist.length }} produk tersimpan
                    </p>
                </div>
                <button
                    v-if="wishlist.length > 0"
                    class="flex items-center gap-1.5 rounded-full border border-border px-4 py-2 text-sm text-muted-foreground transition-colors hover:border-destructive hover:text-destructive"
                    @click="clearAll"
                >
                    <Trash2 :size="15" />
                    Hapus Semua
                </button>
            </div>

            <!-- Loading skeletons -->
            <div v-if="loading" class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                <SkeletonCard v-for="n in wishlist.length || 4" :key="n" />
            </div>

            <!-- Product grid -->
            <div
                v-else-if="products.length > 0"
                class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
            >
                <ProductCard
                    v-for="product in products"
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

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center justify-center py-32 text-center">
                <div class="mb-5 flex h-24 w-24 items-center justify-center rounded-full bg-muted">
                    <ShoppingBag :size="40" class="text-muted-foreground/40" />
                </div>
                <p class="text-lg font-semibold text-foreground">Wishlist masih kosong</p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Tambahkan produk favorit kamu dengan menekan ikon ❤️ di kartu produk.
                </p>
                <Link
                    href="/catalog"
                    class="mt-5 rounded-full bg-primary px-6 py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                >
                    Jelajahi Katalog
                </Link>
            </div>

        </div>
    </GuestLayout>
</template>

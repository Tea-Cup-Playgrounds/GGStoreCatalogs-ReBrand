<script setup lang="ts">
import PriceDisplay from '@/components/shared/PriceDisplay.vue';
import { useWishlist } from '@/composables/useWishlist';
import { Link } from '@inertiajs/vue3';
import { Heart } from 'lucide-vue-next';

const props = defineProps<{
    id: number;
    name: string;
    slug: string;
    price: number;
    promoPrice?: number | null;
    isPromoActive?: boolean;
    firstPhoto?: string | null;
    brandName?: string | null;
}>();

const { isWishlisted, toggle } = useWishlist();
const wishlisted = isWishlisted(props.id);
</script>

<template>
    <div class="group relative overflow-hidden rounded-xl border border-border bg-card transition-shadow hover:shadow-md">
        <!-- Wishlist button -->
        <button
            class="absolute right-2 top-2 z-10 rounded-full bg-white/80 p-1.5 backdrop-blur-sm transition-colors hover:bg-white"
            :aria-label="wishlisted ? 'Hapus dari wishlist' : 'Tambah ke wishlist'"
            @click.prevent="toggle(id)"
        >
            <Heart
                :size="16"
                :class="wishlisted ? 'fill-red-500 text-red-500' : 'text-muted-foreground'"
            />
        </button>

        <!-- Promo badge -->
        <span
            v-if="isPromoActive"
            class="absolute left-2 top-2 z-10 rounded bg-primary px-1.5 py-0.5 text-[10px] font-bold uppercase text-white"
        >
            Promo
        </span>

        <Link :href="route('product.show', { id: `${id}-${slug}` })">
            <!-- Image -->
            <div class="aspect-square overflow-hidden bg-muted">
                <img
                    v-if="firstPhoto"
                    :src="firstPhoto"
                    :alt="`${name} - GG Case Store`"
                    width="300"
                    height="300"
                    loading="lazy"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                />
                <div v-else class="flex h-full w-full items-center justify-center text-muted-foreground">
                    <span class="text-xs">No Image</span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-3">
                <p v-if="brandName" class="mb-0.5 text-[11px] font-medium uppercase tracking-wide text-primary">
                    {{ brandName }}
                </p>
                <h3 class="mb-2 line-clamp-2 text-sm font-medium leading-snug text-foreground">
                    {{ name }}
                </h3>
                <PriceDisplay :price="price" :promo-price="promoPrice" :is-promo-active="isPromoActive" />
            </div>
        </Link>
    </div>
</template>

<script setup lang="ts">
import { useFormatRupiah } from '@/composables/useFormatRupiah';

const props = defineProps<{
    price: number;
    promoPrice?: number | null;
    isPromoActive?: boolean;
}>();

const { formatRupiah, discountPercent } = useFormatRupiah();
</script>

<template>
    <div v-if="isPromoActive && promoPrice" class="flex flex-col gap-0.5">
        <div class="flex items-center gap-2">
            <span class="text-2xl font-bold text-primary">{{ formatRupiah(promoPrice) }}</span>
            <span class="rounded bg-primary px-1.5 py-0.5 text-[10px] font-bold text-white">
                {{ discountPercent(price, promoPrice) }}% OFF
            </span>
        </div>
        <span class="text-xl text-muted-foreground line-through">{{ formatRupiah(price) }}</span>
    </div>
    <span v-else class="text-xl font-bold text-foreground">{{ formatRupiah(price) }}</span>
</template>

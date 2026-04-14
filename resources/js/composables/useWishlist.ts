import { useLocalStorage } from '@vueuse/core';
import { computed } from 'vue';

export function useWishlist() {
    const wishlist = useLocalStorage<number[]>('gg-wishlist', []);

    const isWishlisted = (id: number) => computed(() => wishlist.value.includes(id));

    const toggle = (id: number) => {
        const idx = wishlist.value.indexOf(id);
        if (idx === -1) {
            wishlist.value = [...wishlist.value, id];
        } else {
            wishlist.value = wishlist.value.filter((i) => i !== id);
        }
    };

    return { wishlist, isWishlisted, toggle };
}

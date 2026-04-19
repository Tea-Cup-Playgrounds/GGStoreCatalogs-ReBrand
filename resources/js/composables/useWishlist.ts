import { useLocalStorage } from '@vueuse/core';
import { computed } from 'vue';

export function useWishlist() {
    const wishlist = useLocalStorage<string[]>('gg-wishlist', []);

    const isWishlisted = (hash: string) => computed(() => wishlist.value.includes(hash));

    const toggle = (hash: string) => {
        const idx = wishlist.value.indexOf(hash);
        if (idx === -1) {
            wishlist.value = [...wishlist.value, hash];
        } else {
            wishlist.value = wishlist.value.filter((h) => h !== hash);
        }
    };

    return { wishlist, isWishlisted, toggle };
}

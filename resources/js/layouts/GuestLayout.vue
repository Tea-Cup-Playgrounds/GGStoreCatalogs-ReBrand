<script setup lang="ts">
import AppFooter from '@/components/shared/AppFooter.vue';
import { useAppearance } from '@/composables/useAppearance';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Home, Moon, Search, ShoppingBag, Sun, Tag, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const page = usePage();
const searchOpen = ref(false);
const searchQuery = ref('');
const { appearance, updateAppearance } = useAppearance();

const isDark = computed(() => appearance.value === 'dark');

const toggleTheme = () => updateAppearance(isDark.value ? 'light' : 'dark');

const navLinks = [
    { label: 'Home', href: '/', icon: Home },
    { label: 'Katalog', href: '/catalog', icon: BookOpen },
    { label: 'Brands', href: '/brands', icon: Tag },
];

const submitSearch = () => {
    if (searchQuery.value.trim()) {
        window.location.href = `/catalog?search=${encodeURIComponent(searchQuery.value.trim())}`;
    }
};
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background">
        <!-- Top Navigation -->
        <header class="sticky top-0 z-50 border-b border-border bg-background/95 backdrop-blur-sm">
            <div class="mx-auto flex h-14 max-w-6xl items-center justify-between gap-4 px-4">
                <!-- Logo -->
                <Link href="/" class="flex shrink-0 items-center gap-2.5">
                    <img
                        src="/images/logoCatalog.png"
                        alt="GG Case Store Logo"
                        width="32"
                        height="32"
                        class="h-8 w-8 rounded object-contain"
                    />
                    <span class="text-base font-bold tracking-tight text-foreground">
                        Case Store
                    </span>
                </Link>

                <!-- Desktop nav links -->
                <nav class="hidden items-center gap-6 md:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                        :class="{ 'text-primary font-semibold': page.url === link.href }"
                    >
                        {{ link.label }}
                    </Link>
                </nav>

                <!-- Search + Wishlist -->
                <div class="flex items-center gap-2">
                    <!-- Search bar (desktop) -->
                    <form class="hidden md:flex" @submit.prevent="submitSearch">
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <input
                                v-model="searchQuery"
                                type="search"
                                placeholder="Cari produk..."
                                class="h-9 w-52 rounded-full border border-border bg-muted pl-9 pr-4 text-sm outline-none transition-all focus:w-64 focus:border-primary focus:ring-1 focus:ring-primary"
                            />
                        </div>
                    </form>

                    <!-- Mobile search toggle -->
                    <button class="rounded-full p-2 hover:bg-muted md:hidden" @click="searchOpen = !searchOpen">
                        <component :is="searchOpen ? X : Search" :size="20" />
                    </button>

                    <!-- Theme toggle -->
                    <button
                        class="rounded-full p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                        @click="toggleTheme"
                    >
                        <Sun v-if="isDark" :size="20" />
                        <Moon v-else :size="20" />
                    </button>

                    <!-- Wishlist -->
                    <Link href="/wishlist" class="rounded-full p-2 hover:bg-muted" aria-label="Wishlist">
                        <ShoppingBag :size="20" />
                    </Link>
                </div>
            </div>

            <!-- Mobile search bar -->
            <div v-if="searchOpen" class="border-t border-border px-4 py-2 md:hidden">
                <form @submit.prevent="submitSearch">
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input
                            v-model="searchQuery"
                            type="search"
                            placeholder="Cari produk..."
                            autofocus
                            class="h-10 w-full rounded-full border border-border bg-muted pl-9 pr-4 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                        />
                    </div>
                </form>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 pb-20 md:pb-0">
            <slot />
        </main>

        <!-- Footer -->
        <AppFooter />

        <!-- Bottom mobile navigation -->
        <nav class="fixed bottom-0 left-0 right-0 z-50 border-t border-border bg-background md:hidden">
            <div class="flex items-center justify-around py-2">
                <Link
                    v-for="link in navLinks"
                    :key="link.href"
                    :href="link.href"
                    class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs text-muted-foreground transition-colors"
                    :class="{ 'text-primary font-semibold': page.url === link.href }"
                >
                    <component :is="link.icon" :size="20" />
                    {{ link.label }}
                </Link>
                <Link
                    href="/wishlist"
                    class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs text-muted-foreground transition-colors"
                    :class="{ 'text-primary font-semibold': page.url === '/wishlist' }"
                >
                    <ShoppingBag :size="20" />
                    Wishlist
                </Link>
            </div>
        </nav>
    </div>
</template>

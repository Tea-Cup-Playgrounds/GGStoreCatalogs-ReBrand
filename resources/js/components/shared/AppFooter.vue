<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const socialLinks = [
    {
        label: 'GG GROUP Entertainment',
        href: 'https://www.youtube.com/@ggcasegroupentertainment?si=qtgdwvn3h4jYXq7j',
        icon: '/images/social/youtube.png',
        alt: 'YouTube Icon',
    },
    {
        label: 'ggcasegroup.id',
        href: 'https://www.instagram.com/ggcasegroup.id?igsh=NnB2MDh2eGl4bWVk',
        icon: '/images/social/instagram.png',
        alt: 'Instagram Icon',
    },
    {
        label: 'ggaccindonesia',
        href: 'https://www.tiktok.com/@ggaccindonesia?_t=ZS-8ydq3i7CD2I&_r=1',
        icon: '/images/social/tiktok.png',
        alt: 'TikTok Icon',
    },
    {
        label: 'GGCASE GROUP SAMARINDA',
        href: 'https://www.tokopedia.com/ggcasegroups',
        icon: '/images/social/tokopedia.png',
        alt: 'Tokopedia Icon',
    },
    {
        label: 'GG CASE GROUP',
        href: 'https://shopee.co.id/ggcasegroup',
        icon: '/images/social/shopee.png',
        alt: 'Shopee Icon',
    },
];

const quickLinks = [
    { label: 'Home', href: '/' },
    { label: 'Katalog', href: '/catalog' },
    { label: 'Brands', href: '/brands' },
    { label: 'Wishlist', href: '/wishlist' },
];
</script>

<template>
    <footer class="border-t border-[#323232] bg-[#000000] text-[#444]">
        <!-- Main grid -->
        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-10 px-4 py-10 md:grid-cols-3">

            <!-- Col 1 — Logo + description -->
            <div>
                <Link href="/" class="mb-4 inline-flex items-center gap-3">
                    <img
                        src="/images/logoCatalog.png"
                        alt="GG Case Store Logo"
                        width="40"
                        height="40"
                        class="h-10 w-10 rounded-md object-contain"
                    />
                    <span class="text-lg font-bold tracking-tight text-white">
                        Case Store
                    </span>
                </Link>
                <p class="text-sm leading-relaxed text-[#d8d8d8]">
                    Solusi lengkap elektronik dan aksesoris, hanya di GG Case.
                    Belanja mudah, cepat, dan aman.
                </p>
            </div>

            <!-- Col 2 — Quick links -->
            <div>
                <h2 class="mb-4 text-base font-semibold text-white">Quick Links</h2>
                <ul class="space-y-2">
                    <li v-for="link in quickLinks" :key="link.href">
                        <Link
                            :href="link.href"
                            class="text-sm text-[#cccccc] transition-colors hover:text-[#e6b120]"
                        >
                            {{ link.label }}
                        </Link>
                    </li>
                </ul>
            </div>

            <!-- Col 3 — Contact / social -->
            <div>
                <h2 class="mb-4 text-base font-semibold text-white">Contact Information</h2>
                <ul class="space-y-3">
                    <li v-for="social in socialLinks" :key="social.href">
                        <a
                            :href="social.href"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-2.5 text-sm text-[#cccccc] transition-colors hover:text-[#e6b120]"
                        >
                            <img
                                :src="social.icon"
                                :alt="social.alt"
                                width="20"
                                height="20"
                                class="h-5 w-5 shrink-0 object-contain"
                            />
                            {{ social.label }}
                        </a>
                    </li>
                </ul>

                <!-- Address -->
                <div class="mt-6">
                    <h2 class="mb-3 text-base font-semibold text-white">Find Us</h2>
                    <a
                        href="https://linktr.ee/qwerty30aja"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-start gap-3 text-sm text-[#cccccc] transition-colors hover:text-[#e6b120]"
                    >
                        <img
                            src="/images/social/gmaps.png"
                            alt="Google Maps Icon"
                            width="40"
                            height="40"
                            class="h-10 w-10 shrink-0 object-contain"
                        />
                        <span class="leading-snug">GG CASE GROUP<br /><span class="text-xs text-[#888]">Samarinda, Kalimantan Timur</span></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="border-t border-[#323232] px-4 py-5 text-center text-sm text-[#999]">
            © {{ new Date().getFullYear() }} GG Case Store. All rights reserved.
        </div>
    </footer>
</template>

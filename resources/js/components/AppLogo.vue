<script setup lang="ts">
interface Props {
    class?: string;
}
defineProps<Props>();
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center overflow-hidden rounded-md">
        <img
            src="/images/logoCatalog.png"
            alt="GG Case Store"
            class="size-6 object-contain"
        />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate font-semibold leading-none">GG Case Store</span>
        <span class="truncate text-xs text-sidebar-foreground/60">Admin Panel</span>
    </div>
</template>

<script setup lang="ts">
defineProps<{ size?: 'sm' | 'md' | 'lg' }>();
</script>

<template>
    <span
        :class="[
            'font-bold tracking-tight text-foreground',
            size === 'sm' ? 'text-lg' : size === 'lg' ? 'text-3xl' : 'text-xl',
        ]"
    >
        <span class="text-primary">GG</span> Case Store
    </span>
</template>

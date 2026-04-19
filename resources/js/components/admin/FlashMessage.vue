<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { CheckCircle, XCircle } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const success = computed(() => (page.props as any).flash?.success as string | undefined);
const error   = computed(() => (page.props as any).flash?.error   as string | undefined);
</script>

<template>
    <Transition name="fade">
        <div
            v-if="success"
            class="mb-4 flex items-center gap-2 rounded-lg bg-green-500/10 px-4 py-3 text-sm font-medium text-green-600"
        >
            <CheckCircle :size="16" class="shrink-0" />
            {{ success }}
        </div>
    </Transition>
    <Transition name="fade">
        <div
            v-if="error"
            class="mb-4 flex items-center gap-2 rounded-lg bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive"
        >
            <XCircle :size="16" class="shrink-0" />
            {{ error }}
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

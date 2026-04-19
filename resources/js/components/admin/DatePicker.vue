<script setup lang="ts">
/**
 * DatePicker — v-calendar based date picker styled to match the design system.
 * modelValue is a YYYY-MM-DD string (matches Laravel date format).
 */
import { DatePicker as VCalendarDatePicker } from 'v-calendar';
import 'v-calendar/style.css';
import { CalendarDays, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    modelValue: string | null;
    placeholder?: string;
    minDate?: string;
    maxDate?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | null): void;
}>();

// v-calendar works with Date objects internally
const internalDate = computed({
    get(): Date | null {
        if (!props.modelValue) { return null; }
        // Parse YYYY-MM-DD without timezone shift
        const [y, m, d] = props.modelValue.split('-').map(Number);
        return new Date(y, m - 1, d);
    },
    set(val: Date | null) {
        if (!val) {
            emit('update:modelValue', null);
            return;
        }
        // Format back to YYYY-MM-DD
        const y = val.getFullYear();
        const m = String(val.getMonth() + 1).padStart(2, '0');
        const d = String(val.getDate()).padStart(2, '0');
        emit('update:modelValue', `${y}-${m}-${d}`);
    },
});

const displayValue = computed(() => {
    if (!props.modelValue) { return ''; }
    const [y, m, d] = props.modelValue.split('-').map(Number);
    return new Date(y, m - 1, d).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
});

function clear() {
    emit('update:modelValue', null);
}
</script>

<template>
    <VCalendarDatePicker
        v-model="internalDate"
        :min-date="minDate ?? undefined"
        :max-date="maxDate ?? undefined"
        :popover="{ placement: 'bottom-start', visibility: 'click' }"
        color="yellow"
        locale="id"
    >
        <template #default="{ togglePopover }">
            <div class="relative flex items-center">
                <button
                    type="button"
                    class="flex h-9 w-full items-center gap-2 rounded-lg border border-border bg-background px-3 text-sm transition-colors hover:border-primary focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                    @click="togglePopover"
                >
                    <CalendarDays :size="15" class="shrink-0 text-muted-foreground" />
                    <span :class="displayValue ? 'text-foreground' : 'text-muted-foreground'">
                        {{ displayValue || placeholder || 'Pilih tanggal' }}
                    </span>
                </button>
                <button
                    v-if="modelValue"
                    type="button"
                    class="absolute right-2 rounded p-0.5 text-muted-foreground hover:text-foreground"
                    @click.stop="clear"
                >
                    <X :size="13" />
                </button>
            </div>
        </template>
    </VCalendarDatePicker>
</template>

<style>
/* Override v-calendar to match the design system CSS variables */
:root {
    --vc-font-family: inherit;
    --vc-rounded-full: 9999px;

    --vc-bg: hsl(var(--card));
    --vc-border: hsl(var(--border));
    --vc-text: hsl(var(--foreground));
    --vc-text-lg: hsl(var(--foreground));
    --vc-muted: hsl(var(--muted-foreground));

    --vc-header-arrow-color: hsl(var(--foreground));
    --vc-header-title-color: hsl(var(--foreground));

    --vc-weekday-color: hsl(var(--muted-foreground));

    --vc-day-content-hover-bg: hsl(var(--muted));
    --vc-day-content-disabled-color: hsl(var(--muted-foreground));

    /* Use primary (gold) for selected day */
    --vc-accent-50:  hsl(42 80% 97%);
    --vc-accent-100: hsl(42 80% 93%);
    --vc-accent-200: hsl(42 80% 85%);
    --vc-accent-300: hsl(42 80% 75%);
    --vc-accent-400: hsl(42 80% 65%);
    --vc-accent-500: hsl(var(--primary));
    --vc-accent-600: hsl(42 80% 44%);
    --vc-accent-700: hsl(42 80% 37%);
    --vc-accent-800: hsl(42 80% 30%);
    --vc-accent-900: hsl(42 80% 22%);
}

.dark {
    --vc-bg: hsl(var(--card));
    --vc-border: hsl(var(--border));
    --vc-text: hsl(var(--foreground));
    --vc-muted: hsl(var(--muted-foreground));
    --vc-day-content-hover-bg: hsl(var(--muted));
}

/* Popover container */
.vc-popover-content-wrapper {
    z-index: 9999 !important;
}

.vc-container {
    border-radius: var(--radius) !important;
    border-color: hsl(var(--border)) !important;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
    font-family: inherit !important;
}
</style>

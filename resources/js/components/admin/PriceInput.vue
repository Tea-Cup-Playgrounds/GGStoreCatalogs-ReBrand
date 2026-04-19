<script setup lang="ts">
/**
 * PriceInput — number input that displays Indonesian-formatted values.
 * Shows "150.000" while the form value stays as the raw number 150000.
 *
 * Usage:
 *   <PriceInput v-model="form.price" />
 */
import { ref, watch } from 'vue';

const props = withDefaults(defineProps<{
    modelValue: number | null;
    placeholder?: string;
    required?: boolean;
    min?: number;
}>(), {
    placeholder: '0',
    required: false,
    min: 0,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: number | null): void;
}>();

const isFocused = ref(false);

// Format a number as Indonesian thousands-separated string: 150000 → "150.000"
function format(val: number | null): string {
    if (val === null || val === undefined || isNaN(val)) { return ''; }
    return val.toLocaleString('id-ID');
}

// Strip all non-digit characters and parse back to number
function parse(str: string): number | null {
    const digits = str.replace(/\D/g, '');
    if (digits === '') { return null; }
    return parseInt(digits, 10);
}

// The displayed string value
const displayValue = ref(format(props.modelValue));

// Keep display in sync when modelValue changes externally (e.g. form.reset())
watch(() => props.modelValue, (val) => {
    if (!isFocused.value) {
        displayValue.value = format(val);
    }
});

function onFocus() {
    isFocused.value = true;
}

function onInput(e: Event) {
    const input = e.target as HTMLInputElement;
    const digitsOnly = input.value.replace(/\D/g, '');
    const parsed = parse(digitsOnly);

    // Reformat with dots live, preserve cursor at end
    const formatted = format(parsed);
    displayValue.value = formatted;
    input.value = formatted;

    emit('update:modelValue', parsed);
}

function onBlur() {
    isFocused.value = false;
    // Ensure final value is clean
    const parsed = parse(displayValue.value);
    emit('update:modelValue', parsed);
    displayValue.value = format(parsed);
}
</script>

<template>
    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-muted-foreground">
            Rp
        </span>
        <input
            :value="displayValue"
            inputmode="numeric"
            :placeholder="placeholder"
            :required="required"
            class="h-9 w-full rounded-lg border border-border bg-background pl-9 pr-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
            @focus="onFocus"
            @input="onInput"
            @blur="onBlur"
        />
    </div>
</template>

<script setup lang="ts">
import FlashMessage from '@/components/admin/FlashMessage.vue';
import ImageUpload from '@/components/admin/ImageUpload.vue';
import DatePicker from '@/components/admin/DatePicker.vue';
import PriceInput from '@/components/admin/PriceInput.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface SelectOption { id: number; name: string }

interface ProductData {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    brand_id: number | null;
    category_id: number | null;
    price: number;
    promo_price: number | null;
    promo_price_start_date: string | null;
    promo_price_end_date: string | null;
    photos: { id: number; photo_url: string }[];
    variants: string[];
}

const props = defineProps<{
    product?: ProductData;
    categories: SelectOption[];
    brands: SelectOption[];
}>();

const isEdit = computed(() => !!props.product);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Produk', href: '/admin/products' },
    { title: isEdit.value ? 'Edit Produk' : 'Tambah Produk', href: '#' },
]);

const form = useForm({
    name:                   props.product?.name ?? '',
    slug:                   props.product?.slug ?? '',
    description:            props.product?.description ?? '',
    brand_id:               props.product?.brand_id ?? null as number | null,
    category_id:            props.product?.category_id ?? null as number | null,
    price:                  props.product?.price ?? 0,
    promo_price:            props.product?.promo_price ?? null as number | null,
    promo_price_start_date: props.product?.promo_price_start_date ?? null as string | null,
    promo_price_end_date:   props.product?.promo_price_end_date ?? null as string | null,
    variants:               props.product?.variants ?? [] as string[],
    photos:                 props.product?.photos.map(p => p.photo_url) ?? [] as string[],
});

// Variants helpers
const addVariant  = () => form.variants.push('');
const removeVariant = (i: number) => form.variants.splice(i, 1);

// Photos helpers
const addPhoto = () => form.photos.push('');

// ── Promo price mode ──────────────────────────────────────────────────────────
const promoMode = ref<'price' | 'percent'>('price');
const discountPercent = ref<number | null>(null);

// In price mode we track the ratio so changing base price keeps promo proportional
const savedRatio = ref<number | null>(null);

// Watch base price live — Inertia form fields are plain props, not refs,
// so we must use a getter function in watch() to track them reactively.
watch(
    () => form.price,
    (newPrice) => {
        if (!newPrice || newPrice <= 0) { return; }

        if (promoMode.value === 'percent' && discountPercent.value !== null) {
            // Recalculate promo from the locked percentage
            form.promo_price = Math.round(newPrice * (1 - discountPercent.value / 100));
        } else if (promoMode.value === 'price' && savedRatio.value !== null) {
            // Reapply the saved ratio
            form.promo_price = Math.round(newPrice * savedRatio.value);
        }
    },
);

// When user manually edits the promo price in price mode, save the new ratio
function onPromoPriceChange(val: number | null) {
    form.promo_price = val;
    savedRatio.value = (val !== null && form.price > 0) ? val / form.price : null;
}

function switchMode(mode: 'price' | 'percent') {
    promoMode.value = mode;
    discountPercent.value = null;
    savedRatio.value = null;
    form.promo_price = null;
}

// When percent changes, recalculate immediately
watch(discountPercent, (pct) => {
    if (promoMode.value === 'percent' && pct !== null && form.price > 0) {
        form.promo_price = Math.round(form.price * (1 - pct / 100));
    }
});

const promoSavings = computed(() => {
    if (!form.promo_price || !form.price || form.promo_price >= form.price) { return null; }
    const saved = form.price - form.promo_price;
    const pct = promoMode.value === 'percent' && discountPercent.value !== null
        ? discountPercent.value
        : Math.round((saved / form.price) * 100);
    return { saved, pct };
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('admin.products.update', props.product!.id), {
            onSuccess: () => {},
        });
    } else {
        form.post(route('admin.products.store'), {
            onSuccess: () => {},
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Produk — Admin' : 'Tambah Produk — Admin'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl p-4 md:p-6">
            <FlashMessage />

            <form @submit.prevent="submit" class="space-y-6">

                <!-- Basic Info -->
                <div class="rounded-xl border border-border bg-card p-5">
                    <h2 class="mb-4 font-semibold text-foreground">Informasi Dasar</h2>
                    <div class="space-y-4">

                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-foreground">Nama Produk <span class="text-destructive">*</span></label>
                            <input v-model="form.name" type="text" required class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary" />
                            <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-1.5">
                                <label class="text-sm font-medium text-foreground">Brand</label>
                                <select v-model="form.brand_id" class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:border-primary">
                                    <option :value="null">— Pilih Brand —</option>
                                    <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-medium text-foreground">Kategori</label>
                                <select v-model="form.category_id" class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:border-primary">
                                    <option :value="null">— Pilih Kategori —</option>
                                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-foreground">Deskripsi</label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                            />
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="rounded-xl border border-border bg-card p-5">
                    <h2 class="mb-4 font-semibold text-foreground">Harga</h2>
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-foreground">Harga Normal (Rp) <span class="text-destructive">*</span></label>
                            <PriceInput v-model="form.price" :required="true" />
                            <p v-if="form.errors.price" class="text-xs text-destructive">{{ form.errors.price }}</p>
                        </div>
                        <!-- Promo price -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-foreground">
                                    Harga Promo <span class="text-xs text-muted-foreground">(opsional)</span>
                                </label>
                                <!-- Mode toggle -->
                                <div class="flex rounded-lg border border-border bg-muted p-0.5 text-xs font-medium">
                                    <button
                                        type="button"
                                        :class="['rounded-md px-3 py-1 transition-colors', promoMode === 'price' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground']"
                                        @click="switchMode('price')"
                                    >
                                        Harga
                                    </button>
                                    <button
                                        type="button"
                                        :class="['rounded-md px-3 py-1 transition-colors', promoMode === 'percent' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground']"
                                        @click="switchMode('percent')"
                                    >
                                        % Diskon
                                    </button>
                                </div>
                            </div>

                            <!-- Direct price input -->
                            <PriceInput
                                v-if="promoMode === 'price'"
                                :model-value="form.promo_price"
                                placeholder="Kosongkan jika tidak ada"
                                @update:model-value="onPromoPriceChange"
                            />

                            <!-- Percent discount input -->
                            <div v-else class="relative">
                                <input
                                    v-model.number="discountPercent"
                                    type="number"
                                    min="1"
                                    max="99"
                                    placeholder="Contoh: 20"
                                    class="h-9 w-full rounded-lg border border-border bg-background pl-3 pr-10 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                                />
                                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-sm text-muted-foreground">%</span>
                            </div>

                            <!-- Result preview -->
                            <div v-if="form.promo_price && form.price && form.promo_price < form.price" class="flex items-center gap-2 rounded-lg bg-green-50 px-3 py-2 dark:bg-green-950/30">
                                <span class="text-xs text-muted-foreground">Harga promo:</span>
                                <span class="text-sm font-semibold text-green-700 dark:text-green-400">
                                    Rp {{ form.promo_price.toLocaleString('id-ID') }}
                                </span>
                                <span v-if="promoSavings" class="ml-auto rounded-full bg-green-100 px-2 py-0.5 text-xs font-bold text-green-700 dark:bg-green-900/50 dark:text-green-400">
                                    -{{ promoSavings.pct }}%
                                </span>
                            </div>
                            <p v-else-if="promoMode === 'percent' && discountPercent && !form.price" class="text-xs text-muted-foreground">
                                Isi harga normal terlebih dahulu.
                            </p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-1.5">
                                <label class="text-sm font-medium text-foreground">Mulai Promo</label>
                                <DatePicker
                                    v-model="form.promo_price_start_date"
                                    placeholder="Pilih tanggal mulai"
                                    :max-date="form.promo_price_end_date || undefined"
                                />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-medium text-foreground">Akhir Promo</label>
                                <DatePicker
                                    v-model="form.promo_price_end_date"
                                    placeholder="Pilih tanggal akhir"
                                    :min-date="form.promo_price_start_date || undefined"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photos -->
                <div class="rounded-xl border border-border bg-card p-5">
                    <h2 class="mb-4 font-semibold text-foreground">Foto Produk</h2>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                        <div v-for="(_, i) in form.photos" :key="i">
                            <ImageUpload
                                v-model="form.photos[i]"
                                folder="products"
                                :label="`Foto ${i + 1}`"
                                hint="1:1 — 1200×1200 px"
                            />
                        </div>
                        <!-- Add slot — same size as upload cells -->
                        <button
                            v-if="form.photos.length < 8"
                            type="button"
                            class="flex aspect-square w-full flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-border text-muted-foreground transition-colors hover:border-primary hover:text-primary"
                            @click="addPhoto"
                        >
                            <Plus :size="22" />
                            <span class="text-xs font-medium">Tambah Foto</span>
                        </button>
                    </div>
                    <p v-if="form.photos.length === 0" class="mt-2 text-xs text-muted-foreground">Klik + untuk menambahkan foto produk.</p>
                </div>

                <!-- Variants -->
                <div class="rounded-xl border border-border bg-card p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="font-semibold text-foreground">Varian</h2>
                        <button type="button" class="flex items-center gap-1 text-xs font-medium text-primary hover:underline" @click="addVariant">
                            <Plus :size="13" /> Tambah Varian
                        </button>
                    </div>
                    <div class="space-y-2">
                        <div v-for="(_, i) in form.variants" :key="i" class="flex items-center gap-2">
                            <input
                                v-model="form.variants[i]"
                                type="text"
                                :placeholder="`Varian ${i + 1} (contoh: Hitam, 128GB)`"
                                class="h-9 flex-1 rounded-lg border border-border bg-background px-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                            />
                            <button type="button" class="shrink-0 rounded-lg p-1.5 text-muted-foreground hover:bg-destructive/10 hover:text-destructive" @click="removeVariant(i)">
                                <Trash2 :size="14" />
                            </button>
                        </div>
                        <p v-if="form.variants.length === 0" class="text-xs text-muted-foreground">Belum ada varian.</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('admin.products.index')" class="rounded-full border border-border px-5 py-2 text-sm hover:bg-muted">
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-full bg-primary px-6 py-2 text-sm font-semibold text-white hover:opacity-90 disabled:opacity-60"
                    >
                        {{ isEdit ? 'Simpan Perubahan' : 'Tambah Produk' }}
                    </button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

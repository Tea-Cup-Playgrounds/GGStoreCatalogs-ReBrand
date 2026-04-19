<script setup lang="ts">
import FlashMessage from '@/components/admin/FlashMessage.vue';
import ImageUpload from '@/components/admin/ImageUpload.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Tag, Trash2, X } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Brand', href: '/admin/brands' },
];

interface Brand {
    id: number;
    name: string;
    slug: string;
    brand_photo: string | null;
    products_count: number;
}

const props = defineProps<{ brands: Brand[] }>();

// ── Create ──
const showCreate = ref(false);
const createForm = useForm({ name: '', slug: '', brand_photo: '' });
const submitCreate = () =>
    createForm.post(route('admin.brands.store'), {
        onSuccess: () => { showCreate.value = false; createForm.reset(); },
    });

// ── Edit ──
const editing = ref<Brand | null>(null);
const editForm = useForm({ name: '', slug: '', brand_photo: '' });
const openEdit = (brand: Brand) => {
    editing.value = brand;
    editForm.name = brand.name;
    editForm.slug = brand.slug;
    editForm.brand_photo = brand.brand_photo ?? '';
};
const submitEdit = () =>
    editForm.put(route('admin.brands.update', editing.value!.id), {
        onSuccess: () => { editing.value = null; },
    });

// ── Delete ──
const deleteForm = useForm({});
const confirmDelete = (brand: Brand) => {
    if (confirm(`Hapus brand "${brand.name}"?`)) {
        deleteForm.delete(route('admin.brands.destroy', brand.id));
    }
};
</script>

<template>
    <Head title="Brand — Admin" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <FlashMessage />

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <Tag :size="20" class="text-primary" />
                    <h1 class="text-lg font-bold text-foreground">Brand</h1>
                    <span class="rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground">{{ brands.length }}</span>
                </div>
                <button
                    class="flex items-center gap-1.5 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                    @click="showCreate = true"
                >
                    <Plus :size="15" /> Tambah Brand
                </button>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                <div
                    v-for="brand in brands"
                    :key="brand.id"
                    class="group relative flex flex-col items-center gap-2 rounded-xl border border-border bg-card p-4 text-center transition-shadow hover:shadow-md"
                >
                    <div class="h-14 w-14 overflow-hidden rounded-xl bg-muted">
                        <img v-if="brand.brand_photo" :src="brand.brand_photo" :alt="brand.name" class="h-full w-full object-contain p-1" />
                        <div v-else class="flex h-full w-full items-center justify-center text-2xl">🏷️</div>
                    </div>
                    <p class="text-sm font-semibold text-foreground">{{ brand.name }}</p>
                    <p class="text-xs text-muted-foreground">{{ brand.products_count }} produk</p>
                    <div class="absolute right-2 top-2 flex gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                        <button class="rounded-lg bg-card p-1.5 shadow hover:bg-muted" @click="openEdit(brand)"><Pencil :size="13" /></button>
                        <button class="rounded-lg bg-card p-1.5 shadow hover:bg-destructive/10 hover:text-destructive" @click="confirmDelete(brand)"><Trash2 :size="13" /></button>
                    </div>
                </div>
                <div v-if="brands.length === 0" class="col-span-full py-10 text-center text-sm text-muted-foreground">
                    Belum ada brand.
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <Teleport to="body">
            <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" @click="showCreate = false" />
                <div class="relative w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-xl">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="font-semibold text-foreground">Tambah Brand</h2>
                        <button class="rounded-full p-1 hover:bg-muted" @click="showCreate = false"><X :size="16" /></button>
                    </div>
                    <form @submit.prevent="submitCreate" class="space-y-4">
                        <ImageUpload
                            v-model="createForm.brand_photo"
                            folder="brands"
                            label="Logo Brand"
                            hint="Rekomendasi: 1200×1200 px (rasio 1:1)"
                        />
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-foreground">Nama <span class="text-destructive">*</span></label>
                            <input v-model="createForm.name" type="text" required class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary" />
                            <p v-if="createForm.errors.name" class="text-xs text-destructive">{{ createForm.errors.name }}</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-foreground">Slug <span class="text-xs text-muted-foreground">(opsional)</span></label>
                            <input v-model="createForm.slug" type="text" class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary" />
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" class="rounded-full border border-border px-4 py-2 text-sm hover:bg-muted" @click="showCreate = false">Batal</button>
                            <button type="submit" :disabled="createForm.processing" class="rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90 disabled:opacity-60">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- Edit Modal -->
        <Teleport to="body">
            <div v-if="editing" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" @click="editing = null" />
                <div class="relative w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-xl">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="font-semibold text-foreground">Edit Brand</h2>
                        <button class="rounded-full p-1 hover:bg-muted" @click="editing = null"><X :size="16" /></button>
                    </div>
                    <form @submit.prevent="submitEdit" class="space-y-4">
                        <ImageUpload
                            v-model="editForm.brand_photo"
                            folder="brands"
                            label="Logo Brand"
                            hint="Rekomendasi: 1200×1200 px (rasio 1:1)"
                        />
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-foreground">Nama <span class="text-destructive">*</span></label>
                            <input v-model="editForm.name" type="text" required class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary" />
                            <p v-if="editForm.errors.name" class="text-xs text-destructive">{{ editForm.errors.name }}</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-foreground">Slug</label>
                            <input v-model="editForm.slug" type="text" class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary" />
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" class="rounded-full border border-border px-4 py-2 text-sm hover:bg-muted" @click="editing = null">Batal</button>
                            <button type="submit" :disabled="editForm.processing" class="rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90 disabled:opacity-60">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>

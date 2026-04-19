<script setup lang="ts">
import FlashMessage from '@/components/admin/FlashMessage.vue';
import ImageUpload from '@/components/admin/ImageUpload.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Layers, Pencil, Plus, Trash2, X } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Kategori', href: '/admin/categories' },
];

interface Category {
    id: number;
    name: string;
    slug: string;
    category_photo: string | null;
    products_count: number;
}

const props = defineProps<{ categories: Category[] }>();

// ── Create ──
const showCreate = ref(false);
const createForm = useForm({ name: '', slug: '', category_photo: '' });
const submitCreate = () =>
    createForm.post(route('admin.categories.store'), {
        onSuccess: () => { showCreate.value = false; createForm.reset(); },
    });

// ── Edit ──
const editing = ref<Category | null>(null);
const editForm = useForm({ name: '', slug: '', category_photo: '' });
const openEdit = (cat: Category) => {
    editing.value = cat;
    editForm.name = cat.name;
    editForm.slug = cat.slug;
    editForm.category_photo = cat.category_photo ?? '';
};
const submitEdit = () =>
    editForm.put(route('admin.categories.update', editing.value!.id), {
        onSuccess: () => { editing.value = null; },
    });

// ── Delete ──
const deleteForm = useForm({});
const confirmDelete = (cat: Category) => {
    if (confirm(`Hapus kategori "${cat.name}"? Produk terkait tidak akan dihapus.`)) {
        deleteForm.delete(route('admin.categories.destroy', cat.id));
    }
};
</script>

<template>
    <Head title="Kategori — Admin" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <FlashMessage />

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <Layers :size="20" class="text-primary" />
                    <h1 class="text-lg font-bold text-foreground">Kategori</h1>
                    <span class="rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground">{{ categories.length }}</span>
                </div>
                <button
                    class="flex items-center gap-1.5 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                    @click="showCreate = true"
                >
                    <Plus :size="15" /> Tambah Kategori
                </button>
            </div>

            <!-- Table -->
            <div class="rounded-xl border border-border bg-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-left text-xs font-medium uppercase tracking-wide text-muted-foreground">
                                <th class="px-5 py-3">Foto</th>
                                <th class="px-5 py-3">Nama</th>
                                <th class="px-5 py-3">Slug</th>
                                <th class="px-5 py-3 text-center">Produk</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr v-for="cat in categories" :key="cat.id" class="transition-colors hover:bg-muted/40">
                                <td class="px-5 py-3">
                                    <div class="h-10 w-10 overflow-hidden rounded-lg bg-muted">
                                        <img v-if="cat.category_photo" :src="cat.category_photo" :alt="cat.name" class="h-full w-full object-cover" />
                                        <div v-else class="flex h-full w-full items-center justify-center text-lg">📦</div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 font-medium text-foreground">{{ cat.name }}</td>
                                <td class="px-5 py-3 font-mono text-xs text-muted-foreground">{{ cat.slug }}</td>
                                <td class="px-5 py-3 text-center text-muted-foreground">{{ cat.products_count }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="rounded-lg p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground" @click="openEdit(cat)">
                                            <Pencil :size="15" />
                                        </button>
                                        <button class="rounded-lg p-1.5 text-muted-foreground hover:bg-destructive/10 hover:text-destructive" @click="confirmDelete(cat)">
                                            <Trash2 :size="15" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="categories.length === 0">
                                <td colspan="5" class="px-5 py-10 text-center text-muted-foreground">Belum ada kategori.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <Teleport to="body">
            <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" @click="showCreate = false" />
                <div class="relative w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-xl">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="font-semibold text-foreground">Tambah Kategori</h2>
                        <button class="rounded-full p-1 hover:bg-muted" @click="showCreate = false"><X :size="16" /></button>
                    </div>
                    <form @submit.prevent="submitCreate" class="space-y-4">
                        <ImageUpload
                            v-model="createForm.category_photo"
                            folder="categories"
                            label="Foto Kategori"
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
                        <h2 class="font-semibold text-foreground">Edit Kategori</h2>
                        <button class="rounded-full p-1 hover:bg-muted" @click="editing = null"><X :size="16" /></button>
                    </div>
                    <form @submit.prevent="submitEdit" class="space-y-4">
                        <ImageUpload
                            v-model="editForm.category_photo"
                            folder="categories"
                            label="Foto Kategori"
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

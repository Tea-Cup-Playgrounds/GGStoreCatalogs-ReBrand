<script setup lang="ts">
import BannerRedirectPicker from '@/components/admin/BannerRedirectPicker.vue';
import FlashMessage from '@/components/admin/FlashMessage.vue';
import ImageUpload from '@/components/admin/ImageUpload.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Image, Pencil, Plus, Trash2, ToggleLeft, ToggleRight, X } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Banner', href: '/admin/banners' },
];

interface Banner {
    id: number;
    title: string;
    banner_image_url: string;
    redirect_url: string | null;
    is_active: boolean;
    sort_order: number;
}
interface ProductOption  { id: number; hash: string; name: string }
interface CategoryOption { id: number; name: string; slug: string }
interface BrandOption    { id: number; name: string; slug: string }

const props = defineProps<{
    banners: Banner[];
    products: ProductOption[];
    categories: CategoryOption[];
    brands: BrandOption[];
}>();

// ── Create ──
const showCreate = ref(false);
const createForm = useForm({
    title: '', banner_image_url: '', redirect_url: '', is_active: true, sort_order: 0,
});
const submitCreate = () =>
    createForm.post(route('admin.banners.store'), {
        onSuccess: () => { showCreate.value = false; createForm.reset(); },
    });

// ── Edit ──
const editing = ref<Banner | null>(null);
const editForm = useForm({
    title: '', banner_image_url: '', redirect_url: '', is_active: true, sort_order: 0,
});
const openEdit = (b: Banner) => {
    editing.value = b;
    editForm.title = b.title;
    editForm.banner_image_url = b.banner_image_url;
    editForm.redirect_url = b.redirect_url ?? '';
    editForm.is_active = b.is_active;
    editForm.sort_order = b.sort_order;
};
const submitEdit = () =>
    editForm.put(route('admin.banners.update', editing.value!.id), {
        onSuccess: () => { editing.value = null; },
    });

// ── Delete ──
const deleteForm = useForm({});
const confirmDelete = (b: Banner) => {
    if (confirm(`Hapus banner "${b.title}"?`)) {
        deleteForm.delete(route('admin.banners.destroy', b.id));
    }
};
</script>

<template>
    <Head title="Banner — Admin" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <FlashMessage />

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <Image :size="20" class="text-primary" />
                    <h1 class="text-lg font-bold text-foreground">Banner</h1>
                    <span class="rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground">{{ banners.length }}</span>
                </div>
                <button
                    class="flex items-center gap-1.5 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                    @click="showCreate = true"
                >
                    <Plus :size="15" /> Tambah Banner
                </button>
            </div>

            <!-- Cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="banner in banners"
                    :key="banner.id"
                    class="group overflow-hidden rounded-xl border border-border bg-card"
                >
                    <div class="relative aspect-[3/1] overflow-hidden bg-muted">
                        <img :src="banner.banner_image_url" :alt="banner.title" class="h-full w-full object-cover" />
                        <span
                            :class="['absolute right-2 top-2 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase', banner.is_active ? 'bg-green-500 text-white' : 'bg-muted-foreground/60 text-white']"
                        >
                            {{ banner.is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="p-3">
                        <p class="truncate text-sm font-semibold text-foreground">{{ banner.title }}</p>
                        <p v-if="banner.redirect_url" class="mt-0.5 truncate font-mono text-xs text-muted-foreground">{{ banner.redirect_url }}</p>
                        <p class="mt-0.5 text-xs text-muted-foreground">Urutan: {{ banner.sort_order }}</p>
                        <div class="mt-3 flex gap-2">
                            <button class="flex flex-1 items-center justify-center gap-1 rounded-lg border border-border py-1.5 text-xs font-medium hover:bg-muted" @click="openEdit(banner)">
                                <Pencil :size="12" /> Edit
                            </button>
                            <button class="flex flex-1 items-center justify-center gap-1 rounded-lg border border-destructive/30 py-1.5 text-xs font-medium text-destructive hover:bg-destructive/10" @click="confirmDelete(banner)">
                                <Trash2 :size="12" /> Hapus
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="banners.length === 0" class="col-span-full py-10 text-center text-sm text-muted-foreground">
                    Belum ada banner.
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <Teleport to="body">
            <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" @click="showCreate = false" />
                <div class="relative w-full max-w-lg overflow-y-auto rounded-2xl border border-border bg-card p-6 shadow-xl" style="max-height:90vh">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="font-semibold text-foreground">Tambah Banner</h2>
                        <button class="rounded-full p-1 hover:bg-muted" @click="showCreate = false"><X :size="16" /></button>
                    </div>
                    <form @submit.prevent="submitCreate" class="space-y-4">
                        <ImageUpload
                            v-model="createForm.banner_image_url"
                            folder="banners"
                            label="Gambar Banner"
                            :crop-ratio="[3, 1]"
                            hint="Rekomendasi: 1200×400 px (rasio 3:1)"
                        />
                        <p v-if="createForm.errors.banner_image_url" class="text-xs text-destructive">{{ createForm.errors.banner_image_url }}</p>

                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-foreground">Judul <span class="text-destructive">*</span></label>
                            <input v-model="createForm.title" type="text" required class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary" />
                            <p v-if="createForm.errors.title" class="text-xs text-destructive">{{ createForm.errors.title }}</p>
                        </div>

                        <BannerRedirectPicker
                            v-model="createForm.redirect_url"
                            :products="products"
                            :categories="categories"
                            :brands="brands"
                        />

                        <div class="flex items-center gap-4">
                            <div class="flex-1 space-y-1.5">
                                <label class="text-sm font-medium text-foreground">Urutan</label>
                                <input v-model.number="createForm.sort_order" type="number" min="0" class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary" />
                            </div>
                            <label class="flex cursor-pointer items-center gap-2 pt-5">
                                <input v-model="createForm.is_active" type="checkbox" class="sr-only" />
                                <component :is="createForm.is_active ? ToggleRight : ToggleLeft" :size="28" :class="createForm.is_active ? 'text-primary' : 'text-muted-foreground'" />
                                <span class="text-sm font-medium text-foreground">Aktif</span>
                            </label>
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
                <div class="relative w-full max-w-lg overflow-y-auto rounded-2xl border border-border bg-card p-6 shadow-xl" style="max-height:90vh">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="font-semibold text-foreground">Edit Banner</h2>
                        <button class="rounded-full p-1 hover:bg-muted" @click="editing = null"><X :size="16" /></button>
                    </div>
                    <form @submit.prevent="submitEdit" class="space-y-4">
                        <ImageUpload
                            v-model="editForm.banner_image_url"
                            folder="banners"
                            label="Gambar Banner"
                            :crop-ratio="[3, 1]"
                            hint="Rekomendasi: 1200×400 px (rasio 3:1)"
                        />

                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-foreground">Judul <span class="text-destructive">*</span></label>
                            <input v-model="editForm.title" type="text" required class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary" />
                        </div>

                        <BannerRedirectPicker
                            v-model="editForm.redirect_url"
                            :products="products"
                            :categories="categories"
                            :brands="brands"
                        />

                        <div class="flex items-center gap-4">
                            <div class="flex-1 space-y-1.5">
                                <label class="text-sm font-medium text-foreground">Urutan</label>
                                <input v-model.number="editForm.sort_order" type="number" min="0" class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary" />
                            </div>
                            <label class="flex cursor-pointer items-center gap-2 pt-5">
                                <input v-model="editForm.is_active" type="checkbox" class="sr-only" />
                                <component :is="editForm.is_active ? ToggleRight : ToggleLeft" :size="28" :class="editForm.is_active ? 'text-primary' : 'text-muted-foreground'" />
                                <span class="text-sm font-medium text-foreground">Aktif</span>
                            </label>
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

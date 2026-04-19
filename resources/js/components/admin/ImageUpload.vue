<script setup lang="ts">
/**
 * ImageUpload — dropzone + crop modal uploader.
 *
 * Props:
 *   modelValue  — current image URL
 *   folder      — storage sub-folder: products | categories | brands | banners
 *   label       — field label
 *   cropRatio   — enforced crop ratio as [w, h], default [1, 1]
 *   hint        — recommended size hint text
 */
import axios from 'axios';
import { Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';
import { useDropzone } from 'vue3-dropzone';
import { Check, ImagePlus, Loader2, Trash2, X } from 'lucide-vue-next';
import { ref } from 'vue';

const props = withDefaults(defineProps<{
    modelValue: string;
    folder?: 'products' | 'categories' | 'brands' | 'banners';
    label?: string;
    cropRatio?: [number, number];
    hint?: string;
}>(), {
    folder: 'products',
    label: 'Foto',
    cropRatio: () => [1, 1],
    hint: '',
});

const emit = defineEmits<{
    (e: 'update:modelValue', url: string): void;
}>();

const uploading = ref(false);
const error     = ref('');
const cropSrc   = ref('');
const showCrop  = ref(false);
const cropper   = ref<any>(null);

// ── Dropzone ─────────────────────────────────────────────────────────────────
const { getRootProps, getInputProps, isDragActive } = useDropzone({
    accept: ['image/*'],
    maxFiles: 1,
    onDrop(accepted) {
        if (accepted[0]) { openCrop(accepted[0]); }
    },
});

// ── Crop flow ─────────────────────────────────────────────────────────────────
function openCrop(file: File) {
    error.value = '';
    const reader = new FileReader();
    reader.onload = (ev) => {
        cropSrc.value = ev.target?.result as string;
        showCrop.value = true;
    };
    reader.readAsDataURL(file);
}

function cancelCrop() {
    showCrop.value = false;
    cropSrc.value = '';
}

async function confirmCrop() {
    if (!cropper.value) { return; }

    const { canvas } = cropper.value.getResult();
    if (!canvas) { return; }

    showCrop.value = false;
    uploading.value = true;
    error.value = '';

    canvas.toBlob(async (blob: Blob | null) => {
        if (!blob) {
            error.value = 'Gagal memproses gambar.';
            uploading.value = false;
            return;
        }
        try {
            const fd = new FormData();
            fd.append('file', blob, 'image.jpg');
            fd.append('folder', props.folder);

            const { data } = await axios.post(route('admin.upload'), fd, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });

            emit('update:modelValue', data.url);
        } catch (e: any) {
            error.value = e?.response?.data?.message ?? 'Upload gagal.';
        } finally {
            uploading.value = false;
        }
    }, 'image/jpeg', 0.92);
}

function clear() {
    emit('update:modelValue', '');
}
</script>

<template>
    <div class="space-y-1.5">
        <label v-if="label" class="text-sm font-medium text-foreground">{{ label }}</label>

        <!-- ── Has image ── -->
        <div v-if="modelValue && !uploading" class="relative overflow-hidden rounded-xl border border-border bg-muted">
            <!-- Clicking the image re-opens file picker -->
            <div v-bind="getRootProps()" class="cursor-pointer">
                <input v-bind="getInputProps()" />
                <img :src="modelValue" alt="preview" class="w-full object-cover" />
            </div>
            <!-- Permanent red trash in top-right corner -->
            <button
                type="button"
                class="absolute right-2 top-2 rounded-full bg-destructive p-1.5 text-white shadow transition-opacity hover:opacity-80"
                aria-label="Hapus gambar"
                @click.stop="clear"
            >
                <Trash2 :size="13" />
            </button>
        </div>

        <!-- ── Uploading spinner ── -->
        <div v-else-if="uploading" class="flex flex-col items-center justify-center gap-2 rounded-xl border border-border bg-muted py-10 text-muted-foreground">
            <Loader2 :size="28" class="animate-spin text-primary" />
            <span class="text-xs">Mengupload…</span>
        </div>

        <!-- ── Empty dropzone ── -->
        <div
            v-else
            v-bind="getRootProps()"
            :class="[
                'flex cursor-pointer flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed py-8 transition-colors',
                isDragActive ? 'border-primary bg-primary/5' : 'border-border bg-muted/30 hover:border-primary/60 hover:bg-muted/60',
            ]"
        >
            <input v-bind="getInputProps()" />
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                <ImagePlus :size="22" class="text-muted-foreground" />
            </div>
            <div class="text-center">
                <p class="text-sm font-medium text-foreground">
                    {{ isDragActive ? 'Lepaskan gambar di sini' : 'Seret gambar ke sini' }}
                </p>
                <p class="mt-0.5 text-xs text-muted-foreground">atau klik untuk memilih file</p>
            </div>
            <span class="rounded-full border border-border bg-background px-3 py-1 text-xs text-muted-foreground">
                PNG, JPG, WEBP — maks. 4 MB
            </span>
        </div>

        <p v-if="hint" class="text-[11px] text-muted-foreground/70">{{ hint }}</p>
        <p v-if="error" class="text-xs text-destructive">{{ error }}</p>
    </div>

    <!-- ── Crop Modal ── -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="showCrop" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="cancelCrop" />

                <div class="relative flex w-full max-w-2xl flex-col gap-0 overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-border px-5 py-4">
                        <div>
                            <p class="font-semibold text-foreground">Crop Gambar</p>
                            <p class="text-xs text-muted-foreground">Rasio terkunci {{ cropRatio[0] }}:{{ cropRatio[1] }}</p>
                        </div>
                        <button type="button" class="rounded-full p-1.5 hover:bg-muted" @click="cancelCrop">
                            <X :size="16" />
                        </button>
                    </div>

                    <!-- Cropper -->
                    <div class="bg-neutral-950">
                        <Cropper
                            ref="cropper"
                            :src="cropSrc"
                            :stencil-props="{
                                aspectRatio: cropRatio[0] / cropRatio[1],
                                movable: true,
                                resizable: true,
                            }"
                            :default-size="{ width: '100%', height: '100%' }"
                            image-restriction="stencil"
                            class="max-h-[480px] w-full"
                        />
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-2 border-t border-border px-5 py-4">
                        <button
                            type="button"
                            class="flex items-center gap-1.5 rounded-full border border-border px-4 py-2 text-sm hover:bg-muted"
                            @click="cancelCrop"
                        >
                            <X :size="14" /> Batal
                        </button>
                        <button
                            type="button"
                            class="flex items-center gap-1.5 rounded-full bg-primary px-5 py-2 text-sm font-semibold text-primary-foreground hover:opacity-90"
                            @click="confirmCrop"
                        >
                            <Check :size="14" /> Gunakan Foto Ini
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

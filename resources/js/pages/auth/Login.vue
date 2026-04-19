<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login — GG Case Store" />

    <div class="flex min-h-screen flex-col items-center justify-center bg-background px-4 py-12">
        <div class="w-full max-w-sm">

            <!-- Logo -->
            <div class="mb-8 flex flex-col items-center gap-3">
                <Link :href="route('home')">
                    <img
                        src="/images/logoCatalog.png"
                        alt="GG Case Store"
                        width="48"
                        height="48"
                        class="h-12 w-12 rounded-xl object-contain"
                    />
                </Link>
                <div class="text-center">
                    <h1 class="text-xl font-bold text-foreground">Masuk ke Admin Panel</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Masukkan email dan password untuk melanjutkan</p>
                </div>
            </div>

            <!-- Status message (e.g. password reset success) -->
            <div v-if="status" class="mb-4 rounded-lg bg-green-500/10 px-4 py-3 text-center text-sm font-medium text-green-600">
                {{ status }}
            </div>

            <!-- Card -->
            <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <form @submit.prevent="submit" class="flex flex-col gap-5">

                    <div class="grid gap-1.5">
                        <Label for="email">Alamat Email</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            autofocus
                            tabindex="1"
                            autocomplete="email"
                            v-model="form.email"
                            placeholder="email@example.com"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-1.5">
                        <div class="flex items-center justify-between">
                            <Label for="password">Password</Label>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs text-muted-foreground hover:text-primary"
                                tabindex="5"
                            >
                                Lupa password?
                            </Link>
                        </div>
                        <Input
                            id="password"
                            type="password"
                            required
                            tabindex="2"
                            autocomplete="current-password"
                            v-model="form.password"
                            placeholder="Password"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <Label for="remember" class="flex cursor-pointer items-center gap-2.5">
                        <Checkbox id="remember" v-model:checked="form.remember" tabindex="3" />
                        <span class="text-sm text-muted-foreground">Ingat saya</span>
                    </Label>

                    <Button type="submit" class="w-full" tabindex="4" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Masuk
                    </Button>
                </form>
            </div>

            <!-- Back to store -->
            <p class="mt-6 text-center text-sm text-muted-foreground">
                <Link :href="route('home')" class="hover:text-primary">
                    ← Kembali ke toko
                </Link>
            </p>

        </div>
    </div>
</template>

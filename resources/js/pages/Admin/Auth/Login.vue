<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/components/AppLayout.vue';
import admin from '@/routes/admin';

const form = useForm({
    email: '',
    password: '',
});

const submit = (): void => {
    form.post(admin.login.url());
};
</script>

<template>
    <AppLayout kind="admin">
        <div class="mx-auto max-w-md">
            <h1 class="text-2xl font-bold text-white">ورود مدیران</h1>
            <p class="mt-1 text-sm text-slate-400">
                ورود به پنل مدیریت فالو‌بگیر.
            </p>

            <form
                class="mt-8 space-y-5 rounded-2xl border border-white/10 bg-slate-900/60 p-6"
                @submit.prevent="submit"
            >
                <div>
                    <label
                        for="email"
                        class="mb-2 block text-sm font-medium text-slate-200"
                    >
                        ایمیل
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        dir="ltr"
                        type="email"
                        required
                        autofocus
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-left text-slate-100 outline-none transition focus:border-indigo-500/60"
                    >
                    <p
                        v-if="form.errors.email"
                        class="mt-2 text-sm text-rose-400"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>

                <div>
                    <label
                        for="password"
                        class="mb-2 block text-sm font-medium text-slate-200"
                    >
                        رمز عبور
                    </label>
                    <input
                        id="password"
                        v-model="form.password"
                        dir="ltr"
                        type="password"
                        required
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-left text-slate-100 outline-none transition focus:border-indigo-500/60"
                    >
                    <p
                        v-if="form.errors.password"
                        class="mt-2 text-sm text-rose-400"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-xl bg-indigo-500 px-4 py-3 font-medium text-white transition hover:bg-indigo-400 disabled:opacity-50"
                >
                    ورود
                </button>
            </form>
        </div>
    </AppLayout>
</template>

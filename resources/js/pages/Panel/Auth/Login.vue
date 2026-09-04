<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/components/AppLayout.vue';
import AppLink from '@/components/AppLink.vue';
import AppSpinner from '@/components/AppSpinner.vue';
import panel from '@/routes/panel';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = (): void => {
    form.post(panel.login.url());
};
</script>

<template>
    <AppLayout kind="panel">
        <div class="mx-auto max-w-md">
            <h1 class="text-2xl font-bold text-white">ورود به پنل کاربری</h1>
            <p class="mt-1 text-sm text-slate-400">
                برای پیگیری سفارش‌ها و تکمیل خرید وارد شوید.
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
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-left text-slate-100 transition outline-none focus:border-indigo-500/60"
                    />
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
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-left text-slate-100 transition outline-none focus:border-indigo-500/60"
                    />
                    <p
                        v-if="form.errors.password"
                        class="mt-2 text-sm text-rose-400"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input
                        v-model="form.remember"
                        type="checkbox"
                        class="size-4 rounded border-white/20 bg-slate-950"
                    />
                    مرا به خاطر بسپار
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-500 px-4 py-3 font-medium text-white transition hover:bg-indigo-400 disabled:opacity-50"
                >
                    <AppSpinner v-if="form.processing" class="size-4" />
                    ورود
                </button>

                <p class="text-center text-sm text-slate-400">
                    حساب کاربری ندارید؟
                    <AppLink
                        :href="panel.register.url()"
                        class="font-medium text-indigo-400 hover:text-indigo-300"
                    >
                        ثبت‌نام کنید
                    </AppLink>
                </p>
            </form>
        </div>
    </AppLayout>
</template>

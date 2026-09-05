<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/components/AppLayout.vue';
import AppSpinner from '@/components/AppSpinner.vue';
import adminUsers from '@/routes/admin/users';
import type { UserRow } from '@/types/likeshow';

const props = defineProps<{ user: UserRow }>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    is_active: props.user.is_active,
    role: props.user.role ?? 'user',
});

const submit = (): void => {
    form.put(adminUsers.update.url(props.user));
};
</script>

<template>
    <AppLayout kind="admin">
        <h1 class="text-2xl font-bold text-white">
            ویرایش کاربر — {{ user.name }}
        </h1>

        <form
            class="mt-8 max-w-lg space-y-5 rounded-2xl border border-white/10 bg-slate-900/60 p-6"
            @submit.prevent="submit"
        >
            <div>
                <label
                    for="name"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    نام و نام خانوادگی
                </label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-slate-100 transition outline-none focus:border-indigo-500/60"
                />
                <p v-if="form.errors.name" class="mt-2 text-sm text-rose-400">
                    {{ form.errors.name }}
                </p>
            </div>

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
                    class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-left text-slate-100 transition outline-none focus:border-indigo-500/60"
                />
                <p v-if="form.errors.email" class="mt-2 text-sm text-rose-400">
                    {{ form.errors.email }}
                </p>
            </div>

            <div>
                <label
                    for="password"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    رمز عبور جدید
                    <span class="text-slate-500">(اختیاری)</span>
                </label>
                <input
                    id="password"
                    v-model="form.password"
                    dir="ltr"
                    type="password"
                    class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-left text-slate-100 transition outline-none focus:border-indigo-500/60"
                />
                <p
                    v-if="form.errors.password"
                    class="mt-2 text-sm text-rose-400"
                >
                    {{ form.errors.password }}
                </p>
            </div>

            <div>
                <label
                    for="password_confirmation"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    تکرار رمز عبور جدید
                </label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    dir="ltr"
                    type="password"
                    class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-left text-slate-100 transition outline-none focus:border-indigo-500/60"
                />
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="size-4 rounded border-white/20 bg-slate-950"
                />
                حساب فعال باشد
            </label>

            <div>
                <label
                    for="role"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    نقش
                </label>
                <select
                    id="role"
                    v-model="form.role"
                    class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-slate-100 transition outline-none focus:border-indigo-500/60"
                >
                    <option value="user">کاربر</option>
                    <option value="admin">مدیر</option>
                </select>
                <p v-if="form.errors.role" class="mt-2 text-sm text-rose-400">
                    {{ form.errors.role }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-500 px-4 py-3 font-medium text-white transition hover:bg-indigo-400 disabled:opacity-50"
            >
                <AppSpinner v-if="form.processing" class="size-4" />
                ذخیره تغییرات
            </button>
        </form>
    </AppLayout>
</template>

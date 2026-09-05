<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/components/AppLayout.vue';
import AppLink from '@/components/AppLink.vue';
import AppSpinner from '@/components/AppSpinner.vue';
import { formatDate, toFa } from '@/lib/ui';
import adminUsers from '@/routes/admin/users';
import type { Paginated, SharedProps, UserRow } from '@/types/likeshow';

const props = defineProps<{
    users: Paginated<UserRow>;
    filters: { q?: string | null };
}>();

const page = usePage();
const me = computed(() => (page.props as unknown as SharedProps).auth.user);

const search = ref<string>(props.filters.q ?? '');
const navigating = ref(false);
const pending = ref<number | null>(null);

const applySearch = (): void => {
    navigating.value = true;

    router.get(
        adminUsers.index.url(),
        search.value.trim() ? { q: search.value.trim() } : {},
        {
            preserveState: true,
            onFinish: () => {
                navigating.value = false;
            },
        },
    );
};

const toggle = (user: UserRow): void => {
    pending.value = user.id;

    router.patch(
        adminUsers.toggle.url(user),
        {},
        {
            onFinish: () => {
                pending.value = null;
            },
        },
    );
};

const destroy = (user: UserRow): void => {
    if (!window.confirm(`حساب «${user.name}» حذف شود؟`)) {
        return;
    }

    pending.value = user.id;

    router.delete(adminUsers.destroy.url(user), {
        onFinish: () => {
            pending.value = null;
        },
    });
};

const go = (target: number): void => {
    if (target < 1 || target > props.users.last_page || navigating.value) {
        return;
    }

    navigating.value = true;

    router.get(
        adminUsers.index.url(),
        {
            page: target,
            ...(search.value.trim() ? { q: search.value.trim() } : {}),
        },
        {
            onFinish: () => {
                navigating.value = false;
            },
        },
    );
};
</script>

<template>
    <AppLayout kind="admin">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-white">کاربران</h1>
            <AppLink
                :href="adminUsers.create.url()"
                class="rounded-xl bg-indigo-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-400"
            >
                کاربر جدید
            </AppLink>
        </div>

        <form class="mt-6 flex gap-2" @submit.prevent="applySearch">
            <input
                v-model="search"
                type="search"
                placeholder="جستجو در نام یا ایمیل..."
                class="w-full max-w-sm rounded-xl border border-white/10 bg-slate-950 px-4 py-2 text-sm text-slate-100 transition outline-none focus:border-indigo-500/60"
            />
            <button
                type="submit"
                :disabled="navigating"
                class="inline-flex items-center gap-2 rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-200 transition hover:border-indigo-500/50 disabled:opacity-50"
            >
                <AppSpinner v-if="navigating" class="size-4" />
                جستجو
            </button>
        </form>

        <div
            class="mt-4 overflow-x-auto rounded-2xl border border-white/10 bg-slate-900/60"
        >
            <table class="w-full text-right text-sm">
                <thead class="text-xs text-slate-400">
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 font-medium">نام</th>
                        <th class="px-4 py-3 font-medium">ایمیل</th>
                        <th class="px-4 py-3 font-medium">نقش‌ها</th>
                        <th class="px-4 py-3 font-medium">وضعیت</th>
                        <th class="px-4 py-3 font-medium">تاریخ عضویت</th>
                        <th class="px-4 py-3" />
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr v-for="user in users.data" :key="user.id">
                        <td class="px-4 py-3 text-slate-200">
                            {{ user.name }}
                        </td>
                        <td class="px-4 py-3 text-slate-300" dir="ltr">
                            {{ user.email }}
                        </td>
                        <td class="px-4 py-3">
                            <span
                                v-for="role in user.roles"
                                :key="role"
                                class="me-1 rounded-full bg-indigo-500/10 px-2.5 py-0.5 text-xs text-indigo-400 ring-1 ring-indigo-500/30 ring-inset"
                            >
                                {{ role === 'admin' ? 'مدیر' : role }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                :class="{
                                    'text-emerald-400': user.is_active,
                                    'text-rose-400': !user.is_active,
                                }"
                            >
                                {{ user.is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-400">
                            {{ formatDate(user.created_at) }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3 text-xs">
                                <button
                                    v-if="user.id !== me?.id"
                                    type="button"
                                    :disabled="pending === user.id"
                                    class="inline-flex items-center gap-1.5 text-amber-400 transition hover:text-amber-300 disabled:opacity-50"
                                    @click="toggle(user)"
                                >
                                    <AppSpinner
                                        v-if="pending === user.id"
                                        class="size-3"
                                    />
                                    {{ user.is_active ? 'غیرفعال' : 'فعال' }}
                                </button>
                                <AppLink
                                    :href="adminUsers.edit.url(user)"
                                    class="text-indigo-400 hover:text-indigo-300"
                                >
                                    ویرایش
                                </AppLink>
                                <button
                                    v-if="user.id !== me?.id"
                                    type="button"
                                    :disabled="pending === user.id"
                                    class="inline-flex items-center gap-1.5 text-rose-400 transition hover:text-rose-300 disabled:opacity-50"
                                    @click="destroy(user)"
                                >
                                    <AppSpinner
                                        v-if="pending === user.id"
                                        class="size-3"
                                    />
                                    حذف
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="users.last_page > 1"
            class="mt-6 flex items-center justify-center gap-3"
        >
            <button
                type="button"
                :disabled="users.current_page <= 1 || navigating"
                class="inline-flex items-center gap-2 rounded-lg border border-white/10 px-4 py-2 text-sm text-slate-300 transition disabled:opacity-40"
                @click="go(users.current_page - 1)"
            >
                <AppSpinner v-if="navigating" class="size-4" />
                قبلی
            </button>
            <span class="text-sm text-slate-400">
                صفحه {{ toFa(users.current_page) }} از
                {{ toFa(users.last_page) }}
            </span>
            <button
                type="button"
                :disabled="users.current_page >= users.last_page || navigating"
                class="inline-flex items-center gap-2 rounded-lg border border-white/10 px-4 py-2 text-sm text-slate-300 transition disabled:opacity-40"
                @click="go(users.current_page + 1)"
            >
                <AppSpinner v-if="navigating" class="size-4" />
                بعدی
            </button>
        </div>
    </AppLayout>
</template>

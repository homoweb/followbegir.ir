<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/components/AppLayout.vue';
import AppLink from '@/components/AppLink.vue';
import { formatDate, toFa } from '@/lib/ui';
import adminUsers from '@/routes/admin/users';
import type { Paginated, SharedProps, UserRow } from '@/types/followbegir';

const props = defineProps<{
    users: Paginated<UserRow>;
    filters: { q?: string | null };
}>();

const page = usePage();
const me = computed(() => (page.props as unknown as SharedProps).auth.user);

const search = ref<string>(props.filters.q ?? '');

const applySearch = (): void => {
    router.get(
        adminUsers.index.url(),
        search.value.trim() ? { q: search.value.trim() } : {},
        { preserveState: true },
    );
};

const toggle = (user: UserRow): void => {
    router.patch(adminUsers.toggle.url(user));
};

const destroy = (user: UserRow): void => {
    if (!window.confirm(`حساب «${user.name}» حذف شود؟`)) {
        return;
    }

    router.delete(adminUsers.destroy.url(user));
};

const go = (target: number): void => {
    if (target < 1 || target > props.users.last_page) {
        return;
    }

    router.get(adminUsers.index.url(), {
        page: target,
        ...(search.value.trim() ? { q: search.value.trim() } : {}),
    });
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
                class="w-full max-w-sm rounded-xl border border-white/10 bg-slate-950 px-4 py-2 text-sm text-slate-100 outline-none transition focus:border-indigo-500/60"
            >
            <button
                type="submit"
                class="rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-200 transition hover:border-indigo-500/50"
            >
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
                                class="me-1 rounded-full bg-indigo-500/10 px-2.5 py-0.5 text-xs text-indigo-400 ring-1 ring-inset ring-indigo-500/30"
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
                                    class="text-amber-400 hover:text-amber-300"
                                    @click="toggle(user)"
                                >
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
                                    class="text-rose-400 hover:text-rose-300"
                                    @click="destroy(user)"
                                >
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
                class="rounded-lg border border-white/10 px-4 py-2 text-sm text-slate-300 transition disabled:opacity-40"
                :disabled="users.current_page <= 1"
                @click="go(users.current_page - 1)"
            >
                قبلی
            </button>
            <span class="text-sm text-slate-400">
                صفحه {{ toFa(users.current_page) }} از
                {{ toFa(users.last_page) }}
            </span>
            <button
                type="button"
                class="rounded-lg border border-white/10 px-4 py-2 text-sm text-slate-300 transition disabled:opacity-40"
                :disabled="users.current_page >= users.last_page"
                @click="go(users.current_page + 1)"
            >
                بعدی
            </button>
        </div>
    </AppLayout>
</template>

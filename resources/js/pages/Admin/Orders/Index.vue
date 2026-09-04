<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/components/AppLayout.vue';
import AppLink from '@/components/AppLink.vue';
import AppSpinner from '@/components/AppSpinner.vue';
import {
    badgeClass,
    formatDate,
    paymentBadge,
    PAYMENT_STATUS_LABELS,
    statusBadge,
    ORDER_STATUS_LABELS,
    toFa,
} from '@/lib/ui';
import adminOrders from '@/routes/admin/orders';
import type {
    Order,
    OrderStatusValue,
    Paginated,
    PaymentStatusValue,
} from '@/types/followbegir';

const props = defineProps<{
    orders: Paginated<Order>;
    filters: {
        q?: string | null;
        status?: string | null;
        payment_status?: string | null;
    };
}>();

const search = ref(props.filters.q ?? '');
const status = ref(props.filters.status ?? '');
const paymentStatus = ref(props.filters.payment_status ?? '');

const statusOptions = Object.entries(ORDER_STATUS_LABELS).map(
    ([value, label]) => ({ value, label }),
);

const paymentOptions = Object.entries(PAYMENT_STATUS_LABELS).map(
    ([value, label]) => ({ value, label }),
);

const navigating = ref(false);

const applyFilters = (): void => {
    const params: Record<string, string> = {};

    if (search.value.trim()) {
        params.q = search.value.trim();
    }
    if (status.value) {
        params.status = status.value;
    }
    if (paymentStatus.value) {
        params.payment_status = paymentStatus.value;
    }

    navigating.value = true;

    router.get(adminOrders.index.url(), params, {
        preserveState: true,
        onFinish: () => {
            navigating.value = false;
        },
    });
};

const go = (target: number): void => {
    if (target < 1 || target > props.orders.last_page || navigating.value) {
        return;
    }

    navigating.value = true;

    router.get(
        adminOrders.index.url(),
        {
            page: target,
            ...(search.value.trim() ? { q: search.value.trim() } : {}),
            ...(status.value ? { status: status.value } : {}),
            ...(paymentStatus.value
                ? { payment_status: paymentStatus.value }
                : {}),
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
        <h1 class="text-2xl font-bold text-white">سفارش‌ها</h1>

        <form
            class="mt-6 grid gap-3 sm:grid-cols-4"
            @submit.prevent="applyFilters"
        >
            <input
                v-model="search"
                type="search"
                placeholder="جستجو در شماره سفارش یا آیدی..."
                class="rounded-xl border border-white/10 bg-slate-950 px-4 py-2 text-sm text-slate-100 transition outline-none focus:border-indigo-500/60"
            />
            <select
                v-model="status"
                class="rounded-xl border border-white/10 bg-slate-950 px-4 py-2 text-sm text-slate-100 transition outline-none focus:border-indigo-500/60"
            >
                <option value="">همه وضعیت‌ها</option>
                <option
                    v-for="option in statusOptions"
                    :key="option.value"
                    :value="option.value"
                >
                    {{ option.label }}
                </option>
            </select>
            <select
                v-model="paymentStatus"
                class="rounded-xl border border-white/10 bg-slate-950 px-4 py-2 text-sm text-slate-100 transition outline-none focus:border-indigo-500/60"
            >
                <option value="">همه پرداخت‌ها</option>
                <option
                    v-for="option in paymentOptions"
                    :key="option.value"
                    :value="option.value"
                >
                    {{ option.label }}
                </option>
            </select>
            <button
                type="submit"
                :disabled="navigating"
                class="inline-flex items-center gap-2 rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-200 transition hover:border-indigo-500/50 disabled:opacity-50"
            >
                <AppSpinner v-if="navigating" class="size-4" />
                اعمال فیلتر
            </button>
        </form>

        <div
            class="mt-4 overflow-x-auto rounded-2xl border border-white/10 bg-slate-900/60"
        >
            <table class="w-full text-right text-sm">
                <thead class="text-xs text-slate-400">
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 font-medium">شماره</th>
                        <th class="px-4 py-3 font-medium">کاربر</th>
                        <th class="px-4 py-3 font-medium">سرویس</th>
                        <th class="px-4 py-3 font-medium">آیدی هدف</th>
                        <th class="px-4 py-3 font-medium">مبلغ</th>
                        <th class="px-4 py-3 font-medium">وضعیت</th>
                        <th class="px-4 py-3 font-medium">پرداخت</th>
                        <th class="px-4 py-3 font-medium">تاریخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr v-for="order in orders.data" :key="order.id">
                        <td class="px-4 py-3" dir="ltr">
                            <AppLink
                                :href="adminOrders.show.url(order)"
                                class="text-indigo-400 hover:text-indigo-300"
                            >
                                {{ order.order_number }}
                            </AppLink>
                        </td>
                        <td class="px-4 py-3 text-slate-300">
                            {{ order.user?.name ?? 'مهمان' }}
                        </td>
                        <td class="px-4 py-3 text-slate-200">
                            {{ order.product_title }}
                        </td>
                        <td class="px-4 py-3 text-slate-300" dir="ltr">
                            @{{ order.target_username }}
                        </td>
                        <td class="px-4 py-3 text-slate-300">
                            {{ toFa(order.total_price) }} تومان
                        </td>
                        <td class="px-4 py-3">
                            <span
                                :class="badgeClass(statusBadge(order.status))"
                            >
                                {{ ORDER_STATUS_LABELS[order.status] }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                :class="
                                    badgeClass(
                                        paymentBadge(order.payment_status),
                                    )
                                "
                            >
                                {{
                                    PAYMENT_STATUS_LABELS[order.payment_status]
                                }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-400">
                            {{ formatDate(order.created_at) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="orders.last_page > 1"
            class="mt-6 flex items-center justify-center gap-3"
        >
            <button
                type="button"
                :disabled="orders.current_page <= 1 || navigating"
                class="inline-flex items-center gap-2 rounded-lg border border-white/10 px-4 py-2 text-sm text-slate-300 transition disabled:opacity-40"
                @click="go(orders.current_page - 1)"
            >
                <AppSpinner v-if="navigating" class="size-4" />
                قبلی
            </button>
            <span class="text-sm text-slate-400">
                صفحه {{ toFa(orders.current_page) }} از
                {{ toFa(orders.last_page) }}
            </span>
            <button
                type="button"
                :disabled="
                    orders.current_page >= orders.last_page || navigating
                "
                class="inline-flex items-center gap-2 rounded-lg border border-white/10 px-4 py-2 text-sm text-slate-300 transition disabled:opacity-40"
                @click="go(orders.current_page + 1)"
            >
                <AppSpinner v-if="navigating" class="size-4" />
                بعدی
            </button>
        </div>
    </AppLayout>
</template>

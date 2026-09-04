<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppLayout from '@/components/AppLayout.vue';
import AppLink from '@/components/AppLink.vue';
import {
    badgeClass,
    formatDate,
    paymentBadge,
    PAYMENT_STATUS_LABELS,
    statusBadge,
    ORDER_STATUS_LABELS,
    toFa,
} from '@/lib/ui';
import panelOrders from '@/routes/panel/orders';
import type { Order, Paginated } from '@/types/followbegir';

const props = defineProps<{ orders: Paginated<Order> }>();

const go = (target: number): void => {
    if (target < 1 || target > props.orders.last_page) {
        return;
    }

    router.get(panelOrders.index.url(), { page: target });
};
</script>

<template>
    <AppLayout kind="panel">
        <h1 class="text-2xl font-bold text-white">سفارش‌های من</h1>

        <div
            v-if="orders.data.length === 0"
            class="mt-8 rounded-2xl border border-white/10 bg-slate-900/60 px-6 py-12 text-center text-slate-400"
        >
            هنوز سفارشی ثبت نکرده‌اید.
        </div>

        <div
            v-else
            class="mt-6 overflow-x-auto rounded-2xl border border-white/10 bg-slate-900/60"
        >
            <table class="w-full text-right text-sm">
                <thead class="text-xs text-slate-400">
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 font-medium">شماره</th>
                        <th class="px-4 py-3 font-medium">سرویس</th>
                        <th class="px-4 py-3 font-medium">آیدی هدف</th>
                        <th class="px-4 py-3 font-medium">تعداد</th>
                        <th class="px-4 py-3 font-medium">مبلغ</th>
                        <th class="px-4 py-3 font-medium">وضعیت</th>
                        <th class="px-4 py-3 font-medium">پرداخت</th>
                        <th class="px-4 py-3 font-medium">تاریخ</th>
                        <th class="px-4 py-3" />
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr v-for="order in orders.data" :key="order.id">
                        <td class="px-4 py-3 text-slate-300" dir="ltr">
                            {{ order.order_number }}
                        </td>
                        <td class="px-4 py-3 text-slate-200">
                            {{ order.product_title }}
                        </td>
                        <td class="px-4 py-3 text-slate-300" dir="ltr">
                            @{{ order.target_username }}
                        </td>
                        <td class="px-4 py-3 text-slate-300">
                            {{ toFa(order.quantity) }}
                        </td>
                        <td class="px-4 py-3 text-slate-300">
                            {{ toFa(order.total_price) }} تومان
                        </td>
                        <td class="px-4 py-3">
                            <span :class="badgeClass(statusBadge(order.status))">
                                {{ ORDER_STATUS_LABELS[order.status] }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                :class="badgeClass(paymentBadge(order.payment_status))"
                            >
                                {{ PAYMENT_STATUS_LABELS[order.payment_status] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-400">
                            {{ formatDate(order.created_at) }}
                        </td>
                        <td class="px-4 py-3">
                            <AppLink
                                :href="panelOrders.show.url(order)"
                                class="text-indigo-400 hover:text-indigo-300"
                            >
                                جزئیات
                            </AppLink>
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
                class="rounded-lg border border-white/10 px-4 py-2 text-sm text-slate-300 transition disabled:opacity-40"
                :disabled="orders.current_page <= 1"
                @click="go(orders.current_page - 1)"
            >
                قبلی
            </button>
            <span class="text-sm text-slate-400">
                صفحه {{ toFa(orders.current_page) }} از
                {{ toFa(orders.last_page) }}
            </span>
            <button
                type="button"
                class="rounded-lg border border-white/10 px-4 py-2 text-sm text-slate-300 transition disabled:opacity-40"
                :disabled="orders.current_page >= orders.last_page"
                @click="go(orders.current_page + 1)"
            >
                بعدی
            </button>
        </div>
    </AppLayout>
</template>

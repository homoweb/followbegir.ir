<script setup lang="ts">
import { computed } from 'vue';
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
import type { Order } from '@/types/likeshow';

const props = defineProps<{ order: Order }>();

const rows = computed(() => [
    { label: 'شماره سفارش', value: props.order.order_number, ltr: true },
    { label: 'سرویس', value: props.order.product_title },
    {
        label: 'آیدی پیج هدف',
        value: `@${props.order.target_username}`,
        ltr: true,
    },
    { label: 'تعداد', value: toFa(props.order.quantity) },
    { label: 'مبلغ کل', value: `${toFa(props.order.total_price)} تومان` },
    { label: 'تاریخ ثبت', value: formatDate(props.order.created_at) },
    { label: 'تاریخ پرداخت', value: formatDate(props.order.paid_at) },
]);
</script>

<template>
    <AppLayout kind="panel">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-white">جزئیات سفارش</h1>
            <AppLink
                :href="panelOrders.index.url()"
                class="text-sm text-indigo-400 hover:text-indigo-300"
            >
                بازگشت به لیست
            </AppLink>
        </div>

        <div
            class="mt-6 divide-y divide-white/10 rounded-2xl border border-white/10 bg-slate-900/60"
        >
            <div
                v-for="row in rows"
                :key="row.label"
                class="flex items-center justify-between px-6 py-4 text-sm"
            >
                <span class="text-slate-400">{{ row.label }}</span>
                <span
                    class="font-medium text-slate-100"
                    :dir="row.ltr ? 'ltr' : undefined"
                >
                    {{ row.value }}
                </span>
            </div>
            <div class="flex items-center justify-between px-6 py-4 text-sm">
                <span class="text-slate-400">وضعیت سفارش</span>
                <span :class="badgeClass(statusBadge(order.status))">
                    {{ ORDER_STATUS_LABELS[order.status] }}
                </span>
            </div>
            <div class="flex items-center justify-between px-6 py-4 text-sm">
                <span class="text-slate-400">وضعیت پرداخت</span>
                <span :class="badgeClass(paymentBadge(order.payment_status))">
                    {{ PAYMENT_STATUS_LABELS[order.payment_status] }}
                </span>
            </div>
        </div>
    </AppLayout>
</template>

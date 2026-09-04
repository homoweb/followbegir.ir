<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/components/AppLayout.vue';
import AppLink from '@/components/AppLink.vue';
import { formatDate, toFa } from '@/lib/ui';
import mainRoutes from '@/routes/main';
import panelOrders from '@/routes/panel/orders';
import type { Order, SharedProps } from '@/types/followbegir';

const props = defineProps<{ order: Order; paid_at: string | null }>();

const page = usePage();
const user = computed(() => (page.props as unknown as SharedProps).auth.user);

const isPaid = computed(
    () =>
        props.order.payment_status === 'paid' ||
        props.order.status === 'completed',
);
</script>

<template>
    <AppLayout kind="main">
        <div class="mx-auto max-w-xl text-center">
            <div
                class="mx-auto flex size-16 items-center justify-center rounded-full text-3xl"
                :class="
                    isPaid
                        ? 'bg-emerald-500/15 text-emerald-400'
                        : 'bg-rose-500/15 text-rose-400'
                "
            >
                {{ isPaid ? '✓' : '✕' }}
            </div>

            <h1 class="mt-6 text-2xl font-bold text-white">
                {{ isPaid ? 'پرداخت با موفقیت انجام شد' : 'پرداخت ناموفق بود' }}
            </h1>
            <p class="mt-2 text-sm text-slate-400">
                {{
                    isPaid
                        ? 'سفارش شما ثبت شد و در حال انجام است.'
                        : 'در صورت کسر وجه، مبلغ به‌صورت خودکار بازگشت داده می‌شود.'
                }}
            </p>

            <div
                class="mt-8 divide-y divide-white/10 rounded-2xl border border-white/10 bg-slate-900/60 text-right"
            >
                <div
                    class="flex items-center justify-between px-6 py-4 text-sm"
                >
                    <span class="text-slate-400">شماره سفارش</span>
                    <span
                        class="font-medium text-slate-100"
                        dir="ltr"
                    >
                        {{ order.order_number }}
                    </span>
                </div>
                <div
                    class="flex items-center justify-between px-6 py-4 text-sm"
                >
                    <span class="text-slate-400">سرویس</span>
                    <span class="font-medium text-slate-100">
                        {{ order.product_title }}
                    </span>
                </div>
                <div
                    class="flex items-center justify-between px-6 py-4 text-sm"
                >
                    <span class="text-slate-400">تعداد</span>
                    <span class="font-medium text-slate-100">
                        {{ toFa(order.quantity) }}
                    </span>
                </div>
                <div
                    class="flex items-center justify-between px-6 py-4 text-sm"
                >
                    <span class="text-slate-400">مبلغ</span>
                    <span class="font-medium text-emerald-400">
                        {{ toFa(order.total_price) }} تومان
                    </span>
                </div>
                <div
                    class="flex items-center justify-between px-6 py-4 text-sm"
                >
                    <span class="text-slate-400">تاریخ</span>
                    <span class="font-medium text-slate-100">
                        {{ formatDate(paid_at ?? order.created_at) }}
                    </span>
                </div>
            </div>

            <div class="mt-8 flex justify-center gap-3">
                <AppLink
                    :href="mainRoutes.home.url()"
                    class="rounded-xl border border-white/10 px-5 py-2.5 text-sm text-slate-200 transition hover:border-indigo-500/50 hover:text-white"
                >
                    بازگشت به صفحه اصلی
                </AppLink>
                <AppLink
                    v-if="user"
                    :href="panelOrders.index.url()"
                    class="rounded-xl bg-indigo-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-400"
                >
                    سفارش‌های من
                </AppLink>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/components/AppLayout.vue';
import AppLink from '@/components/AppLink.vue';
import {
    badgeClass,
    formatDate,
    paymentBadge,
    PAYMENT_STATUS_LABELS,
    statusBadge,
    ORDER_STATUS_LABELS,
    TXN_STATUS_LABELS,
    TXN_STATUS_BADGES,
    toFa,
} from '@/lib/ui';
import adminOrders from '@/routes/admin/orders';
import type { Order, OrderStatusValue } from '@/types/followbegir';

const props = defineProps<{ order: Order }>();

const form = useForm({
    status: props.order.status,
});

const statusOptions = Object.entries(ORDER_STATUS_LABELS).map(
    ([value, label]) => ({ value, label }),
);

const submit = (): void => {
    form.patch(adminOrders.status.url(props.order));
};
</script>

<template>
    <AppLayout kind="admin">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-white">
                سفارش {{ order.order_number }}
            </h1>
            <AppLink
                :href="adminOrders.index.url()"
                class="text-sm text-indigo-400 hover:text-indigo-300"
            >
                بازگشت به لیست
            </AppLink>
        </div>

        <div
            class="mt-6 divide-y divide-white/10 rounded-2xl border border-white/10 bg-slate-900/60"
        >
            <div class="flex items-center justify-between px-6 py-4 text-sm">
                <span class="text-slate-400">کاربر</span>
                <span v-if="order.user" class="font-medium text-slate-100">
                    {{ order.user.name }}
                    <span class="text-slate-500" dir="ltr">
                        ({{ order.user.email }})
                    </span>
                </span>
                <span v-else class="text-slate-400">مهمان</span>
            </div>
            <div class="flex items-center justify-between px-6 py-4 text-sm">
                <span class="text-slate-400">سرویس</span>
                <span class="font-medium text-slate-100">
                    {{ order.product_title }}
                </span>
            </div>
            <div class="flex items-center justify-between px-6 py-4 text-sm">
                <span class="text-slate-400">آیدی پیج هدف</span>
                <span class="font-medium text-slate-100" dir="ltr">
                    @{{ order.target_username }}
                </span>
            </div>
            <div class="flex items-center justify-between px-6 py-4 text-sm">
                <span class="text-slate-400">تعداد</span>
                <span class="font-medium text-slate-100">
                    {{ toFa(order.quantity) }}
                </span>
            </div>
            <div class="flex items-center justify-between px-6 py-4 text-sm">
                <span class="text-slate-400">مبلغ کل</span>
                <span class="font-medium text-emerald-400">
                    {{ toFa(order.total_price) }} تومان
                </span>
            </div>
            <div class="flex items-center justify-between px-6 py-4 text-sm">
                <span class="text-slate-400">تاریخ ثبت</span>
                <span class="font-medium text-slate-100">
                    {{ formatDate(order.created_at) }}
                </span>
            </div>
            <div class="flex items-center justify-between px-6 py-4 text-sm">
                <span class="text-slate-400">تاریخ پرداخت</span>
                <span class="font-medium text-slate-100">
                    {{ formatDate(order.paid_at) }}
                </span>
            </div>
        </div>

        <h2 class="mt-8 text-lg font-bold text-white">پرداخت‌ها</h2>
        <div
            class="mt-4 overflow-x-auto rounded-2xl border border-white/10 bg-slate-900/60"
        >
            <table class="w-full text-right text-sm">
                <thead class="text-xs text-slate-400">
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 font-medium">مبلغ</th>
                        <th class="px-4 py-3 font-medium">شناسه مرجع</th>
                        <th class="px-4 py-3 font-medium">وضعیت</th>
                        <th class="px-4 py-3 font-medium">تاریخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr
                        v-for="payment in order.payments ?? []"
                        :key="payment.id"
                    >
                        <td class="px-4 py-3 text-slate-300">
                            {{ toFa(payment.amount) }} تومان
                        </td>
                        <td class="px-4 py-3 text-slate-300" dir="ltr">
                            {{ payment.reference_id ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span
                                v-if="payment.status"
                                :class="badgeClass(TXN_STATUS_BADGES[payment.status])"
                            >
                                {{ TXN_STATUS_LABELS[payment.status] }}
                            </span>
                            <span v-else class="text-slate-500">—</span>
                        </td>
                        <td class="px-4 py-3 text-slate-400">
                            {{ formatDate(payment.created_at) }}
                        </td>
                    </tr>
                    <tr v-if="(order.payments ?? []).length === 0">
                        <td
                            colspan="4"
                            class="px-4 py-6 text-center text-slate-500"
                        >
                            پرداختی ثبت نشده است.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2 class="mt-8 text-lg font-bold text-white">به‌روزرسانی وضعیت</h2>
        <form
            class="mt-4 max-w-xl rounded-2xl border border-white/10 bg-slate-900/60 p-6"
            @submit.prevent="submit"
        >
            <div>
                <label
                    for="status"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    وضعیت سفارش
                </label>
                <select
                    id="status"
                    v-model="form.status"
                    class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-slate-100 outline-none transition focus:border-indigo-500/60"
                >
                    <option
                        v-for="option in statusOptions"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </option>
                </select>
                <p
                    v-if="form.errors.status"
                    class="mt-2 text-sm text-rose-400"
                >
                    {{ form.errors.status }}
                </p>
                <p class="mt-2 text-xs text-slate-500">
                    تکمیل سفارش، وضعیت پرداخت را نیز «پرداخت شده» ثبت می‌کند.
                </p>
            </div>
            <button
                type="submit"
                :disabled="form.processing"
                class="mt-4 w-full rounded-xl bg-indigo-500 px-4 py-3 font-medium text-white transition hover:bg-indigo-400 disabled:opacity-50"
            >
                ذخیره وضعیت
            </button>
        </form>
    </AppLayout>
</template>

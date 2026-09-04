<script setup lang="ts">
import { computed, ref } from 'vue';
import AppLayout from '@/components/AppLayout.vue';
import AppSpinner from '@/components/AppSpinner.vue';
import { toFa } from '@/lib/ui';
import paymentRoutes from '@/routes/main/payment';
import type { Order } from '@/types/followbegir';

const props = defineProps<{ order: Order }>();

const submitting = ref(false);

const csrf = computed(
    () =>
        document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
            ?.content ?? '',
);

const startUrl = computed(() => paymentRoutes.start.url(props.order));

const summary = computed(() => [
    { label: 'شماره سفارش', value: props.order.order_number },
    { label: 'سرویس', value: props.order.product_title },
    {
        label: 'آیدی پیج هدف',
        value: `@${props.order.target_username}`,
        ltr: true,
    },
    { label: 'تعداد', value: toFa(props.order.quantity) },
]);
</script>

<template>
    <AppLayout kind="main">
        <div class="mx-auto max-w-2xl">
            <h1 class="text-2xl font-bold text-white">بازبینی و پرداخت</h1>
            <p class="mt-1 text-sm text-slate-400">
                جزئیات سفارش را بررسی کنید و سپس به درگاه پرداخت بروید.
            </p>

            <div
                class="mt-8 divide-y divide-white/10 rounded-2xl border border-white/10 bg-slate-900/60"
            >
                <div
                    v-for="row in summary"
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
                <div
                    class="flex items-center justify-between px-6 py-4 text-base font-bold"
                >
                    <span class="text-slate-300">مبلغ قابل پرداخت</span>
                    <span class="text-emerald-400">
                        {{ toFa(order.total_price) }} تومان
                    </span>
                </div>
            </div>

            <form
                :action="startUrl"
                method="post"
                class="mt-8"
                @submit="submitting = true"
            >
                <input type="hidden" name="_token" :value="csrf" />
                <button
                    type="submit"
                    :disabled="submitting"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-3 font-medium text-white transition hover:bg-emerald-400 disabled:opacity-50"
                >
                    <AppSpinner v-if="submitting" class="size-4" />
                    {{
                        submitting
                            ? 'در حال انتقال به درگاه…'
                            : 'پرداخت با درگاه بانکی'
                    }}
                </button>
            </form>

            <p class="mt-4 text-center text-xs text-slate-500">
                پس از کلیک، به درگاه امن بانکی منتقل می‌شوید و به این صفحه
                بازمی‌گردید.
            </p>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppLayout from '@/components/AppLayout.vue';
import AppLink from '@/components/AppLink.vue';
import { PRODUCT_TYPE_LABELS, toFa } from '@/lib/ui';
import adminProducts from '@/routes/admin/products';
import type { Product } from '@/types/followbegir';

defineProps<{ products: Product[] }>();

const toggle = (product: Product): void => {
    router.patch(adminProducts.toggle.url(product));
};

const destroy = (product: Product): void => {
    if (!window.confirm(`محصول «${product.title}» حذف شود؟`)) {
        return;
    }

    router.delete(adminProducts.destroy.url(product));
};
</script>

<template>
    <AppLayout kind="admin">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-white">محصولات</h1>
            <AppLink
                :href="adminProducts.create.url()"
                class="rounded-xl bg-indigo-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-400"
            >
                محصول جدید
            </AppLink>
        </div>

        <div
            class="mt-6 overflow-x-auto rounded-2xl border border-white/10 bg-slate-900/60"
        >
            <table class="w-full text-right text-sm">
                <thead class="text-xs text-slate-400">
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 font-medium">عنوان</th>
                        <th class="px-4 py-3 font-medium">نوع</th>
                        <th class="px-4 py-3 font-medium">قیمت پایه</th>
                        <th class="px-4 py-3 font-medium">محدوده</th>
                        <th class="px-4 py-3 font-medium">پله‌ها</th>
                        <th class="px-4 py-3 font-medium">وضعیت</th>
                        <th class="px-4 py-3" />
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr v-for="product in products" :key="product.id">
                        <td class="px-4 py-3 text-slate-200">
                            {{ product.title }}
                        </td>
                        <td class="px-4 py-3 text-slate-300">
                            اینستاگرام · {{ PRODUCT_TYPE_LABELS[product.type] }}
                        </td>
                        <td class="px-4 py-3 text-slate-300">
                            {{ toFa(product.base_price) }} تومان
                        </td>
                        <td class="px-4 py-3 text-slate-400">
                            {{ toFa(product.min_quantity) }} تا
                            {{ toFa(product.max_quantity) }}
                        </td>
                        <td class="px-4 py-3 text-slate-400">
                            {{ toFa(product.prices?.length ?? 0) }}
                        </td>
                        <td class="px-4 py-3">
                            <span
                                :class="{
                                    'text-emerald-400': product.is_active,
                                    'text-rose-400': !product.is_active,
                                }"
                            >
                                {{ product.is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3 text-xs">
                                <button
                                    type="button"
                                    class="text-amber-400 hover:text-amber-300"
                                    @click="toggle(product)"
                                >
                                    {{ product.is_active ? 'غیرفعال' : 'فعال' }}
                                </button>
                                <AppLink
                                    :href="adminProducts.edit.url(product)"
                                    class="text-indigo-400 hover:text-indigo-300"
                                >
                                    ویرایش
                                </AppLink>
                                <button
                                    type="button"
                                    class="text-rose-400 hover:text-rose-300"
                                    @click="destroy(product)"
                                >
                                    حذف
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>

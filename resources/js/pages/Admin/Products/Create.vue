<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/components/AppLayout.vue';
import adminProducts from '@/routes/admin/products';
import type { Option, PriceTier } from '@/types/followbegir';

const props = defineProps<{
    platforms: Option[];
    types: Option[];
}>();

const form = useForm({
    title: '',
    description: '',
    platform: props.platforms[0]?.value ?? 'instagram',
    type: props.types[0]?.value ?? 'followers',
    min_quantity: 1000,
    max_quantity: 1000000,
    step_quantity: 1000,
    base_price: 90000,
    prices: [] as PriceTier[],
    is_active: true,
});

const addTier = (): void => {
    form.prices.push({ min_quantity: 1000, max_quantity: 10000, price: 80000 });
};

const removeTier = (index: number): void => {
    form.prices.splice(index, 1);
};

const submit = (): void => {
    form.post(adminProducts.store.url());
};
</script>

<template>
    <AppLayout kind="admin">
        <h1 class="text-2xl font-bold text-white">محصول جدید</h1>

        <form
            class="mt-8 max-w-2xl space-y-5 rounded-2xl border border-white/10 bg-slate-900/60 p-6"
            @submit.prevent="submit"
        >
            <div>
                <label
                    for="title"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    عنوان
                </label>
                <input
                    id="title"
                    v-model="form.title"
                    type="text"
                    required
                    class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-slate-100 outline-none transition focus:border-indigo-500/60"
                >
                <p v-if="form.errors.title" class="mt-2 text-sm text-rose-400">
                    {{ form.errors.title }}
                </p>
            </div>

            <div>
                <label
                    for="description"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    توضیحات
                </label>
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-slate-100 outline-none transition focus:border-indigo-500/60"
                />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label
                        for="platform"
                        class="mb-2 block text-sm font-medium text-slate-200"
                    >
                        پلتفرم
                    </label>
                    <select
                        id="platform"
                        v-model="form.platform"
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-slate-100 outline-none transition focus:border-indigo-500/60"
                    >
                        <option
                            v-for="option in platforms"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                    <p
                        v-if="form.errors.platform"
                        class="mt-2 text-sm text-rose-400"
                    >
                        {{ form.errors.platform }}
                    </p>
                </div>

                <div>
                    <label
                        for="type"
                        class="mb-2 block text-sm font-medium text-slate-200"
                    >
                        نوع سرویس
                    </label>
                    <select
                        id="type"
                        v-model="form.type"
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-slate-100 outline-none transition focus:border-indigo-500/60"
                    >
                        <option
                            v-for="option in types"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                    <p
                        v-if="form.errors.type"
                        class="mt-2 text-sm text-rose-400"
                    >
                        {{ form.errors.type }}
                    </p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label
                        for="min_quantity"
                        class="mb-2 block text-sm font-medium text-slate-200"
                    >
                        حداقل مقدار
                    </label>
                    <input
                        id="min_quantity"
                        v-model.number="form.min_quantity"
                        dir="ltr"
                        type="number"
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-left text-slate-100 outline-none transition focus:border-indigo-500/60"
                    >
                </div>
                <div>
                    <label
                        for="max_quantity"
                        class="mb-2 block text-sm font-medium text-slate-200"
                    >
                        حداکثر مقدار
                    </label>
                    <input
                        id="max_quantity"
                        v-model.number="form.max_quantity"
                        dir="ltr"
                        type="number"
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-left text-slate-100 outline-none transition focus:border-indigo-500/60"
                    >
                </div>
                <div>
                    <label
                        for="step_quantity"
                        class="mb-2 block text-sm font-medium text-slate-200"
                    >
                        گام مقدار
                    </label>
                    <input
                        id="step_quantity"
                        v-model.number="form.step_quantity"
                        dir="ltr"
                        type="number"
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-left text-slate-100 outline-none transition focus:border-indigo-500/60"
                    >
                </div>
            </div>

            <div>
                <label
                    for="base_price"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    قیمت پایه هر ۱۰۰۰ (تومان)
                </label>
                <input
                    id="base_price"
                    v-model.number="form.base_price"
                    dir="ltr"
                    type="number"
                    class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-left text-slate-100 outline-none transition focus:border-indigo-500/60"
                >
                <p
                    v-if="form.errors.base_price"
                    class="mt-2 text-sm text-rose-400"
                >
                    {{ form.errors.base_price }}
                </p>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-200">
                        پله‌های قیمتی
                    </span>
                    <button
                        type="button"
                        class="rounded-lg border border-white/10 px-3 py-1.5 text-xs text-slate-300 transition hover:border-indigo-500/50"
                        @click="addTier"
                    >
                        افزودن پله
                    </button>
                </div>
                <p
                    v-if="form.errors.prices"
                    class="mb-2 text-sm text-rose-400"
                >
                    {{ form.errors.prices }}
                </p>
                <div
                    v-for="(tier, index) in form.prices"
                    :key="index"
                    class="mb-2 flex items-center gap-2"
                >
                    <input
                        v-model.number="tier.min_quantity"
                        dir="ltr"
                        type="number"
                        placeholder="از"
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-3 py-2 text-left text-slate-100 outline-none focus:border-indigo-500/60"
                    >
                    <input
                        v-model.number="tier.max_quantity"
                        dir="ltr"
                        type="number"
                        placeholder="تا"
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-3 py-2 text-left text-slate-100 outline-none focus:border-indigo-500/60"
                    >
                    <input
                        v-model.number="tier.price"
                        dir="ltr"
                        type="number"
                        placeholder="قیمت"
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-3 py-2 text-left text-slate-100 outline-none focus:border-indigo-500/60"
                    >
                    <button
                        type="button"
                        class="shrink-0 text-sm text-rose-400 hover:text-rose-300"
                        @click="removeTier(index)"
                    >
                        حذف
                    </button>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="size-4 rounded border-white/20 bg-slate-950"
                >
                محصول فعال باشد
            </label>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-xl bg-indigo-500 px-4 py-3 font-medium text-white transition hover:bg-indigo-400 disabled:opacity-50"
            >
                ذخیره محصول
            </button>
        </form>
    </AppLayout>
</template>

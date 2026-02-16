<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: { type: String },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Підтвердження Email" />

        <div class="mb-10 text-center relative">
            <div class="absolute -top-10 left-1/2 -translate-x-1/2 w-32 h-32 bg-indigo-600/10 blur-[50px] rounded-full"></div>

            <div class="relative inline-flex items-center justify-center w-20 h-20 bg-slate-900 border border-white/10 text-indigo-500 rounded-[2rem] mb-6 shadow-2xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 drop-shadow-[0_0_8px_rgba(99,102,241,0.5)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>

            <h1 class="text-3xl font-black text-white uppercase italic tracking-tighter">
                Перевір <span class="text-indigo-500">пошту</span>
            </h1>
            <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mt-4 leading-relaxed max-w-[280px] mx-auto opacity-80">
                Дякуємо за реєстрацію! Натисніть на посилання у листі для активації акаунта.
            </p>
        </div>

        <div v-if="verificationLinkSent" class="mb-6 font-bold text-[11px] uppercase tracking-widest text-emerald-400 text-center bg-emerald-500/10 p-4 rounded-2xl border border-emerald-500/20">
            Нове посилання надіслано!
        </div>

        <form @submit.prevent="submit">
            <div class="mt-8 flex flex-col gap-6 items-center">
                <PrimaryButton
                    class="w-full justify-center py-5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[11px] shadow-lg shadow-indigo-500/20 transition-all active:scale-[0.98] border-none"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Надіслати ще раз
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-[10px] font-black text-slate-600 uppercase hover:text-rose-500 transition tracking-[0.2em]"
                >
                    ← Вийти з системи
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

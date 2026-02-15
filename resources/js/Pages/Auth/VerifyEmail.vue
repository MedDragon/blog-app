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

        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-50 text-indigo-600 rounded-full mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h1 class="text-2xl font-black text-gray-900 uppercase">Перевірте пошту</h1>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                Дякуємо за реєстрацію! Будь ласка, натисніть на посилання у листі, який ми щойно надіслали.
            </p>
        </div>

        <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-600 text-center bg-green-50 p-3 rounded-xl">
            Нове посилання надіслано на вашу адресу.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-8 flex flex-col gap-4 items-center">
                <PrimaryButton class="w-full justify-center py-4 bg-indigo-600 rounded-2xl font-black uppercase text-xs" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Надіслати ще раз
                </PrimaryButton>

                <Link :href="route('logout')" method="post" as="button" class="text-xs font-bold text-gray-400 uppercase hover:text-red-500 transition">
                    Вийти
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

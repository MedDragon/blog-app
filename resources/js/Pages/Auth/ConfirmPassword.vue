<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Підтвердження" />

        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-50 text-amber-600 rounded-full mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0x-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h1 class="text-2xl font-black text-gray-900 uppercase">Це приватна зона</h1>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                Будь ласка, підтвердіть свій пароль, перш ніж продовжити.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Пароль" class="uppercase text-[10px] font-bold text-indigo-600 tracking-widest" />
                <TextInput id="password" type="password" class="mt-1 block w-full rounded-xl border-gray-100 shadow-sm" v-model="form.password" required autocomplete="current-password" autofocus />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-8">
                <PrimaryButton class="w-full justify-center py-4 bg-gray-900 rounded-2xl font-black uppercase text-xs shadow-xl" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Підтвердити
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

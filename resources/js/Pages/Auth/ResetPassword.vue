<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: { type: String, required: true },
    token: { type: String, required: true },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Новий пароль" />

        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Новий старт</h1>
            <p class="text-gray-500 text-sm mt-2">Встановіть новий надійний пароль</p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" class="uppercase text-[10px] font-bold text-gray-400" />
                <TextInput id="email" type="email" class="mt-1 block w-full bg-gray-50 rounded-xl" v-model="form.email" required readonly autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Новий пароль" class="uppercase text-[10px] font-bold text-indigo-600" />
                <TextInput id="password" type="password" class="mt-1 block w-full rounded-xl" v-model="form.password" required autofocus autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Підтвердження" class="uppercase text-[10px] font-bold text-indigo-600" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full rounded-xl" v-model="form.password_confirmation" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-8">
                <PrimaryButton class="w-full justify-center py-4 bg-gray-900 rounded-2xl font-black uppercase text-xs" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Оновити пароль
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Створити акаунт" />

        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Реєстрація</h1>
            <p class="text-gray-500 text-sm mt-2">Приєднуйтесь до нашої спільноти</p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Ваше ім'я" class="uppercase text-[10px] font-bold text-indigo-600 tracking-widest" />
                <TextInput id="name" type="text" class="mt-1 block w-full rounded-xl border-gray-100 shadow-sm" v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" class="uppercase text-[10px] font-bold text-indigo-600 tracking-widest" />
                <TextInput id="email" type="email" class="mt-1 block w-full rounded-xl border-gray-100 shadow-sm" v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Пароль" class="uppercase text-[10px] font-bold text-indigo-600 tracking-widest" />
                <TextInput id="password" type="password" class="mt-1 block w-full rounded-xl border-gray-100 shadow-sm" v-model="form.password" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Підтвердження пароля" class="uppercase text-[10px] font-bold text-indigo-600 tracking-widest" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full rounded-xl border-gray-100 shadow-sm" v-model="form.password_confirmation" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-8">
                <PrimaryButton class="w-full justify-center py-4 bg-indigo-600 rounded-2xl font-black uppercase tracking-widest text-xs" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Зареєструватися
                </PrimaryButton>
            </div>

            <div class="mt-8 text-center border-t border-gray-50 pt-6">
                <p class="text-sm text-gray-500">
                    Вже маєте акаунт?
                    <Link :href="route('login')" class="text-indigo-600 font-bold hover:underline">Увійти</Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

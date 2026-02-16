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

        <div class="mb-10 text-center">
            <h1 class="text-4xl font-black text-white uppercase italic tracking-tighter">Реєстрація</h1>
            <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mt-3 opacity-60">Приєднуйтесь до нашої спільноти</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="name" value="Ваше ім'я" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2 ml-1" />
                <TextInput
                    id="name"
                    type="text"
                    class="block w-full bg-slate-900/50 border-white/10 text-white rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 font-bold"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Artem Boss"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2 ml-1" />
                <TextInput
                    id="email"
                    type="email"
                    class="block w-full bg-slate-900/50 border-white/10 text-white rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 font-bold"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="email@example.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="password" value="Пароль" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2 ml-1" />
                    <TextInput
                        id="password"
                        type="password"
                        class="block w-full bg-slate-900/50 border-white/10 text-white rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                </div>
                <div>
                    <InputLabel for="password_confirmation" value="Підтвердження" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2 ml-1" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="block w-full bg-slate-900/50 border-white/10 text-white rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                </div>
            </div>
            <InputError class="mt-1" :message="form.errors.password" />

            <div class="pt-4">
                <PrimaryButton
                    class="w-full justify-center py-5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[11px] shadow-lg shadow-indigo-500/20 border-none transition-all"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Зареєструватися
                </PrimaryButton>
            </div>

            <div class="mt-8 text-center pt-8 border-t border-white/5">
                <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">
                    Вже маєте акаунт?
                    <Link :href="route('login')" class="text-indigo-400 font-black hover:text-white transition ml-2">Увійти</Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

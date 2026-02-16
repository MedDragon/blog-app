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

        <div class="mb-10 text-center relative">
            <h1 class="text-6xl font-black text-white/[0.03] uppercase italic tracking-tighter absolute -top-6 left-1/2 -translate-x-1/2 select-none whitespace-nowrap">
                RESET
            </h1>
            <h2 class="relative z-10 text-3xl font-black text-white uppercase italic tracking-tighter">
                Новий <span class="text-indigo-500">старт</span>
            </h2>
            <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mt-3 opacity-70">
                Встановіть новий надійний пароль
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Твій Email" class="uppercase text-[10px] font-black text-slate-500 tracking-widest mb-2 ml-1" />
                <TextInput
                    id="email"
                    type="email"
                    class="block w-full bg-white/[0.02] border-white/5 text-slate-500 rounded-2xl p-4 font-bold cursor-not-allowed"
                    v-model="form.email"
                    required
                    readonly
                    autocomplete="username"
                />
            </div>

            <div>
                <InputLabel for="password" value="Новий пароль" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2 ml-1" />
                <TextInput
                    id="password"
                    type="password"
                    class="block w-full bg-slate-900/50 border-white/10 text-white rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold"
                    v-model="form.password"
                    required
                    autofocus
                    autocomplete="new-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Повторіть пароль" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2 ml-1" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="block w-full bg-slate-900/50 border-white/10 text-white rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="pt-4">
                <PrimaryButton
                    class="w-full justify-center py-5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[11px] shadow-lg shadow-indigo-500/20 transition-all active:scale-[0.98] border-none"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Оновити та увійти
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

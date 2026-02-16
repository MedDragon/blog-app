<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Вхід в систему" />

        <div class="absolute top-6 left-6 z-50">
            <Link
                href="/"
                class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 hover:text-indigo-400 transition-all group p-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
                Назад
            </Link>
        </div>

        <div class="mb-10 text-center mt-10 relative">
            <h1 class="text-4xl font-black text-white uppercase italic tracking-tighter">
                З поверненням <span class="text-indigo-500 italic">!</span>
            </h1>
            <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mt-3 opacity-60">Будь ласка, увійдіть у свій акаунт</p>
        </div>

        <div v-if="status" class="mb-6 text-sm font-bold text-green-400 bg-green-500/10 p-4 rounded-xl border border-green-500/20 text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Електронна пошта" class="uppercase text-[10px] font-black text-indigo-400 tracking-[0.15em] mb-2 ml-1" />
                <TextInput
                    id="email"
                    type="email"
                    class="block w-full bg-slate-900/50 border-white/10 text-white placeholder:text-slate-700 rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="email@example.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex justify-between items-center mb-2 ml-1">
                    <InputLabel for="password" value="Пароль" class="uppercase text-[10px] font-black text-indigo-400 tracking-[0.15em]" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-[9px] font-black text-slate-500 uppercase hover:text-white transition tracking-widest"
                    >
                        Забули?
                    </Link>
                </div>

                <TextInput
                    id="password"
                    type="password"
                    class="block w-full bg-slate-900/50 border-white/10 text-white placeholder:text-slate-700 rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block">
                <label class="flex items-center group cursor-pointer w-fit">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded bg-slate-900 border-white/10 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-slate-900" />
                    <span class="ms-3 text-[10px] font-black text-slate-500 uppercase tracking-widest group-hover:text-slate-300 transition">Запам'ятати мене</span>
                </label>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center py-5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[11px] shadow-lg shadow-indigo-500/20 transition-all active:scale-[0.98] border-none"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Увійти
                </PrimaryButton>
            </div>

            <div class="mt-10 text-center pt-8 border-t border-white/5">
                <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">
                    Ще не маєте акаунту?
                    <Link
                        :href="route('register')"
                        class="text-indigo-400 font-black hover:text-white transition ml-2"
                    >
                        Створити зараз
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

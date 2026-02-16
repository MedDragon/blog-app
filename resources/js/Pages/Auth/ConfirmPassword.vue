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
        <Head title="Підтвердження доступу" />

        <div class="mb-10 text-center relative">
            <div class="relative inline-flex items-center justify-center w-20 h-20 bg-slate-900 border border-white/10 text-amber-500 rounded-[2rem] mb-6 shadow-2xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 drop-shadow-[0_0_8px_rgba(245,158,11,0.4)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>

            <h1 class="text-3xl font-black text-white uppercase italic tracking-tighter">
                Приватна <span class="text-amber-500">зона</span>
            </h1>
            <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mt-3 leading-relaxed opacity-80">
                Підтвердьте пароль, щоб продовжити дію
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="password" value="Твій пароль" class="uppercase text-[10px] font-black text-indigo-400 tracking-[0.15em] mb-2 ml-1" />

                <TextInput
                    id="password"
                    type="password"
                    class="block w-full bg-slate-900/50 border-white/10 text-white placeholder:text-slate-700 rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center py-5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[11px] shadow-lg shadow-indigo-500/20 transition-all active:scale-[0.98] border-none"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Підтвердити доступ
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

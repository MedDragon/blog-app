<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: { type: String },
});

const form = useForm({ email: '' });

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Відновлення пароля" />

        <div class="mb-10 text-center relative">
            <h1 class="text-7xl font-black text-white/[0.03] uppercase italic tracking-tighter absolute -top-6 left-1/2 -translate-x-1/2 select-none">
                ОЙ!
            </h1>

            <h2 class="relative z-10 text-3xl font-black text-white uppercase italic tracking-tighter">
                Забули <span class="text-indigo-500">пароль?</span>
            </h2>

            <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mt-3 leading-relaxed opacity-70">
                Введіть Email, і ми надішлемо посилання для скидання
            </p>
        </div>

        <div v-if="status" class="mb-6 text-sm font-bold text-emerald-400 bg-emerald-500/10 p-4 rounded-xl border border-emerald-500/20 text-center animate-pulse">
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
                    placeholder="example@mail.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="pt-2 space-y-6">
                <PrimaryButton
                    class="w-full justify-center py-5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[11px] shadow-lg shadow-indigo-500/20 transition-all active:scale-[0.98] border-none"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Надіслати посилання
                </PrimaryButton>

                <div class="text-center pt-4">
                    <Link
                        :href="route('login')"
                        class="text-[10px] font-black text-slate-500 uppercase hover:text-white transition tracking-[0.2em]"
                    >
                        ← Повернутися до входу
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>

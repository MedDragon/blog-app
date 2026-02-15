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

        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Ой!</h1>
            <p class="text-gray-500 text-sm mt-2 leading-relaxed">
                Забули пароль? Без проблем. Введіть свій Email, і ми надішлемо посилання для скидання.
            </p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-4 rounded-xl border border-green-100">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" class="uppercase text-[10px] font-bold text-indigo-600 tracking-widest" />
                <TextInput id="email" type="email" class="mt-1 block w-full rounded-xl border-gray-100 shadow-sm" v-model="form.email" required autofocus autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-8 space-y-4">
                <PrimaryButton class="w-full justify-center py-4 bg-indigo-600 rounded-2xl font-black uppercase text-xs" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Надіслати посилання
                </PrimaryButton>

                <Link :href="route('login')" class="block text-center text-xs font-bold text-gray-400 uppercase hover:text-gray-600 transition">
                    ← Назад до входу
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

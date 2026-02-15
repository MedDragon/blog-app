<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
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

        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">З поверненням!</h1>
            <p class="text-gray-500 text-sm mt-2">Будь ласка, увійдіть у свій акаунт</p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Електронна пошта" class="uppercase text-[10px] font-bold text-indigo-600 tracking-widest" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full border-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="email@example.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-6">
                <div class="flex justify-between items-center">
                    <InputLabel for="password" value="Пароль" class="uppercase text-[10px] font-bold text-indigo-600 tracking-widest" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-[10px] font-bold text-gray-400 uppercase hover:text-indigo-500 transition"
                    >
                        Забули пароль?
                    </Link>
                </div>

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full border-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-6 block">
                <label class="flex items-center group cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="ms-2 text-sm text-gray-600 group-hover:text-gray-900 transition">Запам'ятати мене</span>
                </label>
            </div>

            <div class="mt-8">
                <PrimaryButton
                    class="w-full justify-center py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-lg shadow-indigo-200 transition-all active:scale-[0.98]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Увійти
                </PrimaryButton>
            </div>

            <div class="mt-8 text-center border-t border-gray-50 pt-6">
                <p class="text-sm text-gray-500">
                    Ще не маєте акаунту?
                    <Link
                        :href="route('register')"
                        class="text-indigo-600 font-bold hover:underline underline-offset-4"
                    >
                        Створити зараз
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

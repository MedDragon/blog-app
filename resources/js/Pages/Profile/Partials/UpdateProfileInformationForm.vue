<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    avatar: user.avatar || '', // Нове поле
    bio: user.bio || '',       // Нове поле
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Налаштування Prostir</h2>
            <p class="mt-1 text-sm text-gray-500">Персоналізуйте свій акаунт, щоб виділятися в спільноті.</p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div class="flex items-center gap-6 p-4 bg-gray-50 rounded-3xl border border-gray-100">
                <div class="w-20 h-20 rounded-2xl bg-indigo-600 flex-shrink-0 overflow-hidden shadow-lg border-2 border-white">
                    <img v-if="form.avatar" :src="form.avatar" class="w-full h-full object-cover" @error="form.avatar = ''" />
                    <div v-else class="w-full h-full flex items-center justify-center text-white font-black text-2xl">
                        {{ form.name.charAt(0) }}
                    </div>
                </div>
                <div class="flex-1">
                    <InputLabel for="avatar" value="URL вашого фото" class="uppercase text-[10px] font-bold text-indigo-600" />
                    <TextInput id="avatar" type="text" class="mt-1 block w-full rounded-xl" v-model="form.avatar" placeholder="https://..." />
                    <InputError class="mt-2" :message="form.errors.avatar" />
                </div>
            </div>

            <div>
                <InputLabel for="bio" value="Про себе" class="uppercase text-[10px] font-bold text-indigo-600" />
                <textarea id="bio" v-model="form.bio" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" rows="3" placeholder="Розкажіть щось цікаве..."></textarea>
                <InputError class="mt-2" :message="form.errors.bio" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="name" value="Ваше ім'я" class="uppercase text-[10px] font-bold text-indigo-600" />
                    <TextInput id="name" type="text" class="mt-1 block w-full rounded-xl" v-model="form.name" required />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel for="email" value="Електронна пошта" class="uppercase text-[10px] font-bold text-indigo-600" />
                    <TextInput id="email" type="email" class="mt-1 block w-full rounded-xl" v-model="form.email" required />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing" class="bg-indigo-600 rounded-xl px-8 py-3 uppercase text-[10px] font-black tracking-widest">
                    Зберегти оновлення
                </PrimaryButton>
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600 font-bold tracking-tight">Зміни застосовано!</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

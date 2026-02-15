<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) { form.reset('password', 'password_confirmation'); passwordInput.value.focus(); }
            if (form.errors.current_password) { form.reset('current_password'); currentPasswordInput.value.focus(); }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Безпека акаунта</h2>
            <p class="mt-1 text-sm text-gray-500">Використовуйте надійний пароль для захисту ваших даних.</p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="current_password" value="Поточний пароль" class="uppercase text-[10px] font-bold text-indigo-600" />
                <TextInput id="current_password" ref="currentPasswordInput" v-model="form.current_password" type="password" class="mt-1 block w-full rounded-xl border-gray-100" autocomplete="current-password" />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <div>
                <InputLabel for="password" value="Новий пароль" class="uppercase text-[10px] font-bold text-indigo-600" />
                <TextInput id="password" ref="passwordInput" v-model="form.password" type="password" class="mt-1 block w-full rounded-xl border-gray-100" autocomplete="new-password" />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Підтвердження" class="uppercase text-[10px] font-bold text-indigo-600" />
                <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full rounded-xl border-gray-100" autocomplete="new-password" />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing" class="bg-gray-900 rounded-xl px-8 py-3 uppercase text-[10px] font-black tracking-widest">Оновити пароль</PrimaryButton>
                <p v-if="form.recentlySuccessful" class="text-sm text-green-600 font-bold">Оновлено!</p>
            </div>
        </form>
    </section>
</template>

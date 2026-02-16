<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);
const form = useForm({ current_password: '', password: '', password_confirmation: '' });

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
            <h2 class="text-xl font-black text-white uppercase tracking-tight">Безпека</h2>
            <p class="mt-1 text-sm text-slate-400">Регулярна зміна пароля підвищує захист вашого цифрового простору.</p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <InputLabel for="current_password" value="Дійсний пароль" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2" />
                    <TextInput id="current_password" ref="currentPasswordInput" v-model="form.current_password" type="password" class="block w-full bg-slate-950/50 border-white/10 text-white rounded-xl" />
                    <InputError :message="form.errors.current_password" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="password" value="Новий пароль" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2" />
                    <TextInput id="password" ref="passwordInput" v-model="form.password" type="password" class="block w-full bg-slate-950/50 border-white/10 text-white rounded-xl" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Підтвердження" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2" />
                    <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="block w-full bg-slate-950/50 border-white/10 text-white rounded-xl" />
                </div>
            </div>
            <InputError :message="form.errors.password" class="mt-1" />

            <div class="pt-4">
                <PrimaryButton :disabled="form.processing" class="bg-slate-800 hover:bg-indigo-600 rounded-2xl px-10 py-5 uppercase text-[11px] font-black tracking-widest transition-all">
                    Змінити пароль
                </PrimaryButton>
            </div>
        </form>
    </section>
</template>

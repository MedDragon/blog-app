<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({ password: '' });

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => { confirmingUserDeletion.value = false; form.reset(); };
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-xl font-black text-red-600 uppercase tracking-tight">Видалення акаунта</h2>
            <p class="mt-1 text-sm text-gray-500">Будьте обережні. Всі дані будуть видалені безповоротно.</p>
        </header>

        <DangerButton @click="confirmingUserDeletion = true" class="rounded-xl px-8 py-3 uppercase text-[10px] font-black tracking-widest">Видалити мій профіль</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-10">
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tighter">Ви впевнені?</h2>
                <p class="mt-3 text-sm text-gray-500">Для підтвердження видалення, будь ласка, введіть ваш пароль.</p>

                <div class="mt-6">
                    <InputLabel for="password" value="Пароль" class="sr-only" />
                    <TextInput id="password" ref="passwordInput" v-model="form.password" type="password" class="mt-1 block w-full rounded-xl border-gray-200" placeholder="Ваш пароль" @keyup.enter="deleteUser" />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal" class="rounded-xl px-6 uppercase text-[10px] font-bold">Скасувати</SecondaryButton>
                    <DangerButton class="rounded-xl px-6 bg-red-600 uppercase text-[10px] font-bold" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="deleteUser">
                        Так, видалити назавжди
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>

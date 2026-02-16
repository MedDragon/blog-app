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
    <section>
        <header>
            <h2 class="text-xl font-black text-rose-500 uppercase tracking-tight">Видалення профілю</h2>
            <p class="mt-1 text-sm text-slate-400">Всі ваші публікації та дані будуть стерті назавжди.</p>
        </header>

        <button @click="confirmingUserDeletion = true" class="mt-6 px-8 py-4 border-2 border-rose-500/20 text-rose-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all shadow-lg">
            Покинути Prostir
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-12 bg-slate-950 text-white">
                <h2 class="text-3xl font-black uppercase tracking-tighter">Останній крок</h2>
                <p class="mt-4 text-slate-400 text-sm leading-relaxed">Ми шкодуємо, що ви йдете. Будь ласка, введіть свій пароль, щоб підтвердити видалення акаунта.</p>

                <div class="mt-8">
                    <TextInput id="password" ref="passwordInput" v-model="form.password" type="password" class="block w-full bg-white/5 border-white/10 text-white rounded-xl p-4" placeholder="Ваш пароль" @keyup.enter="deleteUser" />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-10 flex justify-end gap-4">
                    <SecondaryButton @click="closeModal" class="rounded-xl border-none bg-white/10 text-white uppercase text-[10px] font-black">Скасувати</SecondaryButton>
                    <DangerButton class="rounded-xl bg-rose-600 px-8 uppercase text-[10px] font-black" :disabled="form.processing" @click="deleteUser">
                        Видалити назавжди
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>

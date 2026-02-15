<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

// Використовуємо computed, щоб дані користувача завжди були свіжими
const user = computed(() => usePage().props.auth.user);

const photoPreview = ref(null);
const fileInput = ref(null);

const form = useForm({
    _method: 'patch',
    name: user.value.name,
    email: user.value.email,
    avatar: null,
    avatar_url: '',
    bio: user.value.bio || '',
});

const updatePhotoPreview = () => {
    const photo = fileInput.value.files[0];
    if (!photo) return;

    form.avatar = photo;
    form.avatar_url = '';

    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);
};

const submitForm = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Очищаємо тимчасові дані форми, щоб дати computed-юзеру показати нове фото
            photoPreview.value = null;
            form.avatar = null;
            form.avatar_url = '';
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Налаштування Prostir</h2>
            <p class="mt-1 text-sm text-gray-500">Персоналізуйте свій акаунт, щоб виділятися в спільноті.</p>
        </header>

        <form @submit.prevent="submitForm" class="mt-6 space-y-6" enctype="multipart/form-data">
            <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100 shadow-inner space-y-6">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <div class="w-24 h-24 rounded-3xl bg-indigo-600 flex-shrink-0 overflow-hidden shadow-xl border-4 border-white transition group-hover:scale-105 duration-300">
                            <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
                            <img v-else-if="form.avatar_url" :src="form.avatar_url" class="w-full h-full object-cover" />
                            <img v-else-if="user.avatar" :src="user.avatar" class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full flex items-center justify-center text-white font-black text-3xl italic">
                                {{ form.name.charAt(0) }}
                            </div>
                        </div>
                    </div>

                    <div class="flex-1">
                        <InputLabel value="Завантажити файл" class="uppercase text-[10px] font-black text-indigo-600 tracking-widest mb-2" />
                        <input type="file" ref="fileInput" class="hidden" @change="updatePhotoPreview" accept="image/*" />

                        <div class="flex gap-2">
                            <button type="button" @click="$refs.fileInput.click()" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition shadow-sm">
                                Вибрати фото
                            </button>
                            <button v-if="photoPreview || form.avatar" type="button" @click="photoPreview = null; form.avatar = null" class="px-4 py-2 text-xs font-bold text-red-500 hover:text-red-700 transition">
                                Скасувати файл
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <InputLabel for="avatar_url" value="Або вставити посилання на фото" class="uppercase text-[10px] font-black text-indigo-600 tracking-widest mb-2" />
                    <TextInput
                        id="avatar_url"
                        type="text"
                        class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm"
                        v-model="form.avatar_url"
                        placeholder="https://..."
                        @input="photoPreview = null; form.avatar = null"
                    />
                    <InputError class="mt-2" :message="form.errors.avatar" />
                </div>
            </div>

            <div>
                <InputLabel for="bio" value="Про себе" class="uppercase text-[10px] font-black text-indigo-600 tracking-widest" />
                <textarea
                    id="bio"
                    v-model="form.bio"
                    class="mt-1 block w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm p-4 text-sm font-medium"
                    rows="3"
                    placeholder="Розкажіть спільноті щось цікаве..."
                ></textarea>
                <InputError class="mt-2" :message="form.errors.bio" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="name" value="Ваше ім'я" class="uppercase text-[10px] font-black text-indigo-600 tracking-widest" />
                    <TextInput id="name" type="text" class="mt-1 block w-full rounded-xl border-gray-200" v-model="form.name" required />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel for="email" value="Електронна пошта" class="uppercase text-[10px] font-black text-indigo-600 tracking-widest" />
                    <TextInput id="email" type="email" class="mt-1 block w-full rounded-xl border-gray-200" v-model="form.email" required />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <PrimaryButton :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 rounded-xl px-10 py-4 uppercase text-[10px] font-black tracking-[0.2em] shadow-lg transition-all active:scale-95">
                    {{ form.processing ? 'Опрацювання...' : 'Зберегти зміни' }}
                </PrimaryButton>

                <Transition enter-active-class="transition ease-in-out duration-300" enter-from-class="opacity-0 translate-x-4" leave-active-class="transition ease-in-out duration-300" leave-to-class="opacity-0 translate-x-4">
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600 font-black italic tracking-tight flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                        Профіль оновлено!
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

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
    reader.onload = (e) => { photoPreview.value = e.target.result; };
    reader.readAsDataURL(photo);
};

const submitForm = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
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
            <h2 class="text-xl font-black text-white uppercase tracking-tight">Налаштування <span class="text-indigo-500">Prostir</span></h2>
            <p class="mt-1 text-sm text-slate-400">Персоналізуйте свій акаунт, щоб виділятися в спільноті.</p>
        </header>

        <form @submit.prevent="submitForm" class="mt-8 space-y-8" enctype="multipart/form-data">
            <div class="p-6 bg-white/[0.03] rounded-[2rem] border border-white/5 shadow-inner space-y-6">
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="relative group">
                        <div class="w-28 h-28 rounded-[2.5rem] bg-gradient-to-tr from-indigo-600 to-purple-600 p-1 flex-shrink-0 overflow-hidden shadow-2xl transition group-hover:scale-105 duration-300">
                            <div class="w-full h-full bg-slate-900 rounded-[2.3rem] overflow-hidden">
                                <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
                                <img v-else-if="form.avatar_url" :src="form.avatar_url" class="w-full h-full object-cover" />
                                <img v-else-if="user.avatar" :src="user.avatar" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center text-white font-black text-4xl italic">
                                    {{ form.name.charAt(0) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 text-center sm:text-left">
                        <InputLabel value="Аватар користувача" class="uppercase text-[10px] font-black text-indigo-400 tracking-[0.2em] mb-3" />
                        <input type="file" ref="fileInput" class="hidden" @change="updatePhotoPreview" accept="image/*" />

                        <div class="flex flex-wrap justify-center sm:justify-start gap-3">
                            <button type="button" @click="$refs.fileInput.click()" class="px-6 py-2.5 bg-white/10 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest text-white hover:bg-indigo-600 transition shadow-lg">
                                Вибрати файл
                            </button>
                            <button v-if="photoPreview || form.avatar" type="button" @click="photoPreview = null; form.avatar = null" class="px-4 py-2 text-[10px] font-black uppercase text-rose-500 hover:text-rose-400 transition">
                                Скасувати
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <InputLabel for="avatar_url" value="Або пряме посилання" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2" />
                    <TextInput id="avatar_url" type="text" class="block w-full bg-slate-950/50 border-white/10 text-white rounded-xl" v-model="form.avatar_url" placeholder="https://..." @input="photoPreview = null; form.avatar = null" />
                    <InputError class="mt-2" :message="form.errors.avatar" />
                </div>
            </div>

            <div>
                <InputLabel for="bio" value="Твоя історія (Bio)" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2" />
                <textarea id="bio" v-model="form.bio" class="block w-full bg-slate-950/50 border-white/10 text-white rounded-[1.5rem] focus:ring-indigo-500 p-4 text-sm font-medium" rows="3" placeholder="Розкажіть про себе..."></textarea>
                <InputError class="mt-2" :message="form.errors.bio" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="name" value="Нікнейм" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2" />
                    <TextInput id="name" type="text" class="block w-full bg-slate-950/50 border-white/10 text-white rounded-xl" v-model="form.name" required />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel for="email" value="Електронна пошта" class="uppercase text-[10px] font-black text-indigo-400 tracking-widest mb-2" />
                    <TextInput id="email" type="email" class="block w-full bg-slate-950/50 border-white/10 text-white rounded-xl" v-model="form.email" required />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>

            <div class="flex items-center gap-6 pt-4">
                <PrimaryButton :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-500 rounded-2xl px-12 py-5 uppercase text-[11px] font-black tracking-[0.2em] shadow-[0_10px_30px_rgba(79,70,229,0.3)] transition-all active:scale-95">
                    {{ form.processing ? 'Опрацювання...' : 'Оновити профіль' }}
                </PrimaryButton>

                <Transition enter-active-class="transition duration-300" enter-from-class="opacity-0 translate-x-4" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-[10px] font-black uppercase text-emerald-400 tracking-widest flex items-center gap-2">
                        Збережено успішно
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

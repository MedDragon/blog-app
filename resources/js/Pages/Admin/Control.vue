<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    admins: Array,
    allUsers: Array
});

// Функція оновлення ваги
const updateWeight = (id, weightValue) => {
    router.patch(route('admin.update-weight', id), {
        weight: parseInt(weightValue)
    }, {
        preserveScroll: true,
        onSuccess: () => console.log('Вага оновлена')
    });
};

// Функція призначення адміна
const makeAdmin = (userId) => {
    router.patch(route('admin.update-role', userId), {
        role: 'admin'
    }, {
        preserveScroll: true,
    });
};

// НОВА ФУНКЦІЯ: Увійти під іншим юзером
const loginAs = (userId) => {
    router.post(route('admin.login-as', userId));
};
</script>

<template>
    <Head title="Керування Prostir" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h1 class="text-2xl font-bold mb-6 italic uppercase tracking-tighter text-gray-900">
                        Керування <span class="text-indigo-600">навантаженням</span>
                    </h1>

                    <div class="mb-10">
                        <h2 class="text-xs font-black text-indigo-600 uppercase tracking-widest mb-4">Діючі менеджери</h2>
                        <div class="grid gap-4">
                            <div v-for="admin in admins" :key="admin.id" class="flex items-center justify-between p-4 border border-gray-100 rounded-2xl bg-gray-50/30">
                                <div>
                                    <div class="font-black text-gray-900 uppercase text-sm">{{ admin.name }}</div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase">Постів у роботі: {{ admin.managed_posts_count }}</div>
                                    <div class="text-[10px] text-gray-400 mt-1">{{ admin.email }}</div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <button @click="loginAs(admin.id)" class="text-[10px] font-black uppercase text-indigo-600 hover:underline mr-4">
                                        Увійти в акаунт
                                    </button>
                                    <span class="text-[10px] font-black uppercase text-gray-400">Вага (%):</span>
                                    <input
                                        type="number"
                                        v-model="admin.assignment_weight"
                                        @change="updateWeight(admin.id, admin.assignment_weight)"
                                        class="w-20 border-none bg-white shadow-sm rounded-lg font-black text-indigo-600 focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Користувачі Prostir</h2>
                        <div class="max-h-80 overflow-y-auto border border-gray-100 rounded-2xl p-2 bg-gray-50/20">
                            <div v-for="user in allUsers" :key="user.id" class="flex items-center justify-between p-3 hover:bg-white hover:shadow-sm rounded-xl transition duration-200 group">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-700">{{ user.name }}</span>
                                    <span class="text-[10px] text-gray-400">{{ user.email }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button @click="loginAs(user.id)" class="text-[10px] font-black text-gray-400 hover:text-indigo-600 transition uppercase tracking-widest">
                                        Тест вхід
                                    </button>
                                    <button
                                        type="button"
                                        @click="makeAdmin(user.id)"
                                        class="bg-gray-900 text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition shadow-md active:scale-95"
                                    >
                                        Зробити адміном
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
div::-webkit-scrollbar { width: 4px; }
div::-webkit-scrollbar-track { background: transparent; }
div::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>

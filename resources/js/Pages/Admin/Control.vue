<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    admins: Array,
    allUsers: Array
});

// Оновлення ваги
const updateWeight = (id, weightValue) => {
    router.patch(route('admin.update-weight', id), {
        weight: parseInt(weightValue)
    }, { preserveScroll: true });
};

// Призначити адміном
const makeAdmin = (userId) => {
    router.patch(route('admin.update-role', userId), { role: 'admin' }, { preserveScroll: true });
};

// ПОНИЖЕННЯ РОЛІ (Тепер точно змінить роль у БД)
const downgradeToUser = (userId) => {
    if (confirm('Понизити менеджера до звичайного користувача?')) {
        router.patch(route('admin.downgrade', userId), {}, { preserveScroll: true });
    }
};

// ВИДАЛЕННЯ
const deleteUser = (userId) => {
    if (confirm('Видалити користувача НАЗАВЖДИ?')) {
        router.delete(route('admin.destroy-user', userId), { preserveScroll: true });
    }
};

// ВХІД ПІД ЮЗЕРОМ (Повернуто!)
const loginAs = (userId) => {
    if (confirm('Увійти в систему під цим користувачем?')) {
        router.post(route('admin.login-as', userId));
    }
};
</script>

<template>
    <Head title="Керування Prostir" />

    <AuthenticatedLayout>
        <div class="py-12 relative z-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-slate-800/40 backdrop-blur-2xl border border-white/10 rounded-[3rem] p-8 shadow-2xl">

                    <div class="flex items-center justify-between mb-12">
                        <h1 class="text-3xl font-black italic uppercase text-white tracking-tighter">
                            Керування <span class="text-indigo-500">Доступом</span>
                        </h1>
                    </div>

                    <div class="grid lg:grid-cols-2 gap-10">

                        <div class="space-y-6">
                            <h2 class="text-xs font-black text-indigo-400 uppercase tracking-widest ml-4 flex items-center gap-2">
                                <span class="w-2 h-2 bg-indigo-500 rounded-full animate-ping"></span>
                                Адміністрація
                            </h2>

                            <div v-for="admin in admins" :key="admin.id"
                                 class="flex items-center justify-between p-6 bg-slate-900/60 border border-white/5 rounded-[2rem] hover:border-indigo-500/30 transition-all">
                                <div class="flex-1">
                                    <div class="font-black text-white uppercase">{{ admin.name }}</div>
                                    <div class="flex gap-4 mt-3">
                                        <button @click="loginAs(admin.id)" class="text-[9px] font-black uppercase text-indigo-500 hover:text-white transition">
                                            Увійти →
                                        </button>
                                        <button @click="downgradeToUser(admin.id)" class="text-[9px] font-black uppercase text-orange-500 hover:text-white transition">
                                            Понизити
                                        </button>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2">
                                    <span class="text-[9px] font-black text-slate-500 uppercase">Вага %</span>
                                    <input type="number" v-model="admin.assignment_weight"
                                           @change="updateWeight(admin.id, admin.assignment_weight)"
                                           class="w-20 bg-slate-950/50 border-none text-indigo-400 rounded-xl font-black text-center focus:ring-indigo-500" />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h2 class="text-xs font-black text-slate-500 uppercase tracking-widest ml-4">Всі користувачі</h2>
                            <div class="bg-slate-950/30 rounded-[2.5rem] border border-white/5 p-4 max-h-[500px] overflow-y-auto custom-scrollbar">
                                <div v-for="user in allUsers" :key="user.id"
                                     class="flex items-center justify-between p-4 hover:bg-white/5 rounded-2xl transition group">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-300">{{ user.name }}</span>
                                        <span class="text-[10px] text-slate-600 uppercase">{{ user.role }}</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button @click="loginAs(user.id)" class="p-2 text-slate-500 hover:text-indigo-400" title="Вхід">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                                        </button>

                                        <button @click="deleteUser(user.id)" class="p-2 text-slate-500 hover:text-rose-500" title="Видалити">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>

                                        <button v-if="user.role !== 'admin'" @click="makeAdmin(user.id)"
                                                class="bg-white/5 hover:bg-indigo-600 text-slate-300 px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">
                                            + Адмін
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

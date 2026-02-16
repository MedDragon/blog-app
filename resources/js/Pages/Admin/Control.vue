<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    admins: Array,
    allUsers: Array
});

// Функція оновлення ваги (Логіка без змін)
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

// Увійти під іншим юзером
const loginAs = (userId) => {
    router.post(route('admin.login-as', userId));
};
</script>

<template>
    <Head title="Керування Prostir" />

    <AuthenticatedLayout>
        <div class="py-12 relative z-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-slate-800/40 backdrop-blur-2xl border border-white/10 rounded-[3rem] p-8 shadow-[0_20px_50px_rgba(0,0,0,0.3)]">

                    <div class="flex items-center justify-between mb-12">
                        <h1 class="text-3xl font-black italic uppercase tracking-tighter text-white">
                            Керування <span class="text-indigo-500 drop-shadow-[0_0_10px_rgba(99,102,241,0.6)]">навантаженням</span>
                        </h1>
                        <div class="bg-indigo-500/10 border border-indigo-500/20 px-4 py-2 rounded-2xl">
                            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-400">Admin Mode Active</span>
                        </div>
                    </div>

                    <div class="grid lg:grid-cols-2 gap-10">

                        <div class="space-y-6">
                            <h2 class="text-xs font-black text-indigo-400 uppercase tracking-widest ml-4 flex items-center gap-2">
                                <span class="w-2 h-2 bg-indigo-500 rounded-full animate-ping"></span>
                                Діючі менеджери
                            </h2>

                            <div class="grid gap-4">
                                <div v-for="admin in admins" :key="admin.id"
                                     class="flex items-center justify-between p-6 bg-slate-900/60 border border-white/5 rounded-[2rem] hover:border-indigo-500/40 transition-all duration-300 group shadow-lg">
                                    <div>
                                        <div class="font-black text-white uppercase text-base group-hover:text-indigo-400 transition-colors">{{ admin.name }}</div>
                                        <div class="flex items-center gap-3 mt-1">
                                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">Активні пости: {{ admin.managed_posts_count }}</div>
                                            <div class="w-1 h-1 bg-slate-700 rounded-full"></div>
                                            <div class="text-[10px] text-slate-500 font-medium">{{ admin.email }}</div>
                                        </div>
                                        <button @click="loginAs(admin.id)" class="mt-3 text-[9px] font-black uppercase text-indigo-500 hover:text-white transition-colors">
                                            Імітувати вхід →
                                        </button>
                                    </div>

                                    <div class="flex flex-col items-end gap-2">
                                        <span class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Вага %</span>
                                        <input
                                            type="number"
                                            v-model="admin.assignment_weight"
                                            @change="updateWeight(admin.id, admin.assignment_weight)"
                                            class="w-20 border-none bg-slate-950/50 text-indigo-400 rounded-xl font-black text-center focus:ring-2 focus:ring-indigo-500 shadow-inner"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h2 class="text-xs font-black text-slate-500 uppercase tracking-widest ml-4">База користувачів</h2>

                            <div class="bg-slate-950/30 rounded-[2.5rem] border border-white/5 p-4">
                                <div class="max-h-[500px] overflow-y-auto custom-scrollbar pr-2 space-y-2">
                                    <div v-for="user in allUsers" :key="user.id"
                                         class="flex items-center justify-between p-4 hover:bg-white/5 rounded-2xl transition duration-200 border border-transparent hover:border-white/5 group">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-300 group-hover:text-white transition-colors">{{ user.name }}</span>
                                            <span class="text-[10px] text-slate-500 font-medium">{{ user.email }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button @click="loginAs(user.id)"
                                                    class="p-2 text-slate-500 hover:text-indigo-400 transition-colors"
                                                    title="Тест вхід">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                </svg>
                                            </button>
                                            <button
                                                type="button"
                                                @click="makeAdmin(user.id)"
                                                class="bg-white/5 hover:bg-indigo-600 text-slate-300 hover:text-white px-5 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-sm active:scale-95 border border-white/5"
                                            >
                                                Призначити
                                            </button>
                                        </div>
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

<style scoped>
/* Стилізація скроллбару для темної теми */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.02);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.2);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(99, 102, 241, 0.5);
}
</style>

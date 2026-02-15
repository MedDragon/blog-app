<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome" />

    <div class="relative min-h-screen bg-gray-900 flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-blue-600/20 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10 text-center px-4">
            <div class="mb-6 flex justify-center">
                <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center shadow-2xl rotate-3 hover:rotate-0 transition duration-500">
                    <svg class="w-12 h-12 text-indigo-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 22.525H0V1.475h24v21.05zM1.5 21.025h21V2.975h-21v18.05z"/><path d="M15.45 7.425h-6.9v1.5h6.9v-1.5zM15.45 11.25h-6.9v1.5h6.9v-1.5zM12.45 15.075h-3.9v1.5h3.9v-1.5z"/></svg>
                </div>
            </div>

            <h1 class="text-6xl md:text-8xl font-black text-white uppercase tracking-tighter mb-4">
                Tech<span class="text-indigo-500">Blog</span>
            </h1>
            <p class="text-gray-400 text-lg md:text-xl max-w-xl mx-auto mb-10 font-medium">
                Діліться ідеями, обговорюйте новини та ставте лайки найкращим постам нашої спільноти.
            </p>

            <div v-if="canLogin" class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-xl shadow-indigo-500/20">
                    Перейти до стрічки
                </Link>

                <template v-else>
                    <Link :href="route('login')" class="px-10 py-4 bg-white text-gray-900 rounded-2xl font-black uppercase tracking-widest hover:bg-gray-100 transition shadow-xl">
                        Увійти
                    </Link>
                    <Link v-if="canRegister" :href="route('register')" class="px-10 py-4 bg-transparent border-2 border-gray-700 text-white rounded-2xl font-black uppercase tracking-widest hover:border-white transition">
                        Реєстрація
                    </Link>
                </template>
            </div>
        </div>

        <div class="absolute bottom-10 text-gray-600 text-[10px] font-black uppercase tracking-[0.3em]">
            Built with Laravel 11 • Vue 3 • Tailwind CSS
        </div>
    </div>
</template>

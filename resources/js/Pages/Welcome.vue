<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Вітаємо у Prostir" />

    <div class="relative min-h-screen bg-gray-900 flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-indigo-600/10 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-blue-600/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s"></div>
        </div>

        <div class="relative z-10 text-center px-4">
            <div class="mb-12 flex justify-center group">
                <div class="relative w-24 h-24 flex items-center justify-center transition-all duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-indigo-600 rounded-[2.5rem] rotate-12 group-hover:rotate-0 transition-transform duration-500 shadow-2xl shadow-indigo-500/50"></div>
                    <div class="absolute inset-0 bg-slate-800 rounded-[2.5rem] -rotate-6 group-hover:rotate-0 transition-transform duration-500 border border-white/10 backdrop-blur-xl"></div>
                    <span class="relative text-white font-black text-5xl italic tracking-tighter select-none">P</span>
                </div>
            </div>

            <h1 class="text-7xl md:text-9xl font-black text-white uppercase tracking-tighter mb-6 italic">
                Prostir<span class="text-indigo-500">.</span>
            </h1>

            <p class="text-slate-400 text-sm md:text-base max-w-xl mx-auto mb-12 font-black uppercase tracking-[0.3em] leading-relaxed opacity-70">
                Платформа для обміну думками, новинами та розвитку спільноти.
            </p>

            <div v-if="canLogin" class="flex flex-col gap-4 items-center">

                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="px-12 py-5 bg-indigo-600 text-white rounded-[2rem] font-black uppercase tracking-[0.2em] text-[11px] hover:bg-indigo-500 transition-all shadow-[0_20px_40px_rgba(79,70,229,0.3)] active:scale-95"
                >
                    Перейти до консолі
                </Link>

                <template v-else>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <Link
                            :href="route('login')"
                            class="px-10 py-5 bg-white text-gray-900 rounded-[2.5rem] font-black uppercase tracking-[0.2em] text-[11px] hover:bg-slate-200 transition-all shadow-2xl active:scale-95"
                        >
                            Увійти
                        </Link>

                        <Link
                            :href="route('dashboard')"
                            class="px-10 py-5 bg-slate-800/80 text-white border border-white/10 rounded-[2.5rem] font-black uppercase tracking-[0.2em] text-[11px] hover:bg-slate-700 transition-all active:scale-95 backdrop-blur-md"
                        >
                            Переглянути як гість
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-10 py-5 bg-transparent border-2 border-indigo-600 text-indigo-400 rounded-[2.5rem] font-black uppercase tracking-[0.2em] text-[11px] hover:border-indigo-400 hover:text-indigo-300 transition-all active:scale-95 shadow-[0_0_20px_rgba(79,70,229,0.2)]"
                        >
                            Реєстрація
                        </Link>
                    </div>
                </template>
            </div>
        </div>

        <div class="absolute bottom-10 flex flex-col items-center gap-2">
            <div class="h-px w-12 bg-indigo-500/30 mb-2"></div>
            <div class="text-slate-700 text-[9px] font-black uppercase tracking-[0.5em]">
                Prostir Engine 2026 • v1.0
            </div>
        </div>
    </div>
</template>

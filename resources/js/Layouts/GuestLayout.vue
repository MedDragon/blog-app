<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#070b14] relative selection:bg-indigo-500/30">

        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-[10%] -right-[10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px]"></div>
        </div>

        <div class="z-10 w-full sm:max-w-md mt-6 px-10 py-12 bg-slate-900/40 backdrop-blur-2xl shadow-[0_30px_100px_rgba(0,0,0,0.5)] overflow-hidden sm:rounded-[3rem] border border-white/10 relative">

            <div class="flex justify-center mb-12">
                <Link href="/">
                    <ApplicationLogo class="transform scale-125 transition-transform duration-500 hover:scale-110" />
                </Link>
            </div>

            <slot />
        </div>

        <div class="z-10 mt-8 text-[10px] text-slate-600 font-black uppercase tracking-[0.3em]">
            © 2026 Blog-App • Developed with Laravel & Vue
        </div>
    </div>
</template>

<style>
/**
 * Глобальні стилі для виправлення автозаповнення браузером.
 * Без scoped, щоб стилі достукалися до TextInput у слоті.
 */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    -webkit-text-fill-color: white !important;
    transition: background-color 5000s ease-in-out 0s;
    box-shadow: 0 0 0px 1000px #0f172a inset !important;
    caret-color: white;
}
</style>

<style scoped>
/** * Анімація плавної появи картки при завантаженні сторінки
 */
.z-10 {
    animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

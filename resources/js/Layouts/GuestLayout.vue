<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#f8fafc]">
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-indigo-100/50 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-blue-100/50 rounded-full blur-3xl"></div>
        </div>

        <div class="z-10 w-full sm:max-w-md mt-6 px-10 py-12 bg-white shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] overflow-hidden sm:rounded-[2.5rem] border border-gray-50">
            <div class="flex justify-center mb-8">
                <Link href="/">
                    <ApplicationLogo class="w-16 h-16 fill-current text-indigo-600" />
                </Link>
            </div>

            <slot />
        </div>

        <div class="z-10 mt-8 text-[10px] text-gray-400 font-bold uppercase tracking-widest">
            © 2026 Blog-App • Developed with Laravel & Vue
        </div>
    </div>
</template>

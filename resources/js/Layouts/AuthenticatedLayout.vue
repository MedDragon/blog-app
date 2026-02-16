<script setup>
import { ref } from 'vue';
import ApplicationLogo from '../Components/ApplicationLogo.vue';
import NavLink from '../Components/NavLink.vue';
import ResponsiveNavLink from '../Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-[#070b14] relative overflow-hidden text-slate-200">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-purple-600/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s;"></div>

        <div class="relative z-10">
            <nav class="bg-slate-950/80 backdrop-blur-2xl border-b border-white/5 sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-20">
                        <div class="flex items-center">
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo />
                                </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-12 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')"
                                         class="text-[10px] uppercase font-black tracking-[0.2em] text-slate-400 hover:text-white transition-all">
                                    Стрічка
                                </NavLink>

                                <NavLink
                                    v-if="$page.props.auth.user.role === 'superadmin'"
                                    :href="route('admin.control')"
                                    :active="route().current('admin.control')"
                                    class="text-[10px] uppercase font-black tracking-[0.2em] text-indigo-400 hover:text-indigo-300 transition-all"
                                >
                                    Адмін-панель
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="flex items-center gap-4 bg-white/[0.03] p-1.5 pr-5 rounded-2xl border border-white/10 hover:bg-white/[0.07] transition-all cursor-pointer group">
                                <Link :href="route('profile.edit')" class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg group-hover:scale-105 transition-transform overflow-hidden">
                                    <img v-if="$page.props.auth.user.avatar" :src="$page.props.auth.user.avatar" class="w-full h-full object-cover" />
                                    <span v-else>{{ $page.props.auth.user.name.charAt(0) }}</span>
                                </Link>
                                <div class="text-left">
                                    <div class="text-[11px] font-black text-white leading-none uppercase tracking-wide">{{ $page.props.auth.user.name }}</div>
                                    <Link :href="route('logout')" method="post" as="button" class="text-[9px] font-bold text-slate-500 hover:text-rose-500 transition-colors uppercase">Вийти</Link>
                                </div>
                            </div>
                        </div>

                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="p-2 rounded-xl text-slate-400 hover:bg-white/10 transition">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden bg-slate-950 border-t border-white/5">
                    <div class="pt-2 pb-3 space-y-1 px-4">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">Стрічка</ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.auth.user.role === 'superadmin'" :href="route('admin.control')" class="text-indigo-400">Адмін-панель</ResponsiveNavLink>
                    </div>
                </div>
            </nav>

            <header class="py-10" v-if="$slots.header">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main class="relative">
                <slot />
            </main>
        </div>
    </div>
</template>

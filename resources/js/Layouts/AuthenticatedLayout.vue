<script setup>
import { ref } from 'vue';
import ApplicationLogo from '../Components/ApplicationLogo.vue';
import NavLink from '../Components/NavLink.vue';
import ResponsiveNavLink from '../Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-[#f8fafc]">
            <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-20">
                        <div class="flex items-center">
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo class="block h-10 w-auto fill-current text-indigo-600" />
                                </Link>
                                <span class="ml-3 font-black text-xl uppercase tracking-tighter text-gray-900">Prostir</span>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="text-xs uppercase font-bold tracking-widest">
                                    Стрічка
                                </NavLink>

                                <NavLink
                                    v-if="$page.props.auth.user.role === 'superadmin'"
                                    :href="route('admin.control')"
                                    :active="route().current('admin.control')"
                                    class="text-xs uppercase font-bold tracking-widest text-indigo-600"
                                >
                                    Адмін-панель
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <div class="text-sm font-black text-gray-900 leading-none">{{ $page.props.auth.user.name }}</div>
                                    <Link :href="route('logout')" method="post" as="button" class="text-[10px] font-bold text-gray-400 uppercase hover:text-red-500 transition">Вийти</Link>
                                </div>

                                <Link :href="route('profile.edit')" class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-100 hover:scale-105 transition overflow-hidden border-2 border-white">
                                    <img v-if="$page.props.auth.user.avatar" :src="$page.props.auth.user.avatar" class="w-full h-full object-cover" />
                                    <span v-else>{{ $page.props.auth.user.name.charAt(0) }}</span>
                                </Link>
                            </div>
                        </div>

                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="p-2 rounded-md text-gray-400 hover:bg-gray-100 transition">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden bg-white border-t border-gray-100 shadow-xl">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">Стрічка</ResponsiveNavLink>

                        <ResponsiveNavLink
                            v-if="$page.props.auth.user.role === 'superadmin'"
                            :href="route('admin.control')"
                            :active="route().current('admin.control')"
                            class="text-indigo-600 font-black"
                        >
                            Адмін-панель
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-4 pb-1 border-t border-gray-200 bg-gray-50/50">
                        <div class="px-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold overflow-hidden border border-indigo-100">
                                <img v-if="$page.props.auth.user.avatar" :src="$page.props.auth.user.avatar" class="w-full h-full object-cover" />
                                <span v-else>{{ $page.props.auth.user.name.charAt(0) }}</span>
                            </div>
                            <div>
                                <div class="font-black text-sm text-gray-800 uppercase tracking-tight">{{ $page.props.auth.user.name }}</div>
                                <div class="font-medium text-[10px] text-gray-500">{{ $page.props.auth.user.email }}</div>
                            </div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">Профіль</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="text-red-600 font-bold">Вийти</ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <header class="bg-white py-10 shadow-sm" v-if="$slots.header">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main><slot /></main>
        </div>
    </div>
</template>

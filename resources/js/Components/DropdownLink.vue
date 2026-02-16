<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-5 py-3 text-start text-[10px] font-black uppercase tracking-[0.15em] text-slate-400 transition-all duration-200 ease-in-out hover:bg-indigo-600 hover:text-white focus:outline-none focus:bg-indigo-600 focus:text-white"
    >
        <slot />
    </Link>
</template>

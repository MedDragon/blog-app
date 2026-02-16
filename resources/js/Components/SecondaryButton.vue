<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-6 py-3 bg-white/5 border border-white/10 rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] text-slate-400 hover:bg-white/10 hover:text-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900 active:scale-95 disabled:opacity-25"
    >
        <slot />
    </button>
</template>

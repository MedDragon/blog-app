<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="w-5 h-5 rounded-lg border-white/10 bg-slate-900 text-indigo-600 shadow-sm focus:ring-offset-slate-900 focus:ring-indigo-500 transition-all cursor-pointer hover:border-indigo-500/50"
    />
</template>

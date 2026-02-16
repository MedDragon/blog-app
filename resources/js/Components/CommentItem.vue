<script setup>
const props = defineProps({
    comment: Object,
    user: Object,
    isReply: { type: Boolean, default: false },
    editingCommentId: Number,
    expandedComments: Object,
    repliesCount: { type: Number, default: 0 }
});

defineEmits(['reply', 'edit', 'delete', 'like', 'toggle-child']);
</script>

<template>
    <div :class="isReply ? 'flex gap-3 items-start' : 'flex gap-4 items-start'">
        <div :class="isReply ? 'w-8 h-8 rounded-lg text-[9px]' : 'w-10 h-10 rounded-xl text-xs'"
             class="bg-gray-100 flex items-center justify-center font-black shrink-0 overflow-hidden border-2 border-white shadow-sm">
            <img v-if="comment.user.avatar" :src="comment.user.avatar" class="w-full h-full object-cover" />
            <span v-else>{{ comment.user.name.charAt(0) }}</span>
        </div>

        <div :class="isReply ? 'bg-[#fcfdfe] p-4 rounded-2xl border border-gray-50 flex-1 shadow-sm' : 'bg-white p-5 rounded-[1.5rem] border border-gray-100 shadow-sm flex-1'">

            <div v-if="comment.body.includes('[quote]')" class="mb-3 p-3 bg-indigo-50/50 border-l-4 border-indigo-400 rounded-lg text-[10px] text-gray-400 italic">
                {{ comment.body.substring(comment.body.indexOf('[quote]') + 7, comment.body.indexOf('[/quote]')) }}
            </div>

            <div class="text-[10px] font-black text-indigo-600 mb-2 uppercase tracking-wider">{{ comment.user.name }}</div>

            <div v-if="editingCommentId === comment.id">
                <slot name="edit-form"></slot>
            </div>
            <div v-else class="text-gray-700 text-sm leading-relaxed">
                {{ comment.body.includes('[/quote]') ? comment.body.split('[/quote]')[1] : comment.body }}
            </div>

            <div class="flex gap-5 mt-4 pt-3 border-t border-gray-50 items-center flex-wrap">
                <button @click="$emit('reply', comment)" class="text-[9px] text-gray-500 font-black uppercase hover:text-indigo-600 transition-colors">Відповісти</button>

                <template v-if="comment.user_id === user.id">
                    <button @click="$emit('edit', comment)" class="text-[9px] text-gray-400 font-black uppercase hover:text-indigo-500 transition-colors">Редагувати</button>
                    <button @click="$emit('delete', comment.id)" class="text-[9px] text-red-300 font-black uppercase hover:text-red-500 transition-colors">Видалити</button>
                </template>

                <button @click="$emit('like', comment.id)" class="flex items-center gap-1 group">
                    <span :class="comment.is_liked ? 'text-red-500' : 'text-gray-400 group-hover:text-red-400'" class="text-[11px] font-black transition-all">
                        {{ comment.is_liked ? '❤' : '♡' }} {{ comment.likes_count || 0 }}
                    </span>
                </button>

                <button v-if="!isReply && repliesCount > 0"
                        @click="$emit('toggle-child', comment.id)"
                        class="ml-auto text-[9px] text-indigo-400 font-black uppercase hover:text-indigo-600 transition-all bg-indigo-50/50 px-2 py-1 rounded-lg">
                    {{ expandedComments['child_' + comment.id] ? 'Сховати гілку' : 'Гілка (' + repliesCount + ')' }}
                </button>
            </div>
        </div>
    </div>
</template>

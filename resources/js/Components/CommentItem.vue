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
             class="bg-slate-800 flex items-center justify-center font-black shrink-0 overflow-hidden border border-white/10 shadow-lg text-white">
            <img v-if="comment.user.avatar" :src="comment.user.avatar" class="w-full h-full object-cover" />
            <span v-else>{{ comment.user.name.charAt(0) }}</span>
        </div>

        <div :class="[
            'flex-1 border transition-all duration-300',
            isReply
                ? 'bg-slate-900/40 p-4 rounded-2xl border-white/5 shadow-sm'
                : 'bg-slate-800/40 p-5 rounded-[1.5rem] border-white/10 shadow-xl'
        ]">

            <div v-if="comment.body.includes('[quote]')"
                 class="mb-3 p-3 bg-indigo-500/5 border-l-2 border-indigo-500/50 rounded-lg text-[10px] text-slate-400 italic leading-relaxed">
                <span class="text-indigo-400/60 block mb-1 font-black uppercase tracking-tighter">Цитата:</span>
                {{ comment.body.substring(comment.body.indexOf('[quote]') + 7, comment.body.indexOf('[/quote]')) }}
            </div>

            <div class="text-[10px] font-black text-indigo-400 mb-2 uppercase tracking-widest flex justify-between items-center">
                {{ comment.user.name }}
                <span class="text-[8px] text-slate-600 font-bold uppercase tracking-tighter">
                    {{ new Date(comment.created_at).toLocaleDateString() }}
                </span>
            </div>

            <div v-if="editingCommentId === comment.id" class="my-2">
                <slot name="edit-form"></slot>
            </div>
            <div v-else class="text-slate-200 text-sm leading-relaxed opacity-90">
                {{ comment.body.includes('[/quote]') ? comment.body.split('[/quote]')[1].trim() : comment.body }}
            </div>

            <div class="flex gap-5 mt-4 pt-3 border-t border-white/5 items-center flex-wrap">
                <button @click="$emit('reply', comment)"
                        class="text-[9px] text-slate-500 font-black uppercase hover:text-indigo-400 transition-colors tracking-widest">
                    Відповісти
                </button>

                <template v-if="comment.user_id === user.id || user.role === 'admin' || user.role === 'superadmin'">
                    <button @click="$emit('edit', comment)"
                            class="text-[9px] text-slate-600 font-black uppercase hover:text-white transition-colors tracking-widest">
                        Редагувати
                    </button>
                    <button @click="$emit('delete', comment.id)"
                            class="text-[9px] text-rose-900 font-black uppercase hover:text-rose-500 transition-colors tracking-widest">
                        Видалити
                    </button>
                </template>

                <button @click="$emit('like', comment.id)" class="flex items-center gap-1.5 group ml-1">
                    <span :class="comment.is_liked ? 'text-rose-500' : 'text-slate-500 group-hover:text-rose-400'"
                          class="text-[11px] font-black transition-all">
                        {{ comment.is_liked ? '❤' : '♡' }} {{ comment.likes_count || 0 }}
                    </span>
                </button>

                <button v-if="!isReply && repliesCount > 0"
                        @click="$emit('toggle-child', comment.id)"
                        class="ml-auto text-[9px] text-indigo-400 font-black uppercase hover:bg-indigo-600 hover:text-white transition-all bg-indigo-500/10 border border-indigo-500/20 px-3 py-1.5 rounded-xl tracking-tighter">
                    {{ expandedComments['child_' + comment.id] ? 'Сховати гілку' : 'Гілка (' + repliesCount + ')' }}
                </button>
            </div>
        </div>
    </div>
</template>

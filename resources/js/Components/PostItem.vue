<script setup>
import { ref, computed } from 'vue';
import CommentItem from './CommentItem.vue';

const props = defineProps({
    post: Object,
    user: Object, // Може бути null для гостя
    editingPostId: Number,
    expandedPosts: Object,
    expandedComments: Object,
    editingCommentId: Number
});

const emit = defineEmits([
    'toggle-post', 'start-edit', 'delete-post', 'toggle-like',
    'toggle-comments', 'open-comment-modal', 'start-edit-comment',
    'delete-comment', 'toggle-like-comment', 'auth-error'
]);

const secureAction = (callback) => {
    if (!props.user) emit('auth-error');
    else callback();
};

const canEditOrDelete = computed(() => {
    if (!props.user) return false;
    if (props.user.role === 'superadmin') return true;
    if (props.user.role === 'admin' && props.post.manager_id === props.user.id) return true;
    return props.post.user_id === props.user.id;
});

const sortedComments = computed(() => {
    // Додано безпечну перевірку наявності масиву
    if (!props.post.comments) return [];
    let list = props.post.comments.filter(c => !c.parent_id) || [];
    return list.sort((a, b) => b.id - a.id);
});

const getReplies = (id) => {
    if (!props.post.comments) return [];
    return props.post.comments.filter(c => c.parent_id === id) || [];
};
const getRepliesCount = (id) => getReplies(id).length;
</script>

<template>
    <div class="mb-10 bg-slate-900/40 backdrop-blur-md border border-white/10 rounded-[2.5rem] overflow-hidden shadow-2xl transition-all hover:border-indigo-500/30">
        <div class="p-10 pb-6">
            <div class="flex justify-between items-start mb-6">
                <div v-if="editingPostId === post.id" class="flex-1 mr-4">
                    <slot name="edit-post-form"></slot>
                </div>
                <div v-else class="flex-1">
                    <h3 class="text-3xl font-black text-white tracking-tight italic uppercase">{{ post.title }}</h3>
                </div>
                <span class="px-4 py-1.5 bg-white/5 text-slate-500 rounded-2xl text-[10px] font-black uppercase tracking-widest shrink-0 ml-4 border border-white/5">ID: {{ post.id }}</span>
            </div>

            <div v-if="editingPostId !== post.id" class="text-slate-200 text-[1.05rem] leading-relaxed">
                <p class="whitespace-pre-wrap opacity-90">
                    {{ expandedPosts[post.id] || post.content.length <= 400 ? post.content : post.content.substring(0, 400) + '...' }}
                </p>
                <div class="flex items-center gap-6 mt-6">
                    <button v-if="post.content.length > 400" @click="$emit('toggle-post', post.id)" class="text-indigo-400 font-black uppercase text-[11px] tracking-widest hover:text-indigo-300 transition-all">
                        {{ expandedPosts[post.id] ? 'Згорнути' : 'Читати повністю' }}
                    </button>
                    <div v-if="canEditOrDelete" class="flex gap-4">
                        <button @click="$emit('start-edit', post)" class="text-slate-500 font-black uppercase text-[11px] tracking-widest hover:text-white">Редагувати</button>
                        <button @click="$emit('delete-post', post.id)" class="text-rose-900 font-black uppercase text-[11px] tracking-widest hover:text-rose-500">Видалити</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-slate-950/40 backdrop-blur-xl p-8 pt-6 border-t border-white/5 flex flex-wrap justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black overflow-hidden shadow-lg ring-2 ring-white/10">
                    <img v-if="post.user?.avatar" :src="post.user.avatar" class="w-full h-full object-cover" />
                    <span v-else>{{ post.user?.name?.charAt(0) || '?' }}</span>
                </div>
                <span class="text-white font-black text-sm uppercase tracking-tight">{{ post.user?.name || 'Анонім' }}</span>
            </div>

            <div class="flex gap-3">
                <button @click.stop="secureAction(() => $emit('toggle-like', post.id))" class="flex items-center gap-2.5 bg-white/5 px-5 py-3 rounded-2xl border border-white/5 transition active:scale-90 group hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" :class="post.is_liked ? 'fill-rose-500 stroke-rose-500' : 'fill-none stroke-slate-400 group-hover:stroke-rose-400'" class="w-5 h-5 transition-all duration-300" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span :class="post.is_liked ? 'text-rose-500' : 'text-slate-400'" class="font-black text-xs">{{ post.likes_count || 0 }}</span>
                </button>

                <button @click.stop="$emit('toggle-comments', post.id)" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl shadow-lg shadow-indigo-500/20 font-black text-xs hover:bg-indigo-500 transition uppercase tracking-widest">
                    {{ post.comments_count || 0 }} Відгуків
                </button>
            </div>
        </div>

        <div v-if="expandedComments[post.id]" class="p-8 pt-0 bg-black/20 border-t border-white/5">
            <div class="pt-8 space-y-6">
                <button @click="secureAction(() => $emit('open-comment-modal', post.id))" class="w-full py-4 rounded-2xl border-2 border-dashed border-white/10 text-slate-500 font-bold text-xs uppercase hover:border-indigo-500/40 transition">
                    + Залишити відгук
                </button>

                <template v-if="sortedComments.length > 0">
                    <div v-for="comment in sortedComments" :key="comment.id" class="space-y-4">
                        <CommentItem
                            :comment="comment"
                            :user="user || {}"
                            :editingCommentId="editingCommentId"
                            :expandedComments="expandedComments"
                            :repliesCount="getRepliesCount(comment.id)"
                            @reply="(c) => secureAction(() => $emit('open-comment-modal', post.id, c))"
                            @edit="(c) => $emit('start-edit-comment', c)"
                            @delete="(id) => $emit('delete-comment', id)"
                            @like="(id) => secureAction(() => $emit('toggle-like-comment', id))"
                            @toggle-child="(id) => $emit('toggle-comments', 'child_' + id)"
                        />
                        <div v-if="expandedComments['child_' + comment.id]" class="ml-8 md:ml-14 space-y-4 border-l-2 border-indigo-500/20 pl-6">
                            <CommentItem
                                v-for="reply in getReplies(comment.id)" :key="reply.id"
                                :comment="reply"
                                :user="user || {}"
                                :isReply="true"
                                :editingCommentId="editingCommentId"
                                @reply="(c) => secureAction(() => $emit('open-comment-modal', post.id, c))"
                                @edit="(c) => $emit('start-edit-comment', c)"
                                @delete="(id) => $emit('delete-comment', id)"
                                @like="(id) => secureAction(() => $emit('toggle-like-comment', id))"
                            />
                        </div>
                    </div>
                </template>
                <div v-else class="py-10 text-center text-slate-600 font-bold uppercase text-[10px] tracking-widest">
                    Коментарів ще немає
                </div>
            </div>
        </div>
    </div>
</template>

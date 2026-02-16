<script setup>
import { ref, computed } from 'vue';
import CommentItem from './CommentItem.vue';

const props = defineProps({
    post: Object,
    user: Object, // Це $page.props.auth.user
    editingPostId: Number,
    expandedPosts: Object,
    expandedComments: Object,
    editingCommentId: Number
});

const emit = defineEmits([
    'toggle-post', 'start-edit', 'delete-post', 'toggle-like',
    'toggle-comments', 'open-comment-modal', 'start-edit-comment',
    'delete-comment', 'toggle-like-comment'
]);

// Перевірка прав (Helper functions)
const canEditOrDelete = computed(() => {
    if (props.user.role === 'superadmin') return true;
    if (props.user.role === 'admin' && props.post.manager_id === props.user.id) return true;
    return props.post.user_id === props.user.id;
});

// --- СОРТУВАННЯ КОМЕНТАРІВ (Твоя логіка без змін) ---
const commentSortMethod = ref('newest');
const sortedComments = computed(() => {
    let list = props.post.comments.filter(c => !c.parent_id);
    if (commentSortMethod.value === 'newest') return list.sort((a, b) => b.id - a.id);
    if (commentSortMethod.value === 'oldest') return list.sort((a, b) => a.id - b.id);
    if (commentSortMethod.value === 'popular') {
        return list.sort((a, b) => {
            const repliesA = props.post.comments.filter(c => c.parent_id === a.id);
            const weightA = (a.likes_count || 0) + (repliesA.reduce((sum, r) => sum + (r.likes_count || 0), 0) * 0.5);
            const repliesB = props.post.comments.filter(c => c.parent_id === b.id);
            const weightB = (b.likes_count || 0) + (repliesB.reduce((sum, r) => sum + (r.likes_count || 0), 0) * 0.5);
            return weightB - weightA;
        });
    }
    if (commentSortMethod.value === 'active') {
        return list.sort((a, b) => {
            const repliesA = props.post.comments.filter(c => c.parent_id === a.id).length;
            const repliesB = props.post.comments.filter(c => c.parent_id === b.id).length;
            return repliesB - repliesA;
        });
    }
    return list;
});

const getReplies = (commentId) => props.post.comments.filter(c => c.parent_id === commentId);
const getRepliesCount = (commentId) => props.post.comments.filter(c => c.parent_id === commentId).length;
</script>

<template>
    <div class="mb-10 bg-white shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-[2.5rem] overflow-hidden border border-white transition-all duration-500 hover:shadow-indigo-100 hover:border-indigo-50">

        <div class="p-10 pb-6">
            <div class="flex justify-between items-start mb-6">
                <div v-if="editingPostId === post.id" class="flex-1 mr-4">
                    <slot name="edit-post-form"></slot>
                </div>
                <div v-else class="flex-1">
                    <h3 class="text-3xl font-black text-gray-900 tracking-tight leading-[1.1]">{{ post.title }}</h3>
                    <p v-if="user.role === 'superadmin' && post.manager" class="text-[10px] font-bold text-indigo-400 mt-2 uppercase tracking-tighter">
                        Куратор: {{ post.manager.name }}
                    </p>
                </div>
                <span class="px-4 py-1.5 bg-gray-50 text-gray-400 rounded-2xl text-[10px] font-black uppercase tracking-widest shrink-0 ml-4 shadow-sm">
                    ID: {{ post.id }}
                </span>
            </div>

            <div v-if="editingPostId !== post.id" class="text-gray-600 text-[1.05rem] leading-relaxed">
                <p class="whitespace-pre-wrap">{{ expandedPosts[post.id] || post.content.length <= 400 ? post.content : post.content.substring(0, 400) + '...' }}</p>
                <div class="flex items-center gap-6">
                    <button v-if="post.content.length > 400" @click="$emit('toggle-post', post.id)" class="mt-4 text-indigo-600 font-black uppercase text-[11px] tracking-[0.2em] hover:gap-3 transition-all flex items-center">
                        {{ expandedPosts[post.id] ? 'Згорнути' : 'Читати повністю' }}
                    </button>

                    <div v-if="canEditOrDelete" class="flex gap-4">
                        <button @click="$emit('start-edit', post)" class="mt-4 text-gray-400 font-black uppercase text-[11px] tracking-[0.2em] hover:text-indigo-500 transition-colors">Редагувати</button>
                        <button @click="$emit('delete-post', post.id)" class="mt-4 text-red-300 font-black uppercase text-[11px] tracking-[0.2em] hover:text-red-600 transition-colors">Видалити</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#fafbfd]/80 backdrop-blur-sm p-8 pt-6 border-t border-gray-50 flex flex-wrap justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black overflow-hidden shadow-lg ring-4 ring-white">
                    <img v-if="post.user.avatar" :src="post.user.avatar" class="w-full h-full object-cover" />
                    <span v-else>{{ post.user.name.charAt(0) }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-gray-900 font-black text-sm uppercase">{{ post.user.name }}</span>
                    <span class="text-[10px] text-gray-400 font-bold italic" v-if="post.user.bio">{{ post.user.bio.substring(0, 30) }}...</span>
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="$emit('toggle-like', post.id)" class="flex items-center gap-2.5 bg-white px-5 py-3 rounded-2xl shadow-sm border border-gray-100 transition active:scale-90 group">
                    <svg xmlns="http://www.w3.org/2000/svg" :class="post.is_liked ? 'fill-red-500 stroke-red-500' : 'fill-none stroke-gray-400 group-hover:stroke-red-400'" class="w-5 h-5 transition-all duration-300" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span :class="post.is_liked ? 'text-red-500' : 'text-gray-500'" class="font-black text-xs">{{ post.likes_count || 0 }}</span>
                </button>
                <button @click="$emit('toggle-comments', post.id)" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl shadow-lg font-black text-xs hover:bg-indigo-700 transition">
                    {{ post.comments_count || 0 }} Відгуків
                </button>
            </div>
        </div>

        <div v-if="expandedComments[post.id]" class="p-8 pt-0 bg-[#fafbfd]/80 border-t border-gray-50 animate-in">
            <div class="pt-8 space-y-6">
                <div class="flex items-center justify-between mb-2 px-2">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Сортувати:</span>
                    <div class="flex gap-4">
                        <button v-for="m in ['newest', 'popular', 'active', 'oldest']" :key="m" @click="commentSortMethod = m" :class="commentSortMethod === m ? 'text-indigo-600 scale-110' : 'text-gray-300 hover:text-gray-400'" class="text-[9px] font-black uppercase transition-all tracking-tighter">
                            <span v-if="m === 'newest'">Нові</span>
                            <span v-if="m === 'popular'">Топ</span>
                            <span v-if="m === 'active'">Дискусії</span>
                            <span v-if="m === 'oldest'">Старі</span>
                        </button>
                    </div>
                </div>

                <button @click="$emit('open-comment-modal', post.id)" class="w-full py-4 rounded-2xl border-2 border-dashed border-gray-200 text-gray-400 font-bold text-xs uppercase hover:border-indigo-300 transition">
                    + Написати свою думку
                </button>

                <div v-for="comment in sortedComments" :key="comment.id" class="space-y-4">
                    <CommentItem
                        :comment="comment" :user="user" :editingCommentId="editingCommentId"
                        :expandedComments="expandedComments" :repliesCount="getRepliesCount(comment.id)"
                        @reply="(c) => $emit('open-comment-modal', post.id, c)"
                        @edit="(c) => $emit('start-edit-comment', c)"
                        @delete="(id) => $emit('delete-comment', id)"
                        @like="(id) => $emit('toggle-like-comment', id)"
                        @toggle-child="(id) => $emit('toggle-comments', 'child_' + id)"
                    >
                        <template #edit-form><slot name="edit-comment-form"></slot></template>
                    </CommentItem>

                    <div v-if="expandedComments['child_' + comment.id]" class="ml-14 space-y-4 border-l-2 border-indigo-50 pl-6">
                        <CommentItem
                            v-for="reply in getReplies(comment.id)" :key="reply.id"
                            :comment="reply" :user="user" :isReply="true" :editingCommentId="editingCommentId"
                            @reply="(c) => $emit('open-comment-modal', post.id, c)"
                            @edit="(c) => $emit('start-edit-comment', c)"
                            @delete="(id) => $emit('delete-comment', id)"
                            @like="(id) => $emit('toggle-like-comment', id)"
                        >
                            <template #edit-form><slot name="edit-comment-form"></slot></template>
                        </CommentItem>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

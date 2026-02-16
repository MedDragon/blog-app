<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostItem from '@/Components/PostItem.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({ posts: Array });
const user = computed(() => usePage().props.auth.user);

// --- СТАН ---
const isPostModalOpen = ref(false);
const isCommentModalOpen = ref(false);
const expandedComments = ref({});
const expandedPosts = ref({});
const replyPreview = ref(null);
const editingPostId = ref(null);
const editingCommentId = ref(null);
const showScrollTop = ref(false);

// --- СОРТУВАННЯ ПОСТІВ ---
const postSortMethod = ref('newest');

const sortedPosts = computed(() => {
    let list = [...props.posts];

    if (postSortMethod.value === 'popular') {
        return list.sort((a, b) => {
            // Вага А = лайки поста + 0.1 * сума лайків усіх коментів (будь-якого рівня)
            const commLikesA = a.comments.reduce((sum, c) => sum + (c.likes_count || 0), 0);
            const weightA = (a.likes_count || 0) + (commLikesA * 0.1);

            const commLikesB = b.comments.reduce((sum, c) => sum + (c.likes_count || 0), 0);
            const weightB = (b.likes_count || 0) + (commLikesB * 0.1);

            return weightB - weightA;
        });
    }

    if (postSortMethod.value === 'newest') return list.sort((a, b) => b.id - a.id);
    if (postSortMethod.value === 'oldest') return list.sort((a, b) => a.id - b.id);
    if (postSortMethod.value === 'hyped') return list.sort((a, b) => (b.comments_count || 0) - (a.comments_count || 0));
    return list;
});

// --- ФОРМИ ---
const postForm = useForm({ title: '', content: '', source: 'Source 1' });
const editPostForm = useForm({ title: '', content: '' });
const commentForm = useForm({ body: '', post_id: null, parent_id: null });
const editCommentForm = useForm({ body: '' });

// --- МЕТОДИ СКРОЛУ ---
const handleScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };

onMounted(() => { window.addEventListener('scroll', handleScroll); });
onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });

// --- МЕТОДИ ПОСТІВ ---
const submitPost = () => postForm.post(route('posts.store'), {
    onSuccess: () => { postForm.reset(); isPostModalOpen.value = false; }
});

const submitEditPost = (id) => editPostForm.patch(route('posts.update', id), {
    onSuccess: () => editingPostId.value = null,
    preserveScroll: true
});

const deletePost = (id) => {
    if (confirm('Ви впевнені, що хочете видалити цей пост?')) {
        router.delete(route('posts.destroy', id), { preserveScroll: true });
    }
};

// --- МЕТОДИ КОМЕНТАРІВ ---
const openCommentModal = (postId, comment = null) => {
    commentForm.reset();
    commentForm.post_id = postId;
    replyPreview.value = null;
    if (comment) {
        commentForm.parent_id = comment.parent_id ? comment.parent_id : comment.id;
        const cleanBody = comment.body.includes('[/quote]') ? comment.body.split('[/quote]')[1].trim() : comment.body;
        replyPreview.value = {
            user: comment.user.name,
            text: cleanBody.length > 100 ? cleanBody.substring(0, 100) + '...' : cleanBody
        };
    }
    isCommentModalOpen.value = true;
};

const submitComment = () => {
    let finalBody = replyPreview.value
        ? `[quote]${replyPreview.value.user}: ${replyPreview.value.text}[/quote] ${commentForm.body}`
        : commentForm.body;

    commentForm.transform(data => ({ ...data, body: finalBody })).post(route('comments.store'), {
        preserveScroll: true, // ВИПРАВЛЕНО: тепер сторінка не стрибає вгору
        onSuccess: () => {
            const pid = commentForm.post_id;
            const parentId = commentForm.parent_id;
            commentForm.reset();
            isCommentModalOpen.value = false;
            expandedComments.value[pid] = true;
            if (parentId) expandedComments.value['child_' + parentId] = true;
        },
    });
};

const submitEditComment = (id) => editCommentForm.patch(route('comments.update', id), {
    onSuccess: () => editingCommentId.value = null,
    preserveScroll: true
});

const deleteComment = (id) => {
    if (confirm('Видалити цей коментар?')) {
        router.delete(route('comments.destroy', id), { preserveScroll: true });
    }
};

const toggleLike = (id, type) => router.post(route('likes.toggle'), { id, type }, { preserveScroll: true });
</script>

<template>
    <Head title="Стрічка" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <h2 class="font-black text-3xl text-gray-900 uppercase tracking-tighter italic">Стрічка Prostir</h2>

                <div class="flex bg-white p-1.5 rounded-2xl shadow-sm border border-gray-100 overflow-x-auto no-scrollbar">
                    <button v-for="method in ['newest', 'popular', 'hyped', 'oldest']" :key="method"
                            @click="postSortMethod = method"
                            :class="postSortMethod === method ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-gray-400 hover:text-gray-600'"
                            class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap"
                    >
                        <span v-if="method === 'newest'">Свіжі</span>
                        <span v-if="method === 'popular'">Топ Лайків</span>
                        <span v-if="method === 'hyped'">Обговорювані</span>
                        <span v-if="method === 'oldest'">Архів</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12 bg-[#f8fafc] min-h-screen pb-32">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <PostItem
                    v-for="post in sortedPosts" :key="post.id"
                    :post="post" :user="user" :editingPostId="editingPostId"
                    :expandedPosts="expandedPosts" :expandedComments="expandedComments" :editingCommentId="editingCommentId"
                    @toggle-post="id => expandedPosts[id] = !expandedPosts[id]"
                    @start-edit="p => { editingPostId = p.id; editPostForm.title = p.title; editPostForm.content = p.content; }"
                    @delete-post="deletePost"
                    @toggle-like="id => toggleLike(id, 'post')"
                    @toggle-comments="id => expandedComments[id] = !expandedComments[id]"
                    @open-comment-modal="openCommentModal"
                    @start-edit-comment="c => { editingCommentId = c.id; editCommentForm.body = c.body.includes('[/quote]') ? c.body.split('[/quote]')[1].trim() : c.body; }"
                    @delete-comment="deleteComment"
                    @toggle-like-comment="id => toggleLike(id, 'comment')"
                >
                    <template #edit-post-form>
                        <input v-model="editPostForm.title" class="w-full text-xl font-black border-indigo-100 rounded-xl mb-2 focus:ring-indigo-500" />
                        <textarea v-model="editPostForm.content" rows="6" class="w-full border-gray-100 rounded-2xl p-4 focus:ring-indigo-500"></textarea>
                        <div class="flex gap-2 mt-4">
                            <button @click="submitEditPost(post.id)" class="bg-indigo-600 text-white px-6 py-2 rounded-xl text-[10px] font-black uppercase">Зберегти</button>
                            <button @click="editingPostId = null" class="text-gray-400 text-[10px] font-black uppercase">Скасувати</button>
                        </div>
                    </template>

                    <template #edit-comment-form>
                        <textarea v-model="editCommentForm.body" class="w-full border-gray-100 rounded-xl text-sm p-2 mb-2"></textarea>
                        <div class="flex gap-2">
                            <button @click="submitEditComment(editingCommentId)" class="text-[9px] font-black text-green-500 uppercase">Оновити</button>
                            <button @click="editingCommentId = null" class="text-[9px] font-black text-gray-400 uppercase">Скасувати</button>
                        </div>
                    </template>
                </PostItem>
            </div>
        </div>

        <Transition name="fade">
            <button v-if="showScrollTop" @click="scrollToTop" class="fixed bottom-10 left-10 z-50 bg-white text-indigo-600 w-14 h-14 rounded-2xl shadow-xl border border-indigo-50 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all active:scale-90 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 group-hover:-translate-y-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" />
                </svg>
            </button>
        </Transition>

        <button @click="isPostModalOpen = true" class="fixed bottom-10 right-10 z-50 bg-indigo-600 text-white px-8 h-16 rounded-full shadow-lg flex items-center gap-3 hover:scale-105 active:scale-95 transition-all group">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 group-hover:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            <span class="font-black uppercase tracking-widest text-xs">Додати пост</span>
        </button>

        <div v-if="isPostModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white w-full max-w-2xl rounded-[2.5rem] p-10 shadow-2xl animate-in">
                <h3 class="text-2xl font-black uppercase mb-6 italic tracking-tight">Новий запис у Prostir</h3>
                <form @submit.prevent="submitPost" class="space-y-4">
                    <input v-model="postForm.title" placeholder="Заголовок" class="w-full border-gray-100 rounded-2xl p-4 focus:ring-indigo-500 font-bold" />
                    <textarea v-model="postForm.content" placeholder="Про що думаєте?" rows="5" class="w-full border-gray-100 rounded-2xl p-4 focus:ring-indigo-500"></textarea>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="isPostModalOpen = false" class="px-6 py-3 text-gray-400 font-bold uppercase text-xs">Скасувати</button>
                        <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-black uppercase text-xs shadow-lg hover:bg-indigo-700 transition">Опублікувати</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="isCommentModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white w-full max-w-xl rounded-[2.5rem] p-10 shadow-2xl animate-in">
                <div v-if="replyPreview" class="mb-6 p-4 bg-indigo-50 rounded-2xl border-l-4 border-indigo-500">
                    <div class="text-[10px] font-black text-indigo-600 uppercase mb-1">Відповідь для {{ replyPreview.user }}</div>
                    <div class="text-sm text-gray-500 italic">{{ replyPreview.text }}</div>
                </div>
                <form @submit.prevent="submitComment">
                    <textarea v-model="commentForm.body" placeholder="Ваша думка..." rows="4" class="w-full border-gray-100 rounded-2xl p-4 focus:ring-indigo-500 mb-4"></textarea>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="isCommentModalOpen = false" class="px-6 py-3 text-gray-400 font-bold uppercase text-xs">Закрити</button>
                        <button type="submit" class="bg-gray-900 text-white px-8 py-3 rounded-xl font-black uppercase text-xs hover:bg-black transition">Надіслати</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.animate-in { animation: slideIn 0.3s ease-out forwards; }
@keyframes slideIn { from { opacity: 0; transform: translateY(10px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
.fade-enter-active, .fade-leave-фactive { transition: opacity 0.3s, transform 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(20px); }
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

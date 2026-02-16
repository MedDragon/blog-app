<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostItem from '@/Components/PostItem.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({ posts: Array });
const user = computed(() => usePage().props.auth.user);

// --- СТАН (ПОВНИЙ) ---
const isPostModalOpen = ref(false);
const isCommentModalOpen = ref(false);
const expandedComments = ref({});
const expandedPosts = ref({});
const replyPreview = ref(null);
const editingPostId = ref(null);
const editingCommentId = ref(null);
const showScrollTop = ref(false);
const postSortMethod = ref('newest');

// --- СОРТУВАННЯ (ТВОЯ ЛОГІКА) ---
const sortedPosts = computed(() => {
    let list = [...props.posts];
    if (postSortMethod.value === 'popular') {
        return list.sort((a, b) => {
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

// --- МЕТОДИ (ПОВНИЙ СПИСОК) ---
const handleScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
onMounted(() => { window.addEventListener('scroll', handleScroll); });
onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });

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
        preserveScroll: true,
        onSuccess: () => {
            const pid = commentForm.post_id;
            commentForm.reset();
            isCommentModalOpen.value = false;
            expandedComments.value[pid] = true;
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
                <h2 class="font-black text-4xl text-white uppercase tracking-tighter italic drop-shadow-[0_0_15px_rgba(99,102,241,0.4)]">Стрічка Prostir</h2>

                <div class="flex bg-slate-900/60 p-1.5 rounded-2xl border border-white/10 backdrop-blur-xl overflow-x-auto no-scrollbar shadow-2xl">
                    <button v-for="method in ['newest', 'popular', 'hyped', 'oldest']" :key="method"
                            @click="postSortMethod = method"
                            :class="postSortMethod === method ? 'bg-indigo-600 text-white shadow-[0_0_15px_rgba(79,70,229,0.5)]' : 'text-slate-400 hover:text-slate-200'"
                            class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap"
                    >
                        <span v-if="method === 'newest'">Свіжі</span>
                        <span v-if="method === 'popular'">Топ Лайків</span>
                        <span v-if="method === 'hyped'">Гайп</span>
                        <span v-if="method === 'oldest'">Архів</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12 min-h-screen pb-32">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <PostItem
                    v-for="post in sortedPosts" :key="post.id"
                    class="mb-8 bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-[2.5rem] p-8 shadow-2xl hover:border-indigo-500/30 transition-all duration-500"
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
                        <div class="bg-slate-900/80 p-6 rounded-3xl border border-indigo-500/30">
                            <input v-model="editPostForm.title" class="w-full bg-slate-950 text-white text-xl font-black border-white/10 rounded-xl mb-3 focus:ring-indigo-500" />
                            <textarea v-model="editPostForm.content" rows="6" class="w-full bg-slate-950 text-slate-300 border-white/10 rounded-2xl p-4 focus:ring-indigo-500"></textarea>
                            <div class="flex gap-3 mt-4">
                                <button @click="submitEditPost(post.id)" class="bg-indigo-600 text-white px-8 py-3 rounded-xl text-[10px] font-black uppercase shadow-lg hover:bg-indigo-500 transition">Зберегти</button>
                                <button @click="editingPostId = null" class="text-slate-500 text-[10px] font-black uppercase hover:text-slate-300 transition">Скасувати</button>
                            </div>
                        </div>
                    </template>

                    <template #edit-comment-form>
                        <div class="bg-slate-900/50 p-4 rounded-2xl border border-white/5 mt-2">
                            <textarea v-model="editCommentForm.body" class="w-full bg-slate-950 border-white/10 rounded-xl text-slate-300 text-sm p-3 mb-2 focus:ring-indigo-500"></textarea>
                            <div class="flex gap-3">
                                <button @click="submitEditComment(editingCommentId)" class="text-[10px] font-black text-green-400 uppercase hover:text-green-300">Оновити</button>
                                <button @click="editingCommentId = null" class="text-[10px] font-black text-slate-500 uppercase hover:text-slate-300">Назад</button>
                            </div>
                        </div>
                    </template>
                </PostItem>
            </div>
        </div>

        <button v-if="showScrollTop" @click="scrollToTop" class="fixed bottom-10 left-10 z-50 bg-slate-800/80 backdrop-blur-md text-indigo-400 w-14 h-14 rounded-2xl shadow-2xl border border-white/10 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all active:scale-90 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 group-hover:-translate-y-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" /></svg>
        </button>

        <button @click="isPostModalOpen = true" class="fixed bottom-10 right-10 z-50 bg-indigo-600 text-white px-10 h-16 rounded-full shadow-[0_10px_40px_rgba(79,70,229,0.5)] flex items-center gap-3 hover:scale-105 active:scale-95 transition-all border border-white/20 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 group-hover:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            <span class="font-black uppercase tracking-widest text-xs">Додати пост</span>
        </button>

        <div v-if="isPostModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-xl">
            <div class="bg-slate-900 border border-white/10 w-full max-w-2xl rounded-[3rem] p-12 shadow-[0_0_100px_rgba(0,0,0,0.5)] animate-in">
                <h3 class="text-3xl font-black text-white uppercase mb-8 italic tracking-tighter">Новий пост у <span class="text-indigo-500">Prostir</span></h3>
                <form @submit.prevent="submitPost" class="space-y-6">
                    <input v-model="postForm.title" placeholder="Про що заголовок?" class="w-full bg-slate-950 border-white/10 rounded-2xl p-5 text-white focus:ring-indigo-500 font-bold placeholder:text-slate-600" />
                    <textarea v-model="postForm.content" placeholder="Ваша історія..." rows="6" class="w-full bg-slate-950 border-white/10 rounded-2xl p-5 text-slate-300 focus:ring-indigo-500 placeholder:text-slate-600"></textarea>
                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" @click="isPostModalOpen = false" class="px-8 py-4 text-slate-500 font-bold uppercase text-xs tracking-widest hover:text-white transition">Скасувати</button>
                        <button type="submit" class="bg-indigo-600 text-white px-10 py-4 rounded-2xl font-black uppercase text-xs shadow-xl hover:bg-indigo-500 transition tracking-widest">Опублікувати</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="isCommentModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-xl">
            <div class="bg-slate-900 border border-white/10 w-full max-w-xl rounded-[3rem] p-12 shadow-2xl animate-in">
                <div v-if="replyPreview" class="mb-8 p-5 bg-indigo-500/10 rounded-3xl border border-indigo-500/20">
                    <div class="text-[10px] font-black text-indigo-400 uppercase mb-2 tracking-widest">Відповідь для {{ replyPreview.user }}</div>
                    <div class="text-sm text-slate-400 italic">"{{ replyPreview.text }}"</div>
                </div>
                <form @submit.prevent="submitComment">
                    <textarea v-model="commentForm.body" placeholder="Твій коментар..." rows="4" class="w-full bg-slate-950 border-white/10 rounded-2xl p-5 text-slate-200 focus:ring-indigo-500 mb-6"></textarea>
                    <div class="flex justify-end gap-4">
                        <button type="button" @click="isCommentModalOpen = false" class="px-8 py-4 text-slate-500 font-bold uppercase text-xs hover:text-white transition">Закрити</button>
                        <button type="submit" class="bg-white text-black px-10 py-4 rounded-2xl font-black uppercase text-xs hover:bg-indigo-500 hover:text-white transition">Надіслати</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.animate-in { animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes slideIn { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostItem from '@/Components/PostItem.vue';
import { Head, useForm, router, usePage, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({ posts: Array });

// Юзер (завжди перевіряємо наявність)
const user = computed(() => {
    try {
        return usePage().props.auth?.user || null;
    } catch (e) {
        return null;
    }
});

// СТАН
const isPostModalOpen = ref(false);
const isCommentModalOpen = ref(false);
const isAuthWallOpen = ref(false);
const expandedComments = ref({});
const expandedPosts = ref({});
const replyPreview = ref(null);
const editingPostId = ref(null);
const editingCommentId = ref(null);
const showScrollTop = ref(false);
const postSortMethod = ref('newest');

// ФОРМИ
const postForm = useForm({ title: '', content: '', source: 'Source 1' });
const editPostForm = useForm({ title: '', content: '' });
const commentForm = useForm({ body: '', post_id: null, parent_id: null });
const editCommentForm = useForm({ body: '' });

// МЕТОД: Перемикання коментарів (БЕЗПЕЧНИЙ)
const handleToggleComments = (id) => {
    // Використовуємо просту перевірку, щоб не зациклити рендер
    if (!id) return;

    const stringId = String(id);
    if (expandedComments.value[stringId]) {
        delete expandedComments.value[stringId];
    } else {
        expandedComments.value[stringId] = true;
    }
    // Примусово оновлюємо посилання на об'єкт для Vue
    expandedComments.value = { ...expandedComments.value };
};

// СОРТУВАННЯ
const sortedPosts = computed(() => {
    if (!props.posts || !Array.isArray(props.posts)) return [];
    let list = [...props.posts];

    if (postSortMethod.value === 'popular') {
        return list.sort((a, b) => {
            const aLikes = (a.likes_count || 0) + (a.comments?.length || 0) * 0.5;
            const bLikes = (b.likes_count || 0) + (b.comments?.length || 0) * 0.5;
            return bLikes - aLikes;
        });
    }
    if (postSortMethod.value === 'newest') return list.sort((a, b) => b.id - a.id);
    if (postSortMethod.value === 'oldest') return list.sort((a, b) => a.id - b.id);
    if (postSortMethod.value === 'hyped') return list.sort((a, b) => (b.comments_count || 0) - (a.comments_count || 0));
    return list;
});

// ПЕРЕВІРКА ГОСТЯ
const checkGuest = (callback) => {
    if (!user.value) isAuthWallOpen.value = true;
    else callback();
};

// МЕТОДИ (Лайки, Коменти, Пости)
const toggleLike = (id, type) => checkGuest(() => {
    router.post(route('likes.toggle'), { id, type }, { preserveScroll: true });
});

const openCommentModal = (postId, comment = null) => {
    checkGuest(() => {
        commentForm.reset();
        commentForm.post_id = postId;
        replyPreview.value = null;
        if (comment) {
            commentForm.parent_id = comment.parent_id || comment.id;
            const cleanBody = comment.body.includes('[/quote]') ? comment.body.split('[/quote]')[1].trim() : comment.body;
            replyPreview.value = { user: comment.user?.name || 'User', text: cleanBody.substring(0, 50) + '...' };
        }
        isCommentModalOpen.value = true;
    });
};

const submitComment = () => {
    let finalBody = replyPreview.value ? `[quote]${replyPreview.value.user}: ${replyPreview.value.text}[/quote] ${commentForm.body}` : commentForm.body;
    commentForm.transform(data => ({ ...data, body: finalBody })).post(route('comments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            const pid = commentForm.post_id;
            commentForm.reset();
            isCommentModalOpen.value = false;
            handleToggleComments(pid);
        },
    });
};

const handleScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
onMounted(() => { window.addEventListener('scroll', handleScroll); });
onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });
</script>

<template>
    <Head title="Стрічка" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <h2 class="font-black text-4xl text-white uppercase italic tracking-tighter">Стрічка Prostir</h2>
                <div class="flex bg-slate-900/60 p-1.5 rounded-2xl border border-white/10 backdrop-blur-xl overflow-x-auto no-scrollbar shadow-2xl">
                    <button v-for="m in ['newest', 'popular', 'hyped', 'oldest']" :key="m" @click="postSortMethod = m"
                            :class="postSortMethod === m ? 'bg-indigo-600 text-white' : 'text-slate-400'"
                            class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        {{ m === 'newest' ? 'Свіжі' : m === 'popular' ? 'Топ' : m === 'hyped' ? 'Гайп' : 'Архів' }}
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12 min-h-screen pb-32">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <PostItem
                    v-for="post in sortedPosts" :key="post.id"
                    :post="post" :user="user" :editingPostId="editingPostId"
                    :expandedPosts="expandedPosts" :expandedComments="expandedComments" :editingCommentId="editingCommentId"
                    @toggle-post="id => expandedPosts[id] = !expandedPosts[id]"
                    @toggle-comments="handleToggleComments"
                    @toggle-like="id => toggleLike(id, 'post')"
                    @open-comment-modal="openCommentModal"
                    @start-edit="p => { editingPostId = p.id; editPostForm.title = p.title; editPostForm.content = p.content; }"
                    @delete-post="id => { if(confirm('Видалити?')) router.delete(route('posts.destroy', id), { preserveScroll: true }) }"
                    @start-edit-comment="c => { editingCommentId = c.id; editCommentForm.body = c.body; }"
                    @delete-comment="id => router.delete(route('comments.destroy', id), { preserveScroll: true })"
                    @toggle-like-comment="id => toggleLike(id, 'comment')"
                    @auth-error="isAuthWallOpen = true"
                >
                    <template #edit-post-form>
                        <div class="bg-slate-900/80 p-6 rounded-3xl border border-indigo-500/30">
                            <input v-model="editPostForm.title" class="w-full bg-slate-950 text-white p-3 rounded-xl mb-3" />
                            <textarea v-model="editPostForm.content" rows="6" class="w-full bg-slate-950 text-slate-300 p-4 rounded-2xl mb-4"></textarea>
                            <div class="flex gap-3">
                                <button @click="editPostForm.patch(route('posts.update', post.id), { onSuccess: () => editingPostId = null, preserveScroll: true })" class="bg-indigo-600 text-white px-6 py-2 rounded-xl text-[10px] font-black uppercase">Зберегти</button>
                                <button @click="editingPostId = null" class="text-slate-500 font-black uppercase text-[10px]">Скасувати</button>
                            </div>
                        </div>
                    </template>
                </PostItem>
            </div>
        </div>

        <button @click="checkGuest(() => isPostModalOpen = true)" class="fixed bottom-10 right-10 z-50 bg-indigo-600 text-white px-10 h-16 rounded-full shadow-2xl flex items-center gap-3 active:scale-95 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            <span class="font-black uppercase tracking-widest text-xs">Додати пост</span>
        </button>

        <div v-if="isAuthWallOpen" class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-950/95 backdrop-blur-2xl">
            <div class="bg-slate-900 border border-indigo-500/30 w-full max-w-md rounded-[3.5rem] p-12 text-center">
                <h3 class="text-3xl font-black text-white uppercase mb-4 italic">Хочеш у Prostir?</h3>
                <p class="text-slate-400 text-sm mb-10 font-bold uppercase tracking-widest opacity-60">Потрібна реєстрація</p>
                <div class="space-y-4">
                    <Link :href="route('login')" class="block w-full bg-white text-black py-5 rounded-2xl font-black uppercase text-xs">Увійти</Link>
                    <Link :href="route('register')" class="block w-full bg-indigo-600 text-white py-5 rounded-2xl font-black uppercase text-xs">Реєстрація</Link>
                    <button @click="isAuthWallOpen = false" class="text-slate-500 text-[10px] font-black uppercase mt-4">Назад</button>
                </div>
            </div>
        </div>

        <div v-if="isPostModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-xl">
            <div class="bg-slate-900 border border-white/10 w-full max-w-2xl rounded-[3rem] p-12 shadow-2xl">
                <h3 class="text-3xl font-black text-white uppercase mb-8 italic">Новий пост</h3>
                <form @submit.prevent="postForm.post(route('posts.store'), { onSuccess: () => isPostModalOpen = false })" class="space-y-6">
                    <input v-model="postForm.title" class="w-full bg-slate-950 border-white/10 rounded-2xl p-5 text-white" placeholder="Заголовок" />
                    <textarea v-model="postForm.content" rows="6" class="w-full bg-slate-950 border-white/10 rounded-2xl p-5 text-slate-300" placeholder="Зміст..."></textarea>
                    <div class="flex justify-end gap-4">
                        <button type="button" @click="isPostModalOpen = false" class="text-slate-500 font-bold uppercase text-xs">Скасувати</button>
                        <button type="submit" class="bg-indigo-600 text-white px-10 py-4 rounded-2xl font-black uppercase text-xs shadow-xl">Опублікувати</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="isCommentModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-xl">
            <div class="bg-slate-900 border border-white/10 w-full max-w-xl rounded-[3rem] p-12 shadow-2xl">
                <div v-if="replyPreview" class="mb-6 p-4 bg-indigo-500/10 rounded-2xl text-xs text-slate-400 italic">Відповідь для {{ replyPreview.user }}: "{{ replyPreview.text }}"</div>
                <form @submit.prevent="submitComment">
                    <textarea v-model="commentForm.body" rows="4" class="w-full bg-slate-950 border-white/10 rounded-2xl p-5 text-slate-200 mb-6" placeholder="Твій коментар..."></textarea>
                    <div class="flex justify-end gap-4">
                        <button type="button" @click="isCommentModalOpen = false" class="text-slate-500 font-bold uppercase text-xs">Закрити</button>
                        <button type="submit" class="bg-white text-black px-10 py-4 rounded-2xl font-black uppercase text-xs">Надіслати</button>
                    </div>
                </form>
            </div>
        </div>

        <button v-if="showScrollTop" @click="scrollToTop" class="fixed bottom-32 right-12 bg-white/10 backdrop-blur-lg p-4 rounded-2xl border border-white/10 text-white z-40">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" /></svg>
        </button>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    posts: Array,
});

const isPostModalOpen = ref(false);
const isCommentModalOpen = ref(false);
const expandedComments = ref({});
const replyPreview = ref(null);

const postForm = useForm({
    title: '',
    content: '',
    source: 'Source 1',
});

const commentForm = useForm({
    body: '',
    post_id: null,
    parent_id: null,
});

const submitPost = () => {
    postForm.post(route('posts.store'), {
        onSuccess: () => {
            postForm.reset();
            isPostModalOpen.value = false;
        },
    });
};

const openCommentModal = (postId, comment = null) => {
    commentForm.reset();
    commentForm.post_id = postId;
    replyPreview.value = null;

    if (comment) {
        commentForm.parent_id = comment.parent_id ? comment.parent_id : comment.id;

        // Очищаємо текст від старого тегу [quote], якщо він там є
        const cleanBody = comment.body.includes('[/quote]') ? comment.body.split('[/quote]')[1].trim() : comment.body;
        const shortBody = cleanBody.length > 100 ? cleanBody.substring(0, 100) + '...' : cleanBody;

        replyPreview.value = {
            user: comment.user.name,
            text: shortBody
        };
    }

    isCommentModalOpen.value = true;
};

const submitComment = () => {
    let finalBody = commentForm.body;
    if (replyPreview.value) {
        finalBody = `[quote]${replyPreview.value.user}: ${replyPreview.value.text}[/quote] ${commentForm.body}`;
    }

    commentForm.transform((data) => ({
        ...data,
        body: finalBody,
    })).post(route('comments.store'), {
        onSuccess: () => {
            const pid = commentForm.post_id;
            const parentId = commentForm.parent_id;
            commentForm.reset();
            replyPreview.value = null;
            isCommentModalOpen.value = false;
            expandedComments.value[pid] = true;
            if (parentId) expandedComments.value['child_' + parentId] = true;
        },
    });
};

const toggle = (id) => {
    expandedComments.value[id] = !expandedComments.value[id];
};

const getRepliesCount = (post, commentId) => {
    return post.comments.filter(c => c.parent_id === commentId).length;
};

// Функція лайків
const toggleLike = (id, type) => {
    router.post(route('likes.toggle'), { id, type }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-white uppercase tracking-widest">Блог</h2>
                <button @click="isPostModalOpen = true" class="bg-indigo-600 text-white px-6 py-2 rounded-full hover:bg-indigo-700 transition shadow-lg font-bold">
                    + Новий запис
                </button>
            </div>
        </template>

        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div v-for="post in posts" :key="post.id" class="mb-8 bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                    <div class="p-8 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-3xl font-black text-gray-900 tracking-tight">{{ post.title }}</h3>
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold uppercase">{{ post.source }}</span>
                        </div>
                        <p class="text-gray-600 text-lg leading-relaxed">{{ post.content }}</p>
                    </div>

                    <div class="bg-gray-50 p-6 pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <div class="text-[10px] text-gray-400 font-bold uppercase flex gap-4">
                                <span>Автор: <span class="text-gray-700">{{ post.user.name }}</span></span>
                            </div>

                            <div class="flex gap-6 items-center">
                                <button @click="toggleLike(post.id, 'post')" class="flex items-center gap-2 group">
                                    <svg xmlns="http://www.w3.org/2000/svg" :class="post.is_liked ? 'fill-red-500 stroke-red-500' : 'fill-none stroke-gray-400 group-hover:stroke-red-400'" class="w-6 h-6 transition duration-300" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span :class="post.is_liked ? 'text-red-500' : 'text-gray-400'" class="font-black text-sm">{{ post.likes_count || 0 }}</span>
                                </button>

                                <button @click="toggle(post.id)" class="text-indigo-600 font-bold text-sm hover:underline">
                                    Коментарі ({{ post.comments_count || 0 }})
                                </button>
                                <button @click="openCommentModal(post.id)" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-black transition">
                                    Коментувати
                                </button>
                            </div>
                        </div>

                        <div v-if="expandedComments[post.id]" class="mt-4 space-y-4 pt-4 border-t border-gray-200">
                            <div v-for="comment in post.comments.filter(c => !c.parent_id)" :key="comment.id" class="space-y-3">
                                <div class="flex gap-3 items-start">
                                    <div class="bg-indigo-100 text-indigo-700 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0">{{ comment.user.name.charAt(0) }}</div>
                                    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex-1 relative">
                                        <div class="text-xs font-black text-gray-900 mb-1 uppercase">{{ comment.user.name }}</div>
                                        <div class="text-gray-600 text-sm leading-relaxed">{{ comment.body }}</div>

                                        <div class="flex gap-4 mt-3 items-center">
                                            <button @click="openCommentModal(post.id, comment)" class="text-[10px] text-indigo-500 font-black uppercase hover:text-indigo-700 transition">Відповісти</button>

                                            <button @click="toggleLike(comment.id, 'comment')" class="flex items-center gap-1 group">
                                                <span :class="comment.is_liked ? 'text-red-500' : 'text-gray-400'" class="text-[11px] font-black">
                                                    {{ comment.is_liked ? '❤' : '♡' }} {{ comment.likes_count || 0 }}
                                                </span>
                                            </button>

                                            <button v-if="getRepliesCount(post, comment.id) > 0" @click="toggle('child_' + comment.id)" class="text-[10px] text-gray-400 font-black uppercase hover:text-gray-600">
                                                {{ expandedComments['child_' + comment.id] ? 'Сховати' : 'Відповіді (' + getRepliesCount(post, comment.id) + ')' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="expandedComments['child_' + comment.id]" class="ml-12 space-y-3 border-l-2 border-indigo-100 pl-6">
                                    <div v-for="reply in post.comments.filter(c => c.parent_id === comment.id)" :key="reply.id" class="flex gap-2 items-start">
                                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex-1 shadow-inner relative overflow-hidden">
                                            <div v-if="reply.body.includes('[quote]')" class="mb-2 p-3 bg-white/70 border-l-4 border-indigo-400 rounded-r-lg text-[11px] text-gray-500 leading-snug shadow-sm">
                                                {{ reply.body.substring(reply.body.indexOf('[quote]') + 7, reply.body.indexOf('[/quote]')) }}
                                            </div>

                                            <div class="text-[10px] font-black text-gray-800 mb-1 uppercase">{{ reply.user.name }}</div>
                                            <div class="text-gray-600 text-sm leading-relaxed">
                                                {{ reply.body.includes('[/quote]') ? reply.body.split('[/quote]')[1] : reply.body }}
                                            </div>

                                            <div class="flex gap-4 mt-2 items-center">
                                                <button @click="openCommentModal(post.id, reply)" class="text-[9px] text-gray-400 font-bold uppercase hover:text-indigo-500">Відповісти</button>
                                                <button @click="toggleLike(reply.id, 'comment')" class="flex items-center gap-1 group">
                                                    <span :class="reply.is_liked ? 'text-red-500' : 'text-gray-400'" class="text-[10px] font-black">
                                                        {{ reply.is_liked ? '❤' : '♡' }} {{ reply.likes_count || 0 }}
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isCommentModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/90 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 relative">
                <button @click="isCommentModalOpen = false" class="absolute top-4 right-6 text-gray-400 hover:text-black text-3xl">&times;</button>
                <h3 class="text-xl font-black text-gray-900 mb-4 uppercase">{{ commentForm.parent_id ? 'Ваша відповідь' : 'Ваш коментар' }}</h3>

                <div v-if="replyPreview" class="mb-4 p-4 bg-gray-50 border-l-4 border-indigo-600 rounded-r-2xl text-xs shadow-inner">
                    <div class="font-bold text-indigo-700 mb-1">{{ replyPreview.user }} написав(ла):</div>
                    <div class="text-gray-500 italic leading-relaxed">"{{ replyPreview.text }}"</div>
                </div>

                <form @submit.prevent="submitComment">
                    <textarea v-model="commentForm.body" class="w-full rounded-2xl border-gray-200 p-4 focus:ring-indigo-500 mb-6" rows="4" placeholder="Напишіть щось..." required autofocus></textarea>
                    <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-2xl font-bold hover:bg-black transition uppercase text-xs tracking-widest shadow-lg">Відправити</button>
                </form>
            </div>
        </div>

        <div v-if="isPostModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/90 backdrop-blur-md p-4">
            <div class="bg-white rounded-3xl shadow-2xl max-w-xl w-full p-10 relative">
                <button @click="isPostModalOpen = false" class="absolute top-6 right-6 text-gray-400 hover:text-black text-4xl">&times;</button>
                <h3 class="text-3xl font-black text-gray-900 mb-8 uppercase tracking-tighter">Новий запис</h3>
                <form @submit.prevent="submitPost" class="space-y-6">
                    <input v-model="postForm.title" type="text" placeholder="Заголовок" class="w-full rounded-2xl border-gray-200 p-4 focus:ring-indigo-500" required>
                    <textarea v-model="postForm.content" placeholder="Контент..." class="w-full rounded-2xl border-gray-200 p-4 focus:ring-indigo-500" rows="4" required></textarea>
                    <select v-model="postForm.source" class="w-full rounded-2xl border-gray-200 p-4">
                        <option>Source 1</option>
                        <option>Source 2</option>
                        <option>Source 3</option>
                    </select>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black text-xl hover:bg-indigo-700 transition uppercase shadow-xl">Опублікувати</button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

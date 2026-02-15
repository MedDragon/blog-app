<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    posts: Array,
});

// Стан модальних вікон та розгорнутих коментарів
const isPostModalOpen = ref(false);
const isCommentModalOpen = ref(false);
const expandedComments = ref({}); // Об'єкт для відстеження розгорнутих постів

// Форми
const postForm = useForm({
    title: '',
    content: '',
    source: 'Source 1',
});

const commentForm = useForm({
    body: '',
    post_id: null,
});

// Логіка постів
const submitPost = () => {
    postForm.post(route('posts.store'), {
        onSuccess: () => {
            postForm.reset();
            isPostModalOpen.value = false;
        },
    });
};

// Логіка коментарів
const openCommentModal = (postId) => {
    commentForm.post_id = postId;
    isCommentModalOpen.value = true;
};

const submitComment = () => {
    commentForm.post(route('comments.store'), {
        onSuccess: () => {
            commentForm.reset('body');
            isCommentModalOpen.value = false;
            // Автоматично розгортаємо коментарі після додавання
            expandedComments.value[commentForm.post_id] = true;
        },
    });
};

// Перемикач видимості коментарів
const toggleComments = (postId) => {
    expandedComments.value[postId] = !expandedComments.value[postId];
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight text-white uppercase tracking-widest">Блог (Усі пости)</h2>
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
                            <h3 class="text-3xl font-black text-gray-900 leading-tight">{{ post.title }}</h3>
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold uppercase tracking-wider">
                                {{ post.source }}
                            </span>
                        </div>
                        <p class="text-gray-600 text-lg mb-6 leading-relaxed">{{ post.content }}</p>
                    </div>

                    <div class="bg-gray-50 p-6 pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <div class="text-sm">
                                <span class="text-gray-400">Автор:</span> <span class="font-bold text-gray-700">{{ post.user.name }}</span>
                                <span class="mx-3 text-gray-300">|</span>
                                <span class="text-gray-400">Zoho ID:</span> <code class="text-xs bg-gray-100 px-2 py-1 rounded text-indigo-500 font-bold">{{ post.external_id || 'ID_NULL' }}</code>
                            </div>

                            <div class="flex gap-4">
                                <button @click="toggleComments(post.id)" class="text-indigo-600 font-bold text-sm hover:underline">
                                    {{ expandedComments[post.id] ? 'Сховати коментарі' : 'Коментарі (' + (post.comments ? post.comments.length : 0) + ')' }}
                                </button>
                                <button @click="openCommentModal(post.id)" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-black transition">
                                    Додати коментар
                                </button>
                            </div>
                        </div>

                        <div v-if="expandedComments[post.id]" class="mt-4 space-y-3 pt-4 border-t border-gray-200">
                            <div v-for="comment in post.comments" :key="comment.id" class="flex gap-3 items-start">
                                <div class="bg-indigo-100 text-indigo-700 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ comment.user.name.charAt(0) }}
                                </div>
                                <div class="bg-white p-3 rounded-2xl border border-gray-200 shadow-sm flex-1">
                                    <div class="text-xs font-bold text-gray-900 mb-1">{{ comment.user.name }}</div>
                                    <div class="text-gray-600 text-sm">{{ comment.body }}</div>
                                </div>
                            </div>
                            <div v-if="!post.comments || post.comments.length === 0" class="text-center py-4 text-gray-400 text-sm italic">
                                Поки немає жодного коментаря...
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="posts.length === 0" class="text-center py-20 bg-white rounded-3xl shadow-inner border-2 border-dashed border-gray-200">
                    <p class="text-2xl font-bold text-gray-300 uppercase tracking-widest text-center">Контент відсутній</p>
                </div>
            </div>
        </div>

        <div v-if="isPostModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/80 backdrop-blur-md p-4">
            <div class="bg-white rounded-3xl shadow-2xl max-w-xl w-full p-10 relative">
                <button @click="isPostModalOpen = false" class="absolute top-6 right-6 text-gray-400 hover:text-black text-4xl">&times;</button>
                <h3 class="text-3xl font-black text-gray-900 mb-8 uppercase tracking-tighter">Нова публікація</h3>
                <form @submit.prevent="submitPost">
                    <div class="space-y-6">
                        <input v-model="postForm.title" type="text" placeholder="Заголовок" class="w-full rounded-2xl border-gray-200 p-4 focus:ring-indigo-500" required>
                        <textarea v-model="postForm.content" placeholder="Ваша історія..." class="w-full rounded-2xl border-gray-200 p-4 focus:ring-indigo-500" rows="4" required></textarea>
                        <select v-model="postForm.source" class="w-full rounded-2xl border-gray-200 p-4">
                            <option v-for="n in 5" :key="n">Source {{ n }}</option>
                        </select>
                        <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black text-xl hover:bg-indigo-700 transition shadow-indigo-200 shadow-xl uppercase tracking-widest">Опублікувати</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="isCommentModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/80 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 relative">
                <button @click="isCommentModalOpen = false" class="absolute top-4 right-6 text-gray-400 hover:text-black text-3xl">&times;</button>
                <h3 class="text-xl font-black text-gray-900 mb-6 uppercase tracking-wider">Ваш коментар</h3>
                <form @submit.prevent="submitComment">
                    <textarea v-model="commentForm.body" placeholder="Напишіть щось цікаве..." class="w-full rounded-2xl border-gray-200 p-4 focus:ring-indigo-500 mb-6" rows="4" required></textarea>
                    <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-2xl font-bold hover:bg-black transition shadow-lg uppercase">Відправити</button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

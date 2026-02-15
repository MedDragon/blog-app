<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';

// Отримуємо пости від бекенду
defineProps({
    posts: Array,
});

const form = useForm({
    title: '',
    content: '',
    source: 'Source 1', // Значення за замовчуванням
});

const submit = () => {
    form.post(route('posts.store'), {
        onSuccess: () => form.reset(), // Очистити форму після успіху
    });
};

const commentForm = useForm({
    body: '',
    post_id: null,
});

const submitComment = (postId) => {
    commentForm.post_id = postId;
    commentForm.post(route('comments.store'), {
        onSuccess: () => commentForm.reset('body'),
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Блог (Усі пости)</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4">Створити новий пост</h3>
                        <form @submit.prevent="submit">
                            <input v-model="form.title" type="text" placeholder="Заголовок" class="w-full mb-3 rounded border-gray-300" required>

                            <textarea v-model="form.content" placeholder="Що у вас нового?" class="w-full mb-3 rounded border-gray-300" rows="3" required></textarea>

                            <select v-model="form.source" class="w-full mb-4 rounded border-gray-300">
                                <option>Source 1</option>
                                <option>Source 2</option>
                                <option>Source 3</option>
                                <option>Source 4</option>
                                <option>Source 5</option>
                            </select>

                            <button type="submit" :disabled="form.processing" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Опублікувати
                            </button>
                        </form>
                    </div>
                </div>
                <div v-for="post in posts" :key="post.id" class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold">{{ post.title }}</h3>
                        <span class="text-sm text-gray-500">Джерело: {{ post.source }}</span>
                    </div>
                    <p class="text-gray-700 mb-4">{{ post.content }}</p>
                    <div class="border-t pt-4 text-sm text-gray-600 flex justify-between">
                        <span>Автор: <strong>{{ post.user.name }}</strong></span>
                        <span>Zoho ID: {{ post.external_id || 'Не синхронізовано' }}</span>
                    </div>
                    <div class="mt-6 border-l-4 border-gray-100 pl-4">
                        <div v-for="comment in post.comments" :key="comment.id" class="mb-3 text-sm">
                            <span class="font-bold">{{ comment.user.name }}:</span> {{ comment.body }}
                        </div>

                        <form @submit.prevent="submitComment(post.id)" class="mt-4 flex gap-2">
                            <input v-model="commentForm.body" type="text" placeholder="Напишіть коментар..." class="flex-1 rounded border-gray-300 text-sm">
                            <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded text-sm hover:bg-black">
                                Відправити
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

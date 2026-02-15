<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
// Отримуємо пости від бекенду
defineProps({
    posts: Array,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Блог (Усі пости)</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

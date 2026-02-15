<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    posts: Array,
});

// --- СТАН (Збережено з твого старого коду) ---
const isPostModalOpen = ref(false);
const isCommentModalOpen = ref(false);
const expandedComments = ref({});
const expandedPosts = ref({});
const replyPreview = ref(null);

// --- МЕТОДИ (Збережено повністю) ---
const togglePost = (id) => {
    expandedPosts.value[id] = !expandedPosts.value[id];
};

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

const toggleLike = (id, type) => {
    router.post(route('likes.toggle'), { id, type }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Стрічка" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-black text-3xl text-gray-900 uppercase tracking-tighter italic">Стрічка Prostir</h2>
        </template>

        <div class="py-12 bg-[#f8fafc] min-h-screen pb-32">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

                <div v-for="post in posts" :key="post.id" class="mb-10 bg-white shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-[2.5rem] overflow-hidden border border-white transition-all duration-500 hover:shadow-indigo-100 hover:border-indigo-50">

                    <div class="p-10 pb-6">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-3xl font-black text-gray-900 tracking-tight leading-[1.1]">{{ post.title }}</h3>
                            <span class="px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-2xl text-[10px] font-black uppercase tracking-widest shrink-0 ml-4 shadow-sm border border-indigo-100/50">
                                {{ post.source }}
                            </span>
                        </div>

                        <div class="text-gray-600 text-[1.05rem] leading-relaxed">
                            <p class="whitespace-pre-wrap">
                                {{ expandedPosts[post.id] || post.content.length <= 400
                                ? post.content
                                : post.content.substring(0, 400) + '...'
                                }}
                            </p>
                            <button v-if="post.content.length > 400" @click="togglePost(post.id)" class="mt-4 text-indigo-600 font-black uppercase text-[11px] tracking-[0.2em] flex items-center gap-2 hover:gap-3 transition-all duration-300">
                                <span>{{ expandedPosts[post.id] ? 'Згорнути' : 'Читати повністю' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform" :class="{'rotate-180': expandedPosts[post.id]}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="bg-[#fafbfd]/80 backdrop-blur-sm p-8 pt-6 border-t border-gray-50 flex flex-wrap justify-between items-center gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black overflow-hidden shadow-lg ring-4 ring-white">
                                <img v-if="post.user.avatar" :src="post.user.avatar" class="w-full h-full object-cover" />
                                <span v-else>{{ post.user.name.charAt(0) }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-gray-900 font-black text-sm uppercase tracking-tight">{{ post.user.name }}</span>
                                <span class="text-[10px] text-gray-400 font-bold italic" v-if="post.user.bio">{{ post.user.bio.substring(0, 40) }}...</span>
                            </div>
                        </div>

                        <div class="flex gap-3 items-center">
                            <button @click="toggleLike(post.id, 'post')" class="flex items-center gap-2.5 bg-white px-5 py-3 rounded-2xl shadow-sm border border-gray-100 transition hover:shadow-md active:scale-90 group">
                                <svg xmlns="http://www.w3.org/2000/svg" :class="post.is_liked ? 'fill-red-500 stroke-red-500 scale-110' : 'fill-none stroke-gray-400 group-hover:stroke-red-400'" class="w-5 h-5 transition-all duration-300" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span :class="post.is_liked ? 'text-red-500' : 'text-gray-500'" class="font-black text-xs">{{ post.likes_count || 0 }}</span>
                            </button>
                            <button @click="toggle(post.id)" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl shadow-lg font-black text-xs hover:bg-indigo-700 transition">
                                {{ post.comments_count || 0 }} Відгуків
                            </button>
                        </div>
                    </div>

                    <div v-if="expandedComments[post.id]" class="p-8 pt-0 bg-[#fafbfd]/80 border-t border-gray-50 animate-in">
                        <div class="pt-8 space-y-6">
                            <button @click="openCommentModal(post.id)" class="w-full py-4 rounded-2xl border-2 border-dashed border-gray-200 text-gray-400 font-bold text-xs uppercase hover:border-indigo-300 hover:text-indigo-500 transition">
                                + Написати свою думку
                            </button>

                            <div v-for="comment in post.comments.filter(c => !c.parent_id)" :key="comment.id" class="space-y-4">
                                <div class="flex gap-4 items-start">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-xs font-black shrink-0 overflow-hidden border-2 border-white">
                                        <img v-if="comment.user.avatar" :src="comment.user.avatar" class="w-full h-full object-cover" />
                                        <span v-else>{{ comment.user.name.charAt(0) }}</span>
                                    </div>
                                    <div class="bg-white p-5 rounded-[1.5rem] border border-gray-100 shadow-sm flex-1">
                                        <div class="text-[10px] font-black text-indigo-600 mb-2 uppercase">{{ comment.user.name }}</div>
                                        <div class="text-gray-700 text-sm">{{ comment.body }}</div>
                                        <div class="flex gap-5 mt-4 pt-3 border-t border-gray-50 items-center">
                                            <button @click="openCommentModal(post.id, comment)" class="text-[9px] text-gray-500 font-black uppercase hover:text-indigo-600">Відповісти</button>
                                            <button @click="toggleLike(comment.id, 'comment')" class="flex items-center gap-1">
                                                <span :class="comment.is_liked ? 'text-red-500' : 'text-gray-400'" class="text-[11px] font-black">
                                                    {{ comment.is_liked ? '❤' : '♡' }} {{ comment.likes_count || 0 }}
                                                </span>
                                            </button>
                                            <button v-if="getRepliesCount(post, comment.id) > 0" @click="toggle('child_' + comment.id)" class="ml-auto text-[9px] text-indigo-400 font-black uppercase">
                                                {{ expandedComments['child_' + comment.id] ? 'Сховати' : 'Гілка (' + getRepliesCount(post, comment.id) + ')' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="expandedComments['child_' + comment.id]" class="ml-14 space-y-4 border-l-2 border-indigo-50 pl-6">
                                    <div v-for="reply in post.comments.filter(c => c.parent_id === comment.id)" :key="reply.id" class="flex gap-3 items-start">
                                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-[9px] font-black shrink-0 overflow-hidden border">
                                            <img v-if="reply.user.avatar" :src="reply.user.avatar" class="w-full h-full object-cover" />
                                            <span v-else>{{ reply.user.name.charAt(0) }}</span>
                                        </div>
                                        <div class="bg-[#fcfdfe] p-4 rounded-2xl border border-gray-50 flex-1 shadow-sm">
                                            <div v-if="reply.body.includes('[quote]')" class="mb-3 p-3 bg-white/80 border-l-4 border-indigo-400 rounded-lg text-[10px] text-gray-400 italic">
                                                {{ reply.body.substring(reply.body.indexOf('[quote]') + 7, reply.body.indexOf('[/quote]')) }}
                                            </div>
                                            <div class="text-[9px] font-black text-gray-800 mb-1 uppercase">{{ reply.user.name }}</div>
                                            <div class="text-gray-600 text-sm">
                                                {{ reply.body.includes('[/quote]') ? reply.body.split('[/quote]')[1] : reply.body }}
                                            </div>
                                            <div class="flex gap-4 mt-3 pt-2 opacity-70">
                                                <button @click="openCommentModal(post.id, reply)" class="text-[8px] text-gray-400 font-black uppercase">Відповісти</button>
                                                <button @click="toggleLike(reply.id, 'comment')" class="text-[10px] font-black" :class="reply.is_liked ? 'text-red-500' : 'text-gray-400'">
                                                    {{ reply.is_liked ? '❤' : '♡' }} {{ reply.likes_count || 0 }}
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

        <button
            @click="isPostModalOpen = true"
            class="fixed bottom-10 right-10 z-50 bg-indigo-600 text-white px-8 h-16 rounded-full shadow-[0_20px_60px_rgba(79,70,229,0.4)] hover:bg-indigo-700 hover:scale-105 active:scale-95 transition-all duration-300 flex items-center gap-3 group"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 group-hover:rotate-90 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            <span class="font-black uppercase tracking-widest text-xs">Додати пост</span>
        </button>

        <div v-if="isPostModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white w-full max-w-2xl rounded-[2.5rem] p-10 shadow-2xl animate-in">
                <h3 class="text-2xl font-black uppercase mb-6 italic">Новий запис у Prostir</h3>
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

<style scoped>
.animate-in {
    animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

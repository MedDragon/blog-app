<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Налаштування профілю" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-2 h-8 bg-indigo-500 rounded-full shadow-[0_0_15px_rgba(99,102,241,0.6)]"></div>
                <h2 class="text-3xl font-black leading-tight text-white uppercase tracking-tighter">
                    Налаштування <span class="text-indigo-500 italic">Prostir</span>
                </h2>
            </div>
        </template>

        <div class="py-12 relative">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">

                <div class="bg-slate-900/40 backdrop-blur-2xl border border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.3)] sm:rounded-[2.5rem] p-6 sm:p-10 transition-all hover:border-white/20">
                    <div class="max-w-2xl">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                    </div>
                </div>

                <div class="bg-slate-900/40 backdrop-blur-2xl border border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.3)] sm:rounded-[2.5rem] p-6 sm:p-10 transition-all hover:border-white/20">
                    <div class="max-w-2xl">
                        <UpdatePasswordForm />
                    </div>
                </div>

                <div class="bg-rose-950/10 backdrop-blur-2xl border border-rose-500/10 shadow-[0_20px_50px_rgba(0,0,0,0.3)] sm:rounded-[2.5rem] p-6 sm:p-10 transition-all hover:border-rose-500/20">
                    <div class="max-w-2xl">
                        <DeleteUserForm />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Плавна анімація появи блоків по черзі */
.space-y-8 > div {
    animation: slideUp 0.6s ease-out forwards;
    opacity: 0;
}

.space-y-8 > div:nth-child(1) { animation-delay: 0.1s; }
.space-y-8 > div:nth-child(2) { animation-delay: 0.2s; }
.space-y-8 > div:nth-child(3) { animation-delay: 0.3s; }

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<template>
    <ForumLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Forums</h1>
            
            <div class="space-y-8">
                <div v-for="category in categories" :key="category.id" data-cy="category" class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200" :style="{ borderLeftColor: category.color, borderLeftWidth: '4px' }">
                        <h2 class="text-xl font-semibold text-gray-900">{{ category.name }}</h2>
                        <p v-if="category.description" class="text-sm text-gray-600 mt-1">{{ category.description }}</p>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        <div v-for="forum in category.forums" :key="forum.id" data-cy="forum-item" class="px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <Link :href="route('forums.show', forum.slug)" class="text-lg font-medium text-blue-600 hover:text-blue-500">
                                        {{ forum.name }}
                                    </Link>
                                    <p v-if="forum.description" class="text-sm text-gray-600 mt-1">{{ forum.description }}</p>
                                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                        <span data-cy="forum-threads-count">{{ forum.threads_count }} {{ forum.threads_count === 1 ? 'thread' : 'threads' }}</span>
                                        <span data-cy="forum-posts-count">{{ forum.posts_count }} {{ forum.posts_count === 1 ? 'post' : 'posts' }}</span>
                                    </div>
                                </div>
                                
                                <div v-if="forum.last_post" data-cy="last-post" class="ml-6 text-right text-sm">
                                    <div class="text-gray-900">
                                        <Link :href="route('threads.show', { forum: forum.slug, thread: forum.last_post.thread.slug })" class="hover:underline">
                                            {{ forum.last_post.thread.title }}
                                        </Link>
                                    </div>
                                    <div data-cy="last-post-user" class="text-gray-500 mt-1">
                                        by {{ forum.last_post.user.name }}
                                    </div>
                                    <div data-cy="last-post-time" class="text-gray-400 mt-1">
                                        {{ formatDate(forum.last_post.created_at) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ForumLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ForumLayout from '@/Layouts/ForumLayout.vue';

defineProps({
    categories: Array,
});

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`;
    
    return date.toLocaleDateString();
}
</script>
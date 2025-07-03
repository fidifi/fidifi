<template>
    <ForumLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav data-cy="breadcrumb" class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <Link :href="route('forums.index')" class="text-gray-500 hover:text-gray-700">Forums</Link>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-700 font-medium">{{ forum.name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Forum Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h1 data-cy="forum-title" class="text-2xl font-bold text-gray-900">{{ forum.name }}</h1>
                <p v-if="forum.description" class="text-gray-600 mt-2">{{ forum.description }}</p>
                
                <div class="mt-4">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        New Thread
                    </button>
                </div>
            </div>

            <!-- Threads List -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="divide-y divide-gray-200">
                    <div v-for="thread in threads.data" :key="thread.id" 
                         data-cy="thread-item"
                         class="px-6 py-4 hover:bg-gray-50 transition"
                         :class="{ 'bg-yellow-50': thread.is_pinned }">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span v-if="thread.is_pinned" class="text-yellow-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                        </svg>
                                    </span>
                                    <span v-if="thread.is_locked" class="text-gray-500">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 616 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    <Link :href="route('threads.show', { forum: forum.slug, thread: thread.slug })" 
                                          data-cy="thread-title"
                                          class="text-lg font-medium text-gray-900 hover:text-blue-600">
                                        {{ thread.title }}
                                    </Link>
                                </div>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                    <span data-cy="thread-author">by {{ thread.user.name }}</span>
                                    <span data-cy="thread-replies">{{ thread.posts_count }} {{ thread.posts_count === 1 ? 'reply' : 'replies' }}</span>
                                    <span>{{ thread.views_count }} {{ thread.views_count === 1 ? 'view' : 'views' }}</span>
                                </div>
                            </div>
                            
                            <div v-if="thread.last_post" class="ml-6 text-right text-sm">
                                <div class="text-gray-500">
                                    Last reply by {{ thread.last_post.user.name }}
                                </div>
                                <div class="text-gray-400 mt-1">
                                    {{ formatDate(thread.last_post.created_at) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="threads.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex gap-2">
                            <Link v-for="page in threads.last_page" 
                                  :key="page"
                                  :href="threads.path + '?page=' + page"
                                  :class="[
                                      'px-3 py-1 rounded',
                                      page === threads.current_page 
                                          ? 'bg-blue-600 text-white' 
                                          : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                  ]"
                                  preserve-scroll>
                                {{ page }}
                            </Link>
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
    forum: Object,
    threads: Object,
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
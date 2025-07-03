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
                            <Link :href="route('forums.show', forum.slug)" class="text-gray-500 hover:text-gray-700">
                                {{ forum.name }}
                            </Link>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-700 font-medium">{{ thread.title }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Thread Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 data-cy="thread-title" class="text-2xl font-bold text-gray-900">{{ thread.title }}</h1>
                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                            <span>Started by {{ thread.user.name }}</span>
                            <span>{{ formatDate(thread.created_at) }}</span>
                            <span>{{ thread.views_count }} views</span>
                            <span>{{ thread.posts_count }} replies</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span v-if="thread.is_locked" class="text-gray-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Posts -->
            <div class="space-y-4">
                <div v-for="post in posts.data" :key="post.id" data-cy="post-item" class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="flex">
                        <!-- User Info -->
                        <div class="w-48 bg-gray-50 p-4 border-r border-gray-200">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-300 rounded-full mx-auto mb-2"></div>
                                <div data-cy="post-author" class="font-medium text-gray-900">{{ post.user.name }}</div>
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ post.user.posts_count }} posts
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    Joined {{ formatDate(post.user.created_at) }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Post Content -->
                        <div class="flex-1 p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div data-cy="post-date" class="text-sm text-gray-500">
                                    Posted {{ formatDate(post.created_at) }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <span v-if="post.is_solution" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                        Solution
                                    </span>
                                    <span v-if="post.edited_at" class="text-xs text-gray-400">
                                        (edited)
                                    </span>
                                </div>
                            </div>
                            
                            <div data-cy="post-content" class="prose max-w-none">
                                {{ post.content }}
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <button class="text-gray-500 hover:text-gray-700 text-sm">
                                        Like ({{ post.likes_count }})
                                    </button>
                                    <button class="text-gray-500 hover:text-gray-700 text-sm">
                                        Quote
                                    </button>
                                    <button class="text-gray-500 hover:text-gray-700 text-sm">
                                        Reply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reply Box -->
            <div v-if="!thread.is_locked" class="bg-white rounded-lg shadow-sm p-6 mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reply to this thread</h3>
                <form>
                    <textarea 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        rows="6"
                        placeholder="Write your reply..."
                    ></textarea>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                            Post Reply
                        </button>
                    </div>
                </form>
            </div>
            
            <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-yellow-800">This thread is locked and cannot receive new replies.</span>
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
    thread: Object,
    posts: Object,
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
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
                            
                            <!-- Show quoted content if present -->
                            <div v-if="post.quoted_content" class="mb-4 p-3 bg-gray-100 rounded border-l-4 border-gray-400 text-sm">
                                <div class="font-semibold text-gray-700 mb-1">Quote:</div>
                                <div class="text-gray-600 italic">{{ post.quoted_content }}</div>
                            </div>
                            
                            <div data-cy="post-content" class="prose max-w-none">
                                {{ post.content }}
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <button 
                                        @click="toggleLike(post)" 
                                        :disabled="likingPosts.has(post.id)"
                                        :class="[
                                            'flex items-center gap-1 text-sm transition-colors cursor-pointer',
                                            post.is_liked_by_user 
                                                ? 'text-blue-600 hover:text-blue-700' 
                                                : 'text-gray-500 hover:text-gray-700',
                                            likingPosts.has(post.id) ? 'opacity-50 cursor-not-allowed' : ''
                                        ]"
                                        data-cy="like-button"
                                    >
                                        <svg 
                                            class="w-4 h-4" 
                                            :class="post.is_liked_by_user ? 'fill-current' : 'stroke-current'"
                                            :fill="post.is_liked_by_user ? 'currentColor' : 'none'"
                                            viewBox="0 0 24 24" 
                                            stroke-width="2"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3" />
                                        </svg>
                                        <span>{{ post.is_liked_by_user ? 'Liked' : 'Like' }} ({{ post.likes_count }})</span>
                                    </button>
                                    
                                    <button 
                                        @click="startReply(post, true)" 
                                        class="flex items-center gap-1 text-gray-500 hover:text-gray-700 text-sm transition-colors cursor-pointer"
                                        data-cy="quote-button"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <span>Quote</span>
                                    </button>
                                    
                                    <button 
                                        @click="startReply(post)" 
                                        class="flex items-center gap-1 text-gray-500 hover:text-gray-700 text-sm transition-colors cursor-pointer"
                                        data-cy="reply-button"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                        </svg>
                                        <span>Reply</span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Reply Form -->
                            <div v-if="replyingTo === post.id" class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">
                                    {{ quotedContent ? 'Quote & Reply' : 'Reply to this post' }}
                                </h4>
                                
                                <div v-if="quotedContent" class="mb-3 p-3 bg-gray-100 rounded border-l-4 border-gray-400 text-sm text-gray-700">
                                    <strong>{{ post.user.name }} wrote:</strong><br>
                                    {{ quotedContent }}
                                </div>
                                
                                <textarea 
                                    v-model="replyContent"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    rows="4"
                                    placeholder="Write your reply..."
                                    data-cy="reply-textarea"
                                ></textarea>
                                
                                <div class="mt-3 flex items-center gap-2">
                                    <button 
                                        type="button"
                                        @click="submitReply(post)"
                                        :disabled="!replyContent.trim()"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        data-cy="submit-reply-button"
                                    >
                                        Post Reply
                                    </button>
                                    <button 
                                        type="button"
                                        @click="cancelReply"
                                        class="text-gray-600 hover:text-gray-700 px-4 py-2 rounded-md transition"
                                        data-cy="cancel-reply-button"
                                    >
                                        Cancel
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
                <textarea 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="6"
                    placeholder="Write your reply..."
                ></textarea>
                <div class="mt-4">
                    <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        Post Reply
                    </button>
                </div>
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
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import ForumLayout from '@/Layouts/ForumLayout.vue';

const props = defineProps({
    forum: Object,
    thread: Object,
    posts: Object,
});

const likingPosts = ref(new Set());
const replyingTo = ref(null);
const replyContent = ref('');
const quotedContent = ref('');

// Watch for changes in reply content
watch(replyContent, (newValue) => {
    console.log('Reply content changed:', newValue);
});

async function toggleLike(post) {
    if (likingPosts.value.has(post.id)) return;
    
    likingPosts.value.add(post.id);
    
    try {
        const response = await fetch(`/posts/${post.id}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });
        
        if (response.ok) {
            const data = await response.json();
            // Update the post's like count and liked status
            post.likes_count = data.likes_count;
            post.is_liked_by_user = data.liked;
        }
    } catch (error) {
        console.error('Error toggling like:', error);
    } finally {
        likingPosts.value.delete(post.id);
    }
}

function startReply(post, isQuote = false) {
    replyingTo.value = post.id;
    if (isQuote) {
        quotedContent.value = post.content;
        replyContent.value = ''; // User should type their own reply
    } else {
        quotedContent.value = '';
        replyContent.value = '';
    }
}

function cancelReply() {
    replyingTo.value = null;
    replyContent.value = '';
    quotedContent.value = '';
}

function submitReply(post) {
    if (!replyContent.value.trim()) {
        console.log('Reply content is empty, not submitting');
        return;
    }
    
    console.log('Submitting reply to post:', post.id);
    console.log('Reply content:', replyContent.value);
    console.log('Quoted content:', quotedContent.value);
    
    router.post(`/posts/${post.id}/reply`, {
        content: replyContent.value,
        quoted_content: quotedContent.value || null,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Reply submitted successfully');
            // Reset form and close reply box
            cancelReply();
        },
        onError: (errors) => {
            console.error('Error submitting reply:', errors);
        }
    });
}

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
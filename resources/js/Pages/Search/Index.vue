<template>
    <ForumLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Search</h1>
            
            <!-- Search Form -->
            <form @submit.prevent="search" class="mb-8">
                <div class="flex gap-4">
                    <input
                        v-model="form.q"
                        type="text"
                        placeholder="Search forums..."
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                    
                    <select
                        v-model="form.type"
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">All</option>
                        <option value="threads">Threads</option>
                        <option value="posts">Posts</option>
                        <option value="users">Users</option>
                    </select>
                    
                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition"
                    >
                        Search
                    </button>
                </div>
            </form>
            
            <!-- Results -->
            <div v-if="results">
                <!-- Threads Results -->
                <div v-if="results.threads && results.threads.length > 0" class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Threads</h2>
                    <div class="bg-white rounded-lg shadow-sm divide-y divide-gray-200">
                        <div v-for="thread in (results.threads.data || results.threads)" :key="`thread-${thread.id}`" class="p-4 hover:bg-gray-50">
                            <Link :href="route('threads.show', { forum: thread.forum.slug, thread: thread.slug })" class="block">
                                <h3 class="text-lg font-medium text-blue-600 hover:text-blue-500">{{ thread.title }}</h3>
                                <div class="mt-1 text-sm text-gray-500">
                                    in {{ thread.forum.name }} by {{ thread.user.name }}
                                </div>
                            </Link>
                        </div>
                    </div>
                    
                    <!-- Pagination for threads -->
                    <div v-if="results.threads.data && results.threads.last_page > 1" class="mt-4">
                        <div class="flex gap-2">
                            <Link
                                v-for="page in results.threads.last_page"
                                :key="page"
                                :href="`/search?q=${query}&type=threads&page=${page}`"
                                :class="[
                                    'px-3 py-1 rounded',
                                    page === results.threads.current_page 
                                        ? 'bg-blue-600 text-white' 
                                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                                preserve-scroll
                            >
                                {{ page }}
                            </Link>
                        </div>
                    </div>
                </div>
                
                <!-- Posts Results -->
                <div v-if="results.posts && results.posts.length > 0" class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Posts</h2>
                    <div class="bg-white rounded-lg shadow-sm divide-y divide-gray-200">
                        <div v-for="post in (results.posts.data || results.posts)" :key="`post-${post.id}`" class="p-4 hover:bg-gray-50">
                            <Link :href="route('threads.show', { forum: post.thread.forum.slug, thread: post.thread.slug })" class="block">
                                <h3 class="text-lg font-medium text-blue-600 hover:text-blue-500">{{ post.thread.title }}</h3>
                                <p class="mt-1 text-gray-600 line-clamp-2">{{ post.content }}</p>
                                <div class="mt-1 text-sm text-gray-500">
                                    by {{ post.user.name }} in {{ post.thread.forum.name }}
                                </div>
                            </Link>
                        </div>
                    </div>
                    
                    <!-- Pagination for posts -->
                    <div v-if="results.posts.data && results.posts.last_page > 1" class="mt-4">
                        <div class="flex gap-2">
                            <Link
                                v-for="page in results.posts.last_page"
                                :key="page"
                                :href="`/search?q=${query}&type=posts&page=${page}`"
                                :class="[
                                    'px-3 py-1 rounded',
                                    page === results.posts.current_page 
                                        ? 'bg-blue-600 text-white' 
                                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                                preserve-scroll
                            >
                                {{ page }}
                            </Link>
                        </div>
                    </div>
                </div>
                
                <!-- Users Results -->
                <div v-if="results.users && results.users.length > 0" class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Users</h2>
                    <div class="bg-white rounded-lg shadow-sm divide-y divide-gray-200">
                        <div v-for="user in (results.users.data || results.users)" :key="`user-${user.id}`" class="p-4 hover:bg-gray-50 flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-300 rounded-full"></div>
                            <div>
                                <div class="font-medium text-gray-900">{{ user.name }}</div>
                                <div class="text-sm text-gray-500">@{{ user.username }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pagination for users -->
                    <div v-if="results.users.data && results.users.last_page > 1" class="mt-4">
                        <div class="flex gap-2">
                            <Link
                                v-for="page in results.users.last_page"
                                :key="page"
                                :href="`/search?q=${query}&type=users&page=${page}`"
                                :class="[
                                    'px-3 py-1 rounded',
                                    page === results.users.current_page 
                                        ? 'bg-blue-600 text-white' 
                                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                                preserve-scroll
                            >
                                {{ page }}
                            </Link>
                        </div>
                    </div>
                </div>
                
                <!-- No Results -->
                <div v-if="(!results.threads || results.threads.length === 0) && 
                         (!results.posts || results.posts.length === 0) && 
                         (!results.users || results.users.length === 0)" 
                     class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500">
                    No results found for "{{ query }}"
                </div>
            </div>
        </div>
    </ForumLayout>
</template>

<script setup>
import { Link, router, useForm } from '@inertiajs/vue3';
import ForumLayout from '@/Layouts/ForumLayout.vue';

const props = defineProps({
    query: String,
    type: String,
    results: Object,
});

const form = useForm({
    q: props.query || '',
    type: props.type || '',
});

const search = () => {
    router.get(route('search'), {
        q: form.q,
        type: form.type,
    });
};
</script>
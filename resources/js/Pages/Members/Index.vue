<template>
    <ForumLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Members</h1>
            
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-2xl font-bold text-blue-600">{{ stats.total_members }}</div>
                    <div class="text-gray-600">Total Members</div>
                </div>
                
                <div v-if="stats.newest_member" class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-lg font-semibold text-gray-900">{{ stats.newest_member.name }}</div>
                    <div class="text-gray-600">Newest Member</div>
                </div>
                
                <div v-if="stats.most_posts" class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-lg font-semibold text-gray-900">{{ stats.most_posts.name }}</div>
                    <div class="text-gray-600">Most Posts ({{ stats.most_posts.posts_count }})</div>
                </div>
            </div>
            
            <!-- Search and Sort Controls -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form @submit.prevent="search" class="flex gap-4">
                    <input
                        v-model="form.search"
                        type="text"
                        placeholder="Search members..."
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                    
                    <select
                        v-model="form.sort"
                        @change="search"
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="joined">Sort by Join Date</option>
                        <option value="posts">Sort by Post Count</option>
                    </select>
                    
                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition"
                    >
                        Search
                    </button>
                </form>
            </div>
            
            <!-- Members List -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div v-if="members.data.length > 0" class="divide-y divide-gray-200">
                    <div v-for="member in members.data" :key="member.id" class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-4">
                            <!-- Avatar -->
                            <div class="w-16 h-16 bg-gray-300 rounded-full flex-shrink-0"></div>
                            
                            <!-- Member Info -->
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ member.name }}</h3>
                                <p class="text-gray-600">@{{ member.username }}</p>
                                
                                <div class="flex items-center gap-6 mt-2 text-sm text-gray-500">
                                    <span>{{ member.posts_count }} posts</span>
                                    <span>Joined {{ formatDate(member.created_at) }}</span>
                                </div>
                            </div>
                            
                            <!-- Member Stats -->
                            <div class="text-right text-sm text-gray-500">
                                <div class="font-medium text-gray-900">{{ member.posts_count }}</div>
                                <div>Posts</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- No Results -->
                <div v-else class="p-8 text-center text-gray-500">
                    <div v-if="search">
                        No members found matching "{{ search }}"
                    </div>
                    <div v-else>
                        No members found.
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            <div v-if="members.last_page > 1" class="mt-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing {{ members.from }} to {{ members.to }} of {{ members.total }} results
                    </div>
                    
                    <div class="flex gap-2">
                        <Link
                            v-for="page in members.last_page"
                            :key="page"
                            :href="buildPageUrl(page)"
                            :class="[
                                'px-3 py-1 rounded',
                                page === members.current_page 
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
        </div>
    </ForumLayout>
</template>

<script setup>
import { Link, router, useForm } from '@inertiajs/vue3';
import ForumLayout from '@/Layouts/ForumLayout.vue';

const props = defineProps({
    members: Object,
    search: String,
    sort: String,
    stats: Object,
});

const form = useForm({
    search: props.search || '',
    sort: props.sort || 'joined',
});

const search = () => {
    router.get(route('members.index'), {
        search: form.search,
        sort: form.sort,
    });
};

const buildPageUrl = (page) => {
    const params = new URLSearchParams();
    if (form.search) params.append('search', form.search);
    if (form.sort) params.append('sort', form.sort);
    params.append('page', page);
    
    return `/members?${params.toString()}`;
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));
    
    if (diffInDays === 0) return 'today';
    if (diffInDays === 1) return 'yesterday';
    if (diffInDays < 30) return `${diffInDays} days ago`;
    if (diffInDays < 365) return `${Math.floor(diffInDays / 30)} months ago`;
    
    return `${Math.floor(diffInDays / 365)} years ago`;
};
</script>
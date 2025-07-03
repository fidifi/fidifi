<template>
    <ForumLayout>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav data-cy="breadcrumb" class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <Link :href="route('forums.index')" class="text-gray-500 hover:text-gray-700">Forums</Link>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            <Link :href="route('forums.show', forum.slug)" class="text-gray-500 hover:text-gray-700">
                                {{ forum.name }}
                            </Link>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-700 font-medium">Create Thread</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Create New Thread</h1>
                <p class="mt-2 text-gray-600">Start a new discussion in {{ forum.name }}</p>
            </div>

            <!-- Create Thread Form -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <form @submit.prevent="submit">
                    <!-- Title Field -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Thread Title
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            placeholder="Enter a descriptive title for your thread"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.title }"
                            required
                        />
                        <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">
                            {{ form.errors.title }}
                        </div>
                    </div>

                    <!-- Content Field -->
                    <div class="mb-6">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                            Thread Content
                        </label>
                        <textarea
                            id="content"
                            v-model="form.content"
                            rows="12"
                            placeholder="Write your thread content here..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.content }"
                            required
                        ></textarea>
                        <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">
                            {{ form.errors.content }}
                        </div>
                        <p class="text-gray-500 text-xs mt-1">
                            Minimum 10 characters required
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between">
                        <Link 
                            :href="route('forums.show', forum.slug)" 
                            class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition"
                        >
                            Cancel
                        </Link>
                        
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition disabled:opacity-50"
                        >
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Thread</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </ForumLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import ForumLayout from '@/Layouts/ForumLayout.vue';

const props = defineProps({
    forum: Object,
});

const form = useForm({
    title: '',
    content: '',
});

const submit = () => {
    form.post(route('threads.store', props.forum.slug), {
        onSuccess: () => {
            // Form will redirect on success
        },
    });
};
</script>
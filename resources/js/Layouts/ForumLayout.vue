<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <Link :href="route('forums.index')" class="flex items-center">
                            <span class="text-2xl font-bold text-blue-600">FidiFi</span>
                            <span class="text-2xl font-light text-gray-600 ml-1">Forum</span>
                        </Link>
                        
                        <div class="hidden sm:ml-8 sm:flex sm:space-x-6">
                            <Link :href="route('forums.index')" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                                Forums
                            </Link>
                            <Link :href="route('members.index')" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                                Members
                            </Link>
                            <Link :href="route('search')" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                                Search
                            </Link>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div v-if="$page.props.auth?.user" class="flex items-center space-x-4">
                            <button class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </button>
                            
                            <div class="relative">
                                <button @click="showDropdown = !showDropdown" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <div class="w-8 h-8 bg-gray-400 rounded-full"></div>
                                    <span class="ml-2 text-gray-700">{{ $page.props.auth.user.name }}</span>
                                </button>
                                
                                <div v-if="showDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <Link :href="route('dashboard')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Dashboard
                                    </Link>
                                    <Link :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </Link>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else class="flex items-center space-x-2">
                            <Link :href="route('login')" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                                Login
                            </Link>
                            <Link :href="route('register')" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium transition">
                                Register
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center text-sm text-gray-500">
                    <p>&copy; 2025 FidiFi Forum. Built with Laravel and Vue.js.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showDropdown = ref(false);
</script>
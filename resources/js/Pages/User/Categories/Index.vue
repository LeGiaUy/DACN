<template>
    <UserLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Danh mục sản phẩm</h1>
                <p class="text-gray-600 mt-2">Khám phá các danh mục sản phẩm của chúng tôi</p>
            </div>

            <!-- Categories Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <Link v-for="category in categories" :key="category.id" 
                      :href="route('user.categories.show', category.id)"
                      class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 group">
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-blue-100 rounded-full mx-auto mb-4 flex items-center justify-center group-hover:bg-blue-200 transition duration-300">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition duration-300">{{ category.name }}</h3>
                        <p class="text-gray-600 mb-4">{{ category.description || 'Khám phá sản phẩm trong danh mục này' }}</p>
                        <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span>{{ category.products_count }} sản phẩm</span>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Empty State -->
            <div v-if="categories.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Không có danh mục nào</h3>
                <p class="mt-1 text-sm text-gray-500">Chưa có danh mục sản phẩm nào được tạo.</p>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'

defineProps({
    categories: Array
})
</script>

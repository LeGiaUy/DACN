<template>
    <UserLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Thương hiệu</h1>
                <p class="text-gray-600 mt-2">Khám phá các thương hiệu uy tín của chúng tôi</p>
            </div>

            <!-- Brands Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <Link v-for="brand in brands" :key="brand.id" 
                      :href="route('user.brands.show', brand.id)"
                      class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 group">
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center group-hover:bg-gray-200 transition duration-300">
                            <span class="text-2xl font-bold text-gray-700">{{ brand.name.charAt(0) }}</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition duration-300">{{ brand.name }}</h3>
                        <p class="text-gray-600 mb-4">{{ brand.description || 'Khám phá sản phẩm từ thương hiệu này' }}</p>
                        <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span>{{ brand.products_count }} sản phẩm</span>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Empty State -->
            <div v-if="brands.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Không có thương hiệu nào</h3>
                <p class="mt-1 text-sm text-gray-500">Chưa có thương hiệu nào được tạo.</p>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'

defineProps({
    brands: Array
})
</script>

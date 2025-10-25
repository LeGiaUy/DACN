<template>
    <UserLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <Link :href="route('user.home')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Trang chủ
                        </Link>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <Link :href="route('user.brands.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Thương hiệu</Link>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ brand.name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Brand Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-gray-700">{{ brand.name.charAt(0) }}</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ brand.name }}</h1>
                        <p class="text-gray-600 mt-2">{{ brand.description || 'Khám phá sản phẩm từ thương hiệu này' }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <div class="lg:w-1/4">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold mb-4">Bộ lọc</h3>
                        
                        <!-- Search -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                            <input type="text" 
                                   v-model="filters.search"
                                   @keyup.enter="applyFilters"
                                   placeholder="Nhập tên sản phẩm..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                            <select v-model="filters.category" @change="applyFilters" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Tất cả danh mục</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng giá</label>
                            <div class="space-y-2">
                                <input type="number" 
                                       v-model="filters.min_price"
                                       @change="applyFilters"
                                       placeholder="Giá tối thiểu"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <input type="number" 
                                       v-model="filters.max_price"
                                       @change="applyFilters"
                                       placeholder="Giá tối đa"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp</label>
                            <select v-model="filters.sort" @change="applyFilters" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="created_at">Mới nhất</option>
                                <option value="name">Tên A-Z</option>
                                <option value="price">Giá thấp đến cao</option>
                                <option value="price_desc">Giá cao đến thấp</option>
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        <button @click="clearFilters" class="w-full bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-300">
                            Xóa bộ lọc
                        </button>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="lg:w-3/4">
                    <!-- Results Count -->
                    <div class="mb-6">
                        <p class="text-gray-600">Hiển thị {{ products.data.length }} trong {{ products.total }} sản phẩm</p>
                    </div>

                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div v-for="product in products.data" :key="product.id" 
                             class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                            <div class="aspect-w-16 aspect-h-9">
                                <img :src="product.img_url || '/images/placeholder.jpg'" 
                                     :alt="product.name"
                                     class="w-full h-48 object-cover">
                            </div>
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ product.name }}</h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ product.description }}</p>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-2xl font-bold text-blue-600">{{ formatPrice(product.price) }}</span>
                                    <span class="text-sm text-gray-500">{{ product.category?.name }}</span>
                                </div>
                                <Link :href="route('user.products.show', product.id)" 
                                      class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 text-center block">
                                    Xem chi tiết
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- No Products -->
                    <div v-if="products.data.length === 0" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Không tìm thấy sản phẩm</h3>
                        <p class="mt-1 text-sm text-gray-500">Thử thay đổi bộ lọc để tìm sản phẩm khác.</p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.links" class="mt-8">
                        <nav class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link v-if="products.prev_page_url" :href="products.prev_page_url" 
                                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Trước
                                </Link>
                                <Link v-if="products.next_page_url" :href="products.next_page_url" 
                                      class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Sau
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Hiển thị {{ products.from }} đến {{ products.to }} trong {{ products.total }} kết quả
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link v-for="link in products.links" :key="link.label" :href="link.url" 
                                              v-html="link.label"
                                              :class="[
                                                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                  link.active 
                                                      ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                                                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                  link.url === null ? 'opacity-50 cursor-not-allowed' : ''
                                              ]">
                                        </Link>
                                    </nav>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'

const props = defineProps({
    brand: Object,
    products: Object,
    categories: Array,
    filters: Object
})

const filters = ref({ ...props.filters })

const applyFilters = () => {
    router.get(route('user.brands.show', props.brand.id), filters.value, {
        preserveState: true,
        replace: true
    })
}

const clearFilters = () => {
    filters.value = {}
    applyFilters()
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price)
}
</script>
